<?php declare(strict_types=1);

namespace Tests;

use App\Mail;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

class MailTest extends TestCase
{
    /** @var string default FROM email address */
    private $email;
    /** @var string default FROM name */
    private $name;
    /** @var string subject line */
    private $subject_line;
    /** @var string HTML message content */
    private $html_message;
    /** @var string plain text message content */
    private $plain_message;

    /**
     * Called before _every_ test.
     */
    public function setUp()
    {
        $random_int = rand(0, 9999);
        $this->email = 'test' . $random_int . '@example.com';
        $this->name = 'Mail Test ' . $random_int;
        $this->subject_line = 'This is a test subject' . $random_int;
        $this->html_message = sprintf("<h1>TITLE</h1> <p>Message %d.</p>", $random_int);
        $this->plain_message = sprintf("TITLE Message %d.", $random_int);

        putenv('MAIL_FROM_EMAIL=' . $this->email);
        putenv('MAIL_FROM_NAME=' . $this->name);
        putenv('MAIL_DRIVER=mail');
    }

    /**
     * @expectedException Exception
     */
    public function testFromAddressNeeded()
    {
        putenv('MAIL_FROM_EMAIL=');
        new Mail;
    }

    public function testConstructorSetThrowExceptionsOff()
    {
        putenv('MAIL_FROM_EMAIL=');
        $this->assertInstanceOf(Mail::class, new Mail(false));
    }

    public function testClassInitialisation()
    {
        $mail = new Mail;
        $this->assertInstanceOf(Mail::class, $mail);
        $this->assertInstanceOf(PHPMailer::class, $mail);
    }

    public function testConstructorDefaultDriver()
    {
        $this->assertSame('mail', (new Mail)->Mailer);
    }

    public function testConstructorOverrideDriverToEmptyString()
    {
        putenv('MAIL_DRIVER');
        $this->assertSame('mail', (new Mail)->Mailer);
    }

    public function testConstructorOverrideDriverToSendmail()
    {
        putenv('MAIL_DRIVER=sendmail');
        $this->assertSame('sendmail', (new Mail)->Mailer);
    }

    public function testConstructorOverrideDriverToSMTP()
    {
        putenv('MAIL_DRIVER=smtp');
        $this->assertSame('smtp', (new Mail)->Mailer);
    }

    /**
     * @expectedException Exception
     */
    public function testConstructorOverrideDriverWithUnsupportedDriver()
    {
        putenv('MAIL_DRIVER=foobar');
        new Mail;
    }

    public function testConstructorDefaultContentType()
    {
        $this->assertSame('text/plain', (new Mail)->ContentType);
    }

    public function testConstructorDefaultFromAddressAndReplyTo()
    {
        $mail = new Mail;
        $this->assertSame($this->email, $mail->From);
        $this->assertSame($this->name, $mail->FromName);

        $reply_to = $mail->getReplyToAddresses();
        $this->assertTrue(in_array($this->email, array_values($reply_to[$this->email])));
        $this->assertTrue(in_array($this->name, array_values($reply_to[$this->email])));
    }

    /**
     * @expectedException TypeError
     */
    public function testAddToWithObject()
    {
        (new Mail)->addTo(new stdClass);
    }

    /**
     * @expectedException TypeError
     */
    public function testAddToWithBoolean()
    {
        (new Mail)->addTo(true);
    }

    /**
     * @expectedException TypeError
     */
    public function testAddToWithNull()
    {
        (new Mail)->addTo(null);
    }

    /**
     * @expectedException TypeError
     */
    public function testAddToWithInt()
    {
        (new Mail)->addTo(555);
    }

    public function testAddTo()
    {
        $mail = new Mail;
        $this->assertEmpty($mail->getToAddresses());
        $mail->addTo($this->email, $this->name);
        $this->assertTrue(in_array($this->email, array_values($mail->getToAddresses()[0])));
        $this->assertTrue(in_array($this->name, array_values($mail->getToAddresses()[0])));
        $another_email = 'another.' . $this->email;
        $mail->addTo($another_email);
        $this->assertTrue(in_array($this->email, array_values($mail->getToAddresses()[0])));
        $this->assertTrue(in_array($this->name, array_values($mail->getToAddresses()[0])));
        $this->assertTrue(in_array($another_email, array_values($mail->getToAddresses()[1])));
        $no_name = ''; // This address has no name
        $this->assertTrue(in_array($no_name, array_values($mail->getToAddresses()[1])));
    }

    /**
     * @expectedException TypeError
     */
    public function testSetSubjectWithObject()
    {
        (new Mail)->setSubject(new stdClass);
    }

    /**
     * @expectedException TypeError
     */
    public function testSetSubjectWithFalse()
    {
        (new Mail)->setSubject(false);
    }

    /**
     * @expectedException TypeError
     */
    public function testSetSubjectWithInt()
    {
        (new Mail)->setSubject(123);
    }

    /**
     * @expectedException TypeError
     */
    public function testSetSubjectWithNull()
    {
        (new Mail)->setSubject(null);
    }

    public function testSetSubject()
    {
        $mail = new Mail;
        $this->assertEmpty($mail->Subject);
        $mail->setSubject($this->subject_line);
        $this->assertSame($mail->Subject, $this->subject_line);
    }

    /**
     * @expectedException TypeError
     */
    public function testHtmlMessageWithObject()
    {
        $mail = new Mail;
        $mail->setHtmlMessage(new stdClass);
    }

    /**
     * @expectedException TypeError
     */
    public function testHtmlMessageWithBoolean()
    {
        $mail = new Mail;
        $mail->setHtmlMessage(false);
    }

    /**
     * @expectedException TypeError
     */
    public function testHtmlMessageWithInt()
    {
        $mail = new Mail;
        $mail->setHtmlMessage(321);
    }

    /**
     * @expectedException TypeError
     */
    public function testHtmlMessageWithNull()
    {
        $mail = new Mail;
        $mail->setHtmlMessage(null);
    }

    public function testHtmlMessage()
    {
        $mail = new Mail;
        $this->assertEmpty($mail->Body);
        $this->assertEmpty($mail->AltBody);
        $mail->setHtmlMessage($this->html_message);
        $this->assertSame($mail->Body, $this->html_message);
        $this->assertSame($mail->AltBody, $this->plain_message);
        $this->assertSame('text/html', $mail->ContentType);
    }

    public function testHtmlMessageOverridePlainText()
    {
        $mail = new Mail;
        $mail->setHtmlMessage($this->html_message);
        $message = "Plain text rocks!";
        $mail->setPlainTextMessage($message);
        $this->assertSame($mail->Body, $this->html_message);
        $this->assertSame($mail->AltBody, $message);
    }

    /**
     * @expectedException TypeError
     */
    public function testPlainTextMessageWithObject()
    {
        $mail = new Mail;
        $mail->setPlainTextMessage(new stdClass);
    }

    /**
     * @expectedException TypeError
     */
    public function testPlainTextMessageWithBool()
    {
        $mail = new Mail;
        $mail->setPlainTextMessage(true);
    }

    /**
     * @expectedException TypeError
     */
    public function testPlainTextMessageWithNull()
    {
        $mail = new Mail;
        $mail->setPlainTextMessage(null);
    }

    /**
     * @expectedException TypeError
     */
    public function testPlainTextMessageWithInt()
    {
        $mail = new Mail;
        $mail->setPlainTextMessage(999);
    }

    public function testPlainTextMessage()
    {
        $mail = new Mail;
        $mail->setPlainTextMessage($this->plain_message);
        $this->assertSame($mail->Body, $this->plain_message);
        $this->assertSame('text/plain', $mail->ContentType);
    }

    /**
     * @expectedException Exception
     */
    public function testSendFailsWithNoFrom()
    {
        $mail = new Mail;
        $mail->addTo($this->email);
        $mail->From = null;
        $mail->setSubject("No FROM address!");
        $mail->setPlainTextMessage("Help! No FROM address!");
        $mail->send();
    }

    /**
     * @expectedException Exception
     */
    public function testSendFailsWithNoSubject()
    {
        $mail = new Mail;
        $mail->addTo($this->email);
        $mail->setPlainTextMessage("Help! No subject!");
        $mail->send();
    }

    /**
     * @expectedException Exception
     */
    public function testSendFailsWithBadSubjectLine()
    {
        $mail = new Mail;
        $mail->addTo($this->email);
        $mail->setSubject(' ');
        $mail->setPlainTextMessage('hey!');
        $mail->send();
    }

    /**
     * @expectedException Exception
     */
    public function testSendFailsWithNoPlainTextMessage()
    {
        $mail = new Mail;
        $mail->addTo($this->email);
        $mail->setPlainTextMessage('     ');
        $mail->send();
    }

    /**
     * @expectedException Exception
     */
    public function testSendFailsWhenHtmlMessageDoesNotHaveBody()
    {
        $mail = new Mail;
        $mail->addTo($this->email);
        $mail->setSubject('no HTML body');
        $mail->setHtmlMessage('   ');
        $mail->send();
    }

    /**
     * @expectedException Exception
     */
    public function testSendFailsWhenBadEmailGiven()
    {
        $mail = new Mail;
        $mail->addTo('foo');
        $mail->setSubject('bad TO address');
        $mail->setPlainTextMessage('hey!');
        $mail->send();
    }

    /**
     * @skip - unfinished test
     */
    public function testSend()
    {
        // Mock the SMTP class so we can test a successful call to send()
        putenv('MAIL_DRIVER=smtp');
        $mail = new Mail;
        $mail->addTo($this->email);
        $mail->setSubject('Success!');
        $mail->setPlainTextMessage('Test message');

        /* $smtp = $this->getMockBuilder(SMTP::class)
            ->disableOriginalConstructor()
            ->getMock();

        $smtp->expects($this->once())
            ->method('connect')
            ->willReturn(true);

        $smtp->expects($this->once())
            ->method('sendCommand')
            ->willReturn(true);

        $mail->setSMTPInstance($smtp);

        $mail->send(); */
    }
}
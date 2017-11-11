<?php

/**
 * A wrapper class around PHPMailer, with some sensible defaults to make it easier to use.
 *
 * @author Duggie
 * @since  2017-11-09
 */

namespace App;

use App\Traits\MailTrait;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

final class Mail extends PHPMailer
{
    use MailTrait;

    /**
     * @var int SMTP debug flag: 0 = off
     */
    const SMTP_DEBUG = 0;

    /**
     * @var string default driver for sending emails
     */
    const DRIVER = 'mail';

    /**
     * @var string the transport driver to use e.g. mail, sendmail, smtp
     */
    private $driver;

    /**
     * @var bool a flag to say whether or not we're sending a MIME/HTML message
     */
    private $is_html = false;

    /**
     * Mail constructor.
     *
     * @param bool $exceptions flag to determine whether or not to throw external exceptions?
     *
     * @throws Exception
     */
    public function __construct(bool $exceptions = true)
    {
        parent::__construct($exceptions);
        $this->setDriver();
        $this->setTransport();
        $this->isHTML($this->is_html);
        $this->initialiseFromAddress();
    }

    /**
     * Add a "To" address. Call it multiple times to add more than one.
     *
     * @param string $email
     * @param string $name
     */
    public function addTo(string $email, string $name = '')
    {
        $this->addAddress($email, $name);
    }

    /**
     * Add a Reply-To header. By default, this will be the same as the "From" header.
     * However, you can override it so replies go to a different address.
     *
     * @param string $email
     * @param string $name
     */
    public function setReplyTo(string $email, string $name = '')
    {
        $this->addReplyTo($email, $name);
    }

    /**
     * The email subject line.
     *
     * @param string $subject
     */
    public function setSubject(string $subject)
    {
        $this->Subject = trim($subject);
    }

    /**
     * Add a rich "HTML" message body to the email.
     * A plain text version will be auto-generated from this version.
     *
     * @param string $message
     */
    public function setHtmlMessage(string $message)
    {
        $this->is_html = true;
        $this->msgHTML(trim($message));
    }

    /**
     * Add a plain text portion to the email.
     * For MIME/HTML emails, this is the alternate view.
     * For plain text emails, this is the main body.
     *
     * @param string $message
     */
    public function setPlainTextMessage(string $message)
    {
        $body = $this->is_html ? 'AltBody' : 'Body';
        $this->{$body} = trim($message);
    }

    /**
     * Send the message.
     *
     * @return bool
     *
     * @throws Exception see ErrorInfo property for details
     */
    public function send()
    {
        $this->preSendValidation();

        return parent::send();
    }
}

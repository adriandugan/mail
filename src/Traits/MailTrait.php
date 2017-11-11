<?php

namespace App\Traits;

use PHPMailer\PHPMailer\Exception;

trait MailTrait
{
    /**
     * Set the preferred mail driver.
     */
    private function setDriver()
    {
        if (!$driver = getenv('MAIL_DRIVER')) {
            $driver = self::DRIVER;
        }

        $this->driver = $driver;
    }

    /**
     * Set how the mail should be sent.
     */
    private function setTransport()
    {
        $method = sprintf('setTransportTo%s', ucfirst($this->driver));

        if (!method_exists(self::class, $method)) {
            throw new Exception('Unsupported transport method');
        }

        $this->{$method}();
    }

    /**
     * Set mail sending method to use mail().
     */
    private function setTransportToMail()
    {
        $this->isMail();
    }

    /**
     * Set mail sending method to use SMTP.
     */
    private function setTransportToSmtp()
    {
        $this->configureSMTP();
    }

    /**
     * Set mail sending method to use Sendmail.
     */
    private function setTransportToSendmail()
    {
        $this->isSendmail();
    }

    /**
     * Configure settings for sending via SMTP.
     */
    private function configureSMTP()
    {
        if (!$debug = getenv('MAIL_SMTP_DEBUG')) {
            $debug = self::SMTP_DEBUG;
        }

        $this->isSMTP();
        $this->SMTPAuth = true;
        $this->SMTPDebug = $debug;
        $this->Host = getenv('MAIL_SMTP_HOST');
        $this->Port = getenv('MAIL_SMTP_PORT');
        $this->Username = getenv('MAIL_SMTP_USERNAME');
        $this->Password = getenv('MAIL_SMTP_PASSWORD');
    }

    /**
     * Set up the default "From" address.
     */
    private function initialiseFromAddress()
    {
        $this->setFrom(getenv('MAIL_FROM_EMAIL'), getenv('MAIL_FROM_NAME'));
        $this->setReplyTo($this->From, $this->FromName);
    }

    /**
     * Ensure the email is well-formed before sending it.
     *
     * @throws Exception
     */
    private function preSendValidation()
    {
        if (!$this->From) {
            throw new Exception('No From address');
        }

        if (!$this->Subject) {
            throw new Exception('No Subject line');
        }
    }
}

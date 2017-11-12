<?php

/**
 * An example use case for App\Mail.
 *
 * @author Duggie
 * @since 2017-11-10
 */

require_once __DIR__ . '/autoload.php';

use App\Mail;

/**
 * Send an email.
 */
try {
    $mail = new Mail;

    // Override the default address (specified in environment variables).
    // The name parameter is optional
    $mail->setFrom('from@example.com', 'From Name');

    // Whom to send the email to. Call it multiple times to send the same email to many people.
    // The name parameter is optional
    $mail->addTo('someone@example.com', 'Someone');

    // $mail->addCC('cc@example.com'); // Call multiple times to send to many people. The name parameter is optional
    // $mail->addBCC('bcc@example.com'); // Call multiple times to send to many people. The name parameter is optional

    // Set the subject line - a mandatory field
    $mail->setSubject('This is a test');

    // Set the message body (for an HTML message)
    $mail->setHtmlMessage("<h1>Test Message</h1>
    <p>Hello world!</p>");

    // By default, this is pre-populated with a plain version of the HTML message.
    // But you can call it if you want to customise the plain-text portion.
    $mail->setPlainTextMessage("Hello world!");

    // Runs some pre-flight checks before sending the email.
    $mail->send();
} catch (Exception $e) {
    printf("Unable to send email to: %s (%s) - %s",
        json_encode($mail->getToAddresses()),
        $mail->Subject,
        $mail->ErrorInfo
    );
}

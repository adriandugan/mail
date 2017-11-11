<?php

require_once __DIR__ . '/autoload.php';

use josegonzalez\Dotenv\Loader;
use App\Mail;

(new Loader(__DIR__ . '/.env'))
    ->parse()
    ->putenv();

$mail = new Mail;

$mail->addTo('duggie@gmail.com', 'Duggie');
$mail->setSubject('This is a test');
$mail->setHtmlMessage("<h1>Test Message</h1>
<p>Hello world!</p>");
$mail->setPlainTextMessage("Hello world!");

try {
    $mail->send();
} catch (Exception $e) {
    printf("[code %d] Unable to send your email to: %s (%s) - %s",
        $e->getCode(),
        json_encode($mail->getToAddresses()),
        $mail->Subject,
        $e->getMessage()
    );
}

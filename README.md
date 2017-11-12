[ ![Codeship Status for adriandugan/mail](https://app.codeship.com/projects/244c0c80-aa13-0135-4654-425e3af4c55d/status?branch=master)](https://app.codeship.com/projects/256450)

# Mail

A simple emailer class for PHP.

## The original question

A product we must build will send emails to customers. We know that sending email via SMTP from the customer's server can be problematic so we would like the ability to change our product to use a third party email delivery system in the future - for example SendGrid, Amazon SES or Postmarkapp.com. The specification therefore requires that the email delivery system can be swapped without changing more than a single line of code or configuration file for the entire product.
 
Design a class based email sending system with a working implementation for the built-in mail system of your chosen platform (e.g. PHP's "mail" function, C#'s SmtpClient object etc.) as the initial delivery system. You don't need to actually demonstrate integration with SendGrid and Postmarkapp, however the solution should clearly demonstrate how the system would be changed to use an alternative delivery system.

## Dependencies

* PHP7.0+ (would be lower but I've used native type-hinting)
* PHP Composer

## Build

Run `composer install`.

Configuration is managed via environment variables. For ease of setting these, you may choose to use a dotenv file. You can copy `.env.example` to `.env` and update the settings contained within.

## Usage

I recommend using autoloading so things "Just Work".

```
use App\Mail;

// Here is an example for an HTML email.

try {
    $mail = new Mail;
    $mail->addTo('jane.doe@example.com', 'Jane Doe');
    $mail->setSubject('This is the subject line');
    $mail->setHtmlMessage("<h1>Test Message</h1>
<p>Hello world!</p>");
    $mail->send();
} catch (Exception $e) {
    printf("Unable to send your email to: %s (%s) - %s",
        json_encode($mail->getToAddresses()),
        $mail->Subject,
        $mail->ErrorInfo
    );
}
```

An example script can be found [here](script.php).

## Tests

You can run PHPUnit tests: `vendor/bin/phpunit`

A "Lines of code" report is [here](https://adriandugan.github.io/mail/coverage/lines_of_code.txt)

## This is CRAP!

**100%** test coverage results, including a CRAP index rating, can be found [here](https://adriandugan.github.io/mail/coverage/).

![Code coverage overview](images/coverage_overview.png)

I keep CRAP index scores _well below_ the accepted target of "30" per class which results in smaller classes, smaller function declarations. Fewer lines of code means less room for bugs and more maintainable code.

The maximum function CRAP index score is only **3**. There is minimal complexity here :-)

## Built with...

* TDD - test-driven development, ensuring clean, lean and working code.
* [PHPStorm](https://www.jetbrains.com/phpstorm/) - _The_ standard PHP IDE.
* [phpcs](https://github.com/squizlabs/PHP_CodeSniffer) - PHPCodeSniffer
* [phploc](https://github.com/sebastianbergmann/phploc) - lines of code report generator
* [FIG-PSR2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) - coding standard 
* [Composer](https://getcomposer.org/) - PHP package dependency manager
* [CodeShip](https://codeship.com/) - a Continuous Integration Continous Deployment (CICD) hosted service. Called automatically for _every_ Pull Request.

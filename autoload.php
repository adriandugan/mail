<?php declare(strict_types=1); // Force strict typehinting

/**
 * The autoloader file. "Composer" creates and maintains an autoload file.
 * We can further customise what is autoloaded by adding the recipes in here.
 *
 * @author Duggie
 * @since 2017-11-10
 */

require_once __DIR__ . '/vendor/autoload.php';

use josegonzalez\Dotenv\Loader;

// load the dotenv file. This file won't exist on production.
$dotenv = __DIR__ . '/.env';
if (file_exists($dotenv)) {
    (new Loader($dotenv))
        ->parse()
        ->putenv();
}

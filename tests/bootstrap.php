<?php

if (PHP_SAPI === 'cli') {
    \Symfony\Component\VarDumper\VarDumper::setHandler(function () {});
}

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

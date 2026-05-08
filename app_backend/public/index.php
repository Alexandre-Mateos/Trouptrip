<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload.php';

$env = $_SERVER['APP_ENV']
    ?? $_ENV['APP_ENV']
    ?? getenv('APP_ENV')
    ?? 'prod';

$debug = filter_var(
    $_SERVER['APP_DEBUG']
    ?? $_ENV['APP_DEBUG']
    ?? getenv('APP_DEBUG')
    ?? false,
    FILTER_VALIDATE_BOOL
);

return new Kernel($env, $debug);

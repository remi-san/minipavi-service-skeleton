<?php

declare(strict_types=1);

use App\Container;
use MiniPaviService\Http\KernelBuilder;
use MiniPaviService\Log\ErrorLogLogger;

// Only this public/ folder is served by the web server, the rest is in the parent folder
require __DIR__ . '/../vendor/autoload.php';

// Errors go to the log, not to the Minitel
error_reporting(E_ERROR);
ini_set('display_errors', '0');

// The service's routes and controllers (config/container.php, shared with the tests)
$container = Container::build();

// The framework's wiring (gateway, browsers asking ?web=true, the emulator for the others),
// with the service's: errors and connections go to PHP's error log, the context is signed with
// CONTEXT_SECRET (not without it, with a warning), and the URLs and the emulator come from the
// web server's environment (config/container.php)
KernelBuilder::for($container->router(), $container->messages())
    ->withLogger(new ErrorLogLogger())
    ->withContextKey((string) getenv('CONTEXT_SECRET'))
    ->withRequestUrl($container->requestUrl())
    ->withEmulator($container->emulator())
    ->build()
    ->run();

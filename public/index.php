<?php

use MiniPaviService\Core\Router;
use MiniPaviService\Http\Emulator\EmulatorConfig;
use MiniPaviService\Http\KernelBuilder;
use MiniPaviService\Http\Url\RequestUrl;
use MiniPaviService\Log\ErrorLogLogger;
use MiniPaviService\Message\Messages;

// Only this public/ folder is served by the web server, the rest is in the parent folder
require __DIR__.'/../vendor/autoload.php';

error_reporting(E_ERROR);
ini_set('display_errors', 0);

// The service's routes and controllers (config/container.php, shared with the tests)
$container = require dirname(__DIR__).'/config/container.php';

// The framework's wiring (gateway, browsers asking ?web=true, the emulator for the others),
// with the service's: errors and connections go to PHP's error log, the context is signed with
// CONTEXT_SECRET (not without it, with a warning), and the URLs and the emulator come from the
// web server's environment (config/container.php)
KernelBuilder::for($container->get(Router::class), $container->get(Messages::class))
	->withLogger(new ErrorLogLogger())
	->withContextKey(getenv('CONTEXT_SECRET') ?: '')
	->withRequestUrl($container->get(RequestUrl::class))
	->withEmulator($container->get(EmulatorConfig::class))
	->build()
	->run();

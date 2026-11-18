<?php

declare(strict_types=1);

use App\Container;
use MiniPaviService\Http\KernelBuilder;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;

// Only this public/ folder is served by the web server, the rest is in the parent folder
require __DIR__ . '/../vendor/autoload.php';

// Errors go to the log, not to the Minitel: PHP's display_errors is off (php.ini-production,
// and the stack's PHP configuration)
error_reporting(E_ERROR);

// The service's routes and controllers (config/container.php, shared with the tests)
$container = Container::build();

// Errors and connections go to PHP's error log (docker compose logs services), with Monolog: a
// line per record, the exception's trace included (error_log() adds the date)
$errorLog = new ErrorLogHandler();
$errorLog->setFormatter(
    new LineFormatter(
        '%channel%.%level_name%: %message% %context% %extra%',
        ignoreEmptyContextAndExtra: true,
        includeStacktraces: true,
    ),
);

// The framework's wiring (gateway, browsers asking ?web=true, the emulator for the others),
// with the service's: its logger, the context signed with CONTEXT_SECRET (not without it, with
// a warning), and the URLs and the emulator from the web server's environment
// (config/container.php)
KernelBuilder::for($container->router(), $container->messages())
    ->withLogger(new Logger('service', [$errorLog]))
    ->withContextKey((string) getenv('CONTEXT_SECRET'))
    ->withRequestUrl($container->requestUrl())
    ->withEmulator($container->emulator())
    ->build()
    ->run();

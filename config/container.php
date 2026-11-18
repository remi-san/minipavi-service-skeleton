<?php

declare(strict_types=1);

// Definitions of the service's PHP-DI container (App\Container), whose Router has every route.
// Used by public/index.php, and by the tests (tests/), so they run the service as served.

use App\AppMessage;
use App\Controller\HomeController;
use MiniPaviService\Core\Router;
use MiniPaviService\Core\RouterBuilder;
use MiniPaviService\Http\Emulator\EmulatorConfig;
use MiniPaviService\Http\Url\RequestUrl;
use MiniPaviService\Message\FrameworkMessage;
use MiniPaviService\Message\Messages;
use MiniPaviService\Message\MessagesXmlParser;
use MiniPaviService\Page\Parser\PageXmlParser;

use function DI\create;
use function DI\factory;
use function DI\get;

$config = __DIR__;

// A variable of the environment, null when it isn't set or is empty
$env = static function (string $name): ?string {
    $value = getenv($name);

    return \is_string($value) && $value !== '' ? $value : null;
};

// Controllers are lazy: the router gets proxies, and a controller is only built, and its pages
// parsed, when its route runs (PHP 8.4's native lazy objects)
return [
    // The web server's environment, read here: the framework doesn't read it itself
    RequestUrl::class => create()->constructor($env('SERVICE_URL'), $env('TRUSTED_PROXIES') ?? ''),
    EmulatorConfig::class => factory(static fn() => EmulatorConfig::fromValues(
        $env('EMULATOR_URL'),
        $env('GATEWAY_URL'),
        $env('SERVICES_INTERNAL_HOST'),
    )),

    // The texts shown on screen: config/messages.xml over the framework's, checked to have every
    // message of the framework and of the service
    Messages::class => factory(
        static fn() => new MessagesXmlParser()
            ->parseFile("{$config}/messages.xml", Messages::defaults())
            ->check(FrameworkMessage::class, AppMessage::class),
    ),
    PageXmlParser::class => create()->constructor(get(Messages::class)),

    'page.home' => factory([PageXmlParser::class, 'parseFile'])->parameter('file', "{$config}/pages/home.xml"),
    'controller.home' => create(HomeController::class)->constructor(get('page.home'))->lazy(),

    RouterBuilder::class => create()
        ->constructor(HomeController::NAME)
        ->method('addRoute', HomeController::NAME, get('controller.home')),
    // Fails if the default route isn't a route
    Router::class => factory([RouterBuilder::class, 'build']),
];

<?php

// Composition root of the service: the PHP-DI container, whose Router has every route.
// Used by public/index.php, and by the tests (tests/), so they run the service as served.

use App\AppMessage;
use App\Controller\HomeController;
use DI\ContainerBuilder;
use MiniPaviService\Core\Router;
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

// Controllers are lazy: the router gets proxies, and a controller is only built, and its pages
// parsed, when its route runs (PHP 8.4's native lazy objects)
return (new ContainerBuilder())->addDefinitions([
	// The web server's environment, read here: the framework doesn't read it itself
	RequestUrl::class => create()->constructor(getenv('SERVICE_URL') ?: null, getenv('TRUSTED_PROXIES') ?: ''),
	EmulatorConfig::class => factory(fn() => EmulatorConfig::fromValues(
		getenv('EMULATOR_URL') ?: null,
		getenv('GATEWAY_URL') ?: null,
		getenv('SERVICES_INTERNAL_HOST') ?: null,
	)),

	// The texts shown on screen: config/messages.xml over the framework's, checked to have every
	// message of the framework and of the service
	Messages::class => factory(fn() => (new MessagesXmlParser())
		->parseFile("$config/messages.xml", Messages::defaults())
		->check(FrameworkMessage::class, AppMessage::class)),
	PageXmlParser::class => create()->constructor(get(Messages::class)),

	'page.home' => factory([PageXmlParser::class, 'parseFile'])->parameter('file', "$config/pages/home.xml"),
	'controller.home' => create(HomeController::class)->constructor(get('page.home'))->lazy(),

	Router::class => create()
		->method('addRoute', HomeController::NAME, get('controller.home'))
		->method('setDefaultRoute', HomeController::NAME),
])->build();

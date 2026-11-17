<?php

declare(strict_types=1);

namespace App;

use DI\ContainerBuilder;
use MiniPaviService\Core\Router;
use MiniPaviService\Http\Emulator\EmulatorConfig;
use MiniPaviService\Http\Url\RequestUrl;
use MiniPaviService\Message\Messages;

/**
 * The service's objects, built by PHP-DI from config/container.php, typed: what
 * public/index.php and the tests need
 */
final readonly class Container
{
    private function __construct(
        private \DI\Container $container,
    ) {}

    public static function build(): self
    {
        return new self(
            new ContainerBuilder()
                ->addDefinitions(__DIR__ . '/../config/container.php')
                ->build(),
        );
    }

    public function router(): Router
    {
        $router = $this->container->get(Router::class);

        return $router instanceof Router ? $router : throw new \LogicException('No Router in the container');
    }

    public function messages(): Messages
    {
        $messages = $this->container->get(Messages::class);

        return $messages instanceof Messages ? $messages : throw new \LogicException('No Messages in the container');
    }

    public function requestUrl(): RequestUrl
    {
        $url = $this->container->get(RequestUrl::class);

        return $url instanceof RequestUrl ? $url : throw new \LogicException('No RequestUrl in the container');
    }

    public function emulator(): EmulatorConfig
    {
        $emulator = $this->container->get(EmulatorConfig::class);

        return $emulator instanceof EmulatorConfig
            ? $emulator
            : throw new \LogicException('No EmulatorConfig in the container');
    }
}

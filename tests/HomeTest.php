<?php

declare(strict_types=1);

namespace App\Tests;

use App\Container;
use MiniPaviService\Core\ContextSerializer;
use MiniPaviService\Core\Service;
use MiniPaviService\Minitel\FunctionKey;
use MiniPaviService\Testing\ServiceTester;
use PHPUnit\Framework\TestCase;

/**
 * A user's session on the service, as served (config/container.php), without the gateway
 */
final class HomeTest extends TestCase
{
    private ServiceTester $tester;

    protected function setUp(): void
    {
        $container = Container::build();
        $serializer = new ContextSerializer($container->router());
        $this->tester = new ServiceTester(
            new Service($container->router(), $container->messages(), $serializer),
            $serializer,
        );
    }

    public function testAsksForTheNameOnConnection(): void
    {
        $this->tester
            ->connect()
            ->assertRoute('home')
            ->assertScreenContains('MON SERVICE')
            ->assertScreenContains('Votre nom :')
            ->assertScreenNotContains('Bonjour');
    }

    public function testGreetsTheUser(): void
    {
        $this->tester->connect()->type('Ada')->press(FunctionKey::Send)->assertScreenContains('Bonjour Ada !');
    }

    public function testAsksAgainWithoutName(): void
    {
        $this->tester->connect()->press(FunctionKey::Send)->assertLine0('Tapez votre nom puis Envoi');
    }
}

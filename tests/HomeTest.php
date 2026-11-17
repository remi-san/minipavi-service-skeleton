<?php

declare(strict_types=1);

namespace App\Tests;

use MiniPaviService\Core\Router;
use MiniPaviService\Core\Service;
use MiniPaviService\Message\Messages;
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
        $container = require __DIR__ . '/../config/container.php';
        $this->tester = new ServiceTester(new Service($container->get(Router::class), $container->get(Messages::class)));
    }

    public function testAsksForTheNameOnConnection(): void
    {
        $this->tester->connect()
            ->assertRoute('home')
            ->assertScreenContains('MON SERVICE')
            ->assertScreenContains('Votre nom :')
            ->assertScreenNotContains('Bonjour');
    }

    public function testGreetsTheUser(): void
    {
        $this->tester->connect()
            ->type('Ada')->press(FunctionKey::Send)
            ->assertScreenContains('Bonjour Ada !');
    }

    public function testAsksAgainWithoutName(): void
    {
        $this->tester->connect()
            ->press(FunctionKey::Send)
            ->assertLine0('Tapez votre nom puis Envoi');
    }
}

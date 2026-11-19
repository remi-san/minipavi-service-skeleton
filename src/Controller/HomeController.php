<?php

declare(strict_types=1);

namespace App\Controller;

use App\AppMessage;
use MiniPaviService\Controller\AbstractController;
use MiniPaviService\Controller\ControllerState;
use MiniPaviService\Controller\Parameters\NoParameters;
use MiniPaviService\Controller\Parameters\ParametersType;
use MiniPaviService\Controller\Request;
use MiniPaviService\Controller\Response;
use MiniPaviService\Controller\Response\RunCommand;
use MiniPaviService\Minitel\FunctionKey;
use MiniPaviService\Page;
use MiniPaviService\Vdt\Vdt;

/**
 * Home screen: asks for the user's name, then greets them. The texts are in the page
 * (config/pages/home.xml) and the messages (config/messages.xml), not here.
 *
 * @extends AbstractController<NoParameters>
 */
final class HomeController extends AbstractController
{
    public const NAME = 'home';

    public function __construct(
        private Page $page,
    ) {}

    public function parametersType(): ParametersType
    {
        return ParametersType::none();
    }

    protected function onDisplay(Request $request): Response
    {
        // No name yet: the page doesn't greet
        return $this->askName($this->page->display($request->terminal(), ['name' => '']));
    }

    protected function onReadInput(Request $request): Response
    {
        if ($request->functionKey() !== FunctionKey::Send) {
            return $this->onDisplay($request);
        }

        $name = trim($request->contentAsString());
        if ($name === '') {
            return $this->askName(Vdt::line0($this->page->message(AppMessage::NameMissing)));
        }

        return $this->askName($this->page->display($request->terminal(), ['name' => $name]));
    }

    /**
     * Sends vdt, then reads a name
     *
     * @return Response<ControllerState>
     */
    private function askName(Vdt $vdt): Response
    {
        return RunCommand::readInput($vdt, $this->page->inputCommand());
    }
}

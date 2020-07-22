<?php

declare(strict_types = 1);

namespace App\Web\Actions\Game;

use App\Web\Actions\AbstractAction;
use App\Web\Actions\ProfileTrait;
use App\Services\GameService;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class GameCreate extends AbstractAction
{
    use ProfileTrait;

    public function __invoke(ServerRequest $request)
    {
        $gameService = new GameService($this->resources->getAtlas());
        $userId = $this->getLoggedUserId($request);
        $game = $gameService->createGame($userId);

        return new Response\RedirectResponse($this->resources->getWebRouter()->getGenerator()->generate('game.view', [
            'gameCode' => $game->code,
        ]));
    }
}

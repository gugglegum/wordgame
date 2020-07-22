<?php

declare(strict_types = 1);

namespace App\Web\Actions\Game;

use App\Web\Actions\AbstractAction;
use App\Web\Actions\GameTrait;
use App\Web\Actions\ProfileTrait;
use App\Services\GameService;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class GameJoin extends AbstractAction
{
    use ProfileTrait;
    use GameTrait;

    public function __invoke(ServerRequest $request)
    {
        $userId = $this->getLoggedUserId($request);
        $game = $this->getRequestedGame($request);
        $gameService = new GameService($this->resources->getAtlas());
        $gameService->joinUserToGame($userId, $game->id);

        return new Response\RedirectResponse($this->resources->getWebRouter()->getGenerator()->generate('game.view', [
            'gameCode' => $game->code,
        ]));
    }
}

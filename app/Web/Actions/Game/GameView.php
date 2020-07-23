<?php

declare(strict_types = 1);

namespace App\Web\Actions\Game;

use App\Exceptions\Http\Http404NotFoundException;
use App\Services\GameService;
use App\Web\Actions\AbstractAction;
use App\Web\Actions\GameTrait;
use App\Web\Actions\ProfileTrait;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class GameView extends AbstractAction
{
    use ProfileTrait;
    use GameTrait;

    public function __invoke(ServerRequest $request)
    {
        $loggedUserId = $this->getLoggedUserId($request);
        $gameService = new GameService($this->resources->getAtlas());
        $game = $this->getRequestedGame($request);
        return new Response\HtmlResponse(
            $this->resources->getTemplateEngine()->render('game/view', [
                'game' => $game,
                'loggedUserId' => $loggedUserId,
                'isJoined' => $gameService->isUserInGame($loggedUserId, $game->id),
            ])
        );
    }

}

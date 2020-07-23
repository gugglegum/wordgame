<?php

declare(strict_types = 1);

namespace App\Web\Actions\Profile;

use App\Services\GameService;
use App\Web\Actions\AbstractAction;
use App\Web\Actions\ProfileTrait;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class ProfileViewAction extends AbstractAction
{
    use ProfileTrait;

    public function __invoke(ServerRequest $request)
    {
        $user = $this->getRequestedUser($request);
        $gameService = new GameService($this->resources->getAtlas());
        $games = $gameService->getGamesWithUser($user->id, true);
        $gamesData = [];
        foreach ($games as $game) {
            $gamesData[$game->id] = [
                'game' => $game,
                'users' => $gameService->getGameUsers($game->id),
                'movesCount' => $gameService->getGameMovesCount($game->id),
            ];
        }

        return new Response\HtmlResponse(
            $this->resources->getTemplateEngine()->render('profile/view', [
                'user' => $user,
                'gamesData' => $gamesData,
            ])
        );
    }
}

<?php

declare(strict_types = 1);

namespace App\Web\Actions;

use App\Exceptions\Http\Http404NotFoundException;
use Zend\Diactoros\ServerRequest;

trait GameTrait
{
    /**
     * @param ServerRequest $request
     * @return string|null
     */
    private function getRequestedGameCode(ServerRequest $request): ?string
    {
        return $request->getAttribute('gameCode');
    }

    /**
     * @param ServerRequest $request
     * @return \App\DataSource\Game\GameRecord
     */
    private function getRequestedGame(ServerRequest $request)
    {
        $game = $this->resources->getAtlas()->select(\App\DataSource\Game\Game::class)
            ->where('code = ', (string) $this->getRequestedGameCode($request))
            ->fetchRecord();
        if ($game instanceof \App\DataSource\Game\GameRecord) {
            return $game;
        } else {
            throw new Http404NotFoundException();
        }
    }
}

<?php

declare(strict_types = 1);

namespace App\Web\Actions\Game;

use App\DataSource\Event\EventRecord;
use App\Web\Actions\AbstractAction;
use App\Web\Actions\GameTrait;
use App\Web\Actions\ProfileTrait;
use App\Services\GameService;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class GameEvents extends AbstractAction
{
    use ProfileTrait;
    use GameTrait;

    public function __invoke(ServerRequest $request)
    {
        $game = $this->getRequestedGame($request);
        $gameService = new GameService($this->resources->getAtlas());
        $data = ['events' => [], 'players' => [], 'currentPlayerId' => null];

        $queryParams = $request->getQueryParams();
        $afterId = array_key_exists('after', $queryParams) ? (int) $queryParams['after'] : null;
        $timeStart = gettimeofday(true);
        do {
            /** @var EventRecord[] $events */
            $events = $gameService->getGameEvents($game->id, $afterId);
            if ($afterId !== null && count($events) == 0 && gettimeofday(true) - $timeStart < 20) {
                sleep(1);
                continue;
            }
            foreach ($events as $event) {
                $data['events'][] = [
                    'id' => $event->id,
                    'type' => $event->type,
                    'playerId' => $event->player_id,
                    'word' => $event->word,
                ];
            }
            break;
        } while (true);

        $players = $gameService->getGamePlayers($game->id);
        foreach ($players as $player) {
            $data['players'][$player->id] = [
                'id' => $player->id,
                'username' => $player->username,
            ];
        }
        $data['currentPlayerId'] = $gameService->getCurrentPlayerId($game->id);

        return new Response\JsonResponse($data);
    }
}

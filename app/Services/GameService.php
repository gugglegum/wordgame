<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataSource\Event\Event;
use App\DataSource\Event\EventRecord;
use App\DataSource\Game\Game;
use App\DataSource\Game\GameRecord;
use App\DataSource\GamesPlayer\GamesPlayer;
use App\DataSource\GamesPlayer\GamesPlayerRecord;
use App\DataSource\GamesPlayer\GamesPlayerTable;
use App\DataSource\User\User;
use App\DataSource\User\UserRecord;
use App\Exceptions\Exception;
use App\Web\Actions\Game\GameEvents;
use Atlas\Orm\Atlas;

class GameService
{
    /**
     * @var Atlas
     */
    private $atlas;

    public function __construct(Atlas $atlas)
    {
        $this->atlas = $atlas;
    }

    /**
     * @param string $gameCode
     * @return GameRecord
     */
    public function getGameByCode(string $gameCode): GameRecord
    {
        /** @var GameRecord $game */
        $game = $this->atlas->select(Game::class)->where('code = ', $gameCode)->fetchRecord();
        return $game;
    }

    /**
     * @param int $playerId
     * @return GameRecord
     * @throws Exception
     */
    public function createGame(int $playerId): GameRecord
    {
        $gameCode = $this->generateUniqueGameCode();
        /** @var GameRecord $game */
        $game = $this->atlas->newRecord(Game::class, [
            'code' => $gameCode,
            'creator_id' => $playerId,
        ]);
        $this->atlas->insert($game);
        $this->joinPlayerToGame($playerId, (int) $game->id);
        return $game;
    }

    /**
     * @param int $playerId
     * @param int $gameId
     */
    public function joinPlayerToGame(int $playerId, int $gameId)
    {
        $pdo = $this->atlas->mapper(GamesPlayer::class)->getTable()->getReadConnection()->getPdo();
        $stmt = $pdo->prepare('INSERT INTO ' . GamesPlayerTable::NAME . ' (`game_id`, `player_id`, `active`) VALUES (:gameId, :playerId, 1) ON DUPLICATE KEY UPDATE `active` = 1');
        $stmt->execute([
            ':gameId' => $gameId,
            ':playerId' => $playerId,
        ]);
        $this->addEventToGame('join', null, $playerId, $gameId);
    }

    /**
     * @param int $playerId
     * @param int $gameId
     */
    public function leavePlayerFromGame(int $playerId, int $gameId)
    {
        $this->atlas->mapper(GamesPlayer::class)->getTable()->update()->set('active', 0)
            ->where('player_id = ', $playerId)
            ->where('game_id = ', $gameId)
            ->perform();
        $this->addEventToGame('leave', null, $playerId, $gameId);
    }

    /**
     * @param int $gameId
     * @param bool $activeOnly
     * @return array
     */
    public function getGamePlayerIds(int $gameId, $activeOnly = false): array
    {
        $query = $this->atlas->select(GamesPlayer::class)
            ->columns('player_id')
            ->where('game_id = ', $gameId)
            ->orderBy('id');
        if ($activeOnly) {
            $query->where('active != ', 0);
        }
        return $query->fetchColumn();
    }

    /**
     * @param int $gameId
     * @return UserRecord[]
     */
    public function getGamePlayers(int $gameId): array
    {
        $gamePlayerIds = $this->getGamePlayerIds($gameId);

        /** @var UserRecord[] $players */
        $players = $this->atlas->select(User::class)
            ->columns('id, username')
            ->where('id IN ', $gamePlayerIds)
            ->fetchRecords();

        $playersMap = [];
        foreach ($players as $player) {
            $playersMap[$player->id] = $player;
        }

        $playersSorted = [];
        foreach ($gamePlayerIds as $gamePlayerId) {
            $playersSorted[] = $playersMap[$gamePlayerId];
        }

        return $playersSorted;
    }

    /**
     * @param int $gameId
     * @param string|null $type
     * @return EventRecord|null
     */
    public function getLastEvent(int $gameId, string $type = null): ?EventRecord
    {
        /** @var EventRecord $lastEvent */
        $query = $this->atlas->select(Event::class)
            ->where('game_id = ', $gameId)
            ->orderBy('id desc')
            ->limit(1);
        if ($type !== null) {
            $query->where('type = ', $type);
        }
        /** @var $lastEvent EventRecord|null */
        $lastEvent = $query->fetchRecord();
        return $lastEvent;
    }

    /**
     * @param int $gameId
     * @return int
     */
    public function getCurrentPlayerId(int $gameId): int
    {
        $gamePlayerIds = $this->getGamePlayerIds($gameId, true);
        $lastEvent = $this->getLastEvent($gameId, 'move');
        if ($lastEvent instanceof EventRecord) {
            $index = array_search($lastEvent->player_id, $gamePlayerIds);
            return $gamePlayerIds[($index + 1) % count($gamePlayerIds)];
        } else {
            return $gamePlayerIds[0];
        }
    }

    /**
     * @param int $playerId
     * @param int $gameId
     * @return bool
     */
    public function isPlayerInGame(int $playerId, int $gameId): bool
    {
        return (bool) $this->atlas->select(GamesPlayer::class)
            ->where('player_id = ', $playerId)
            ->where('game_id = ', $gameId)
            ->where('active != ', 0)
            ->fetchCount();
    }

    /**
     * @param string $type
     * @param string|null $word
     * @param int $playerId
     * @param int $gameId
     */
    public function addEventToGame(string $type, ?string $word, int $playerId, int $gameId)
    {
        $event = $this->atlas->newRecord(Event::class, [
            'game_id' => $gameId,
            'type' => $type,
            'player_id' => $playerId,
            'word' => $word,
            'data' => json_encode([]),
        ]);
        $this->atlas->insert($event);
    }

    /**
     * @param int $gameId
     * @param int|null $afterId
     * @return array
     */
    public function getGameEvents(int $gameId, int $afterId = null): array
    {
        $query = $this->atlas->select(Event::class)
            ->where('game_id = ', $gameId)
            ->orderBy('id');
        if ($afterId) {
            $query->where('id > ', $afterId);
        }
        return $query->fetchRecords();
    }

    /**
     * @param string $word
     * @param int $gameId
     * @return bool
     */
    public function isWordAlreadyUsed(string $word, int $gameId): bool
    {
        return $this->atlas->select(Event::class)
            ->where('game_id = ', $gameId)
            ->where('word = ', $word)
            ->fetchCount() != 0;
    }

    /**
     * @param string $gameCode
     * @return bool
     */
    private function checkIsGameCodeExists(string $gameCode): bool
    {
        return $this->atlas->select(Game::class)->where('code = ', $gameCode)->fetchCount() != 0;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function generateUniqueGameCode(): string
    {
        do {
            $gameCode = self::generateCode();
        } while ($this->checkIsGameCodeExists($gameCode));
        return $gameCode;
    }

    /**
     * @return string
     * @throws Exception
     */
    private static function generateCode(): string
    {
        static $alphabet;
        if (!$alphabet) {
            $alphabet = array_merge(
//                range('a', 'z'),
                range('A', 'Z'),
//                range('0', '9')
            );
        }
        $salt = '';
        try {
            for ($i = 0; $i < 8; $i++) {
                $salt .= $alphabet[random_int(0, count($alphabet) - 1)];
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $salt;
    }
}

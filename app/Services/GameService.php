<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataSource\Event\Event;
use App\DataSource\Event\EventRecord;
use App\DataSource\Game\Game;
use App\DataSource\Game\GameRecord;
use App\DataSource\Player\Player;
use App\DataSource\Player\PlayerTable;
use App\DataSource\User\User;
use App\DataSource\User\UserRecord;
use App\Exceptions\Exception;
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
     * @param int $userId
     * @return GameRecord
     * @throws Exception
     */
    public function createGame(int $userId): GameRecord
    {
        $gameCode = $this->generateUniqueGameCode();
        /** @var GameRecord $game */
        $game = $this->atlas->newRecord(Game::class, [
            'code' => $gameCode,
            'creator_id' => $userId,
        ]);
        $this->atlas->insert($game);
        $this->joinUserToGame($userId, (int) $game->id);
        return $game;
    }

    /**
     * @param int $userId
     * @param int $gameId
     */
    public function joinUserToGame(int $userId, int $gameId)
    {
        $pdo = $this->atlas->mapper(Player::class)->getTable()->getReadConnection()->getPdo();
        $stmt = $pdo->prepare('INSERT INTO ' . PlayerTable::NAME . ' (`game_id`, `user_id`, `active`) VALUES (:gameId, :userId, 1) ON DUPLICATE KEY UPDATE `active` = 1');
        $stmt->execute([
            ':gameId' => $gameId,
            ':userId' => $userId,
        ]);
        $this->addEventToGame('join', null, $userId, $gameId);
    }

    /**
     * @param int $userId
     * @param int $gameId
     */
    public function leaveUserFromGame(int $userId, int $gameId)
    {
        $this->atlas->mapper(Player::class)->getTable()->update()->set('active', 0)
            ->where('user_id = ', $userId)
            ->where('game_id = ', $gameId)
            ->perform();
        $this->addEventToGame('leave', null, $userId, $gameId);
    }

    /**
     * @param int $gameId
     * @param bool|null $active
     * @return array
     */
    public function getGameUserIds(int $gameId, bool $active = null): array
    {
        $query = $this->atlas->select(Player::class)
            ->columns('user_id')
            ->where('game_id = ', $gameId)
            ->orderBy('id');
        if ($active !== null) {
            $query->where('active = ', (int) $active);
        }
        return $query->fetchColumn();
    }

    /**
     * @param int $userId
     * @return GameRecord[]
     */
    public function getGamesOfUser(int $userId): array
    {
        return $this->atlas->select(Game::class)
            ->where('creator_id = ', $userId)
            ->orderBy('id DESC')
            ->fetchRecords();
    }

    /**
     * @param int $userId
     * @param bool|null $active
     * @return GameRecord[]
     */
    public function getGamesWithUser(int $userId, bool $active = null): array
    {
        return $this->atlas->select(Game::class)
            ->columns('games.*')
            ->join('INNER JOIN', 'players', 'players.game_id = games.id' . ($active !== null ? (' AND players.active = ' . (int) $active) : ''))
            ->where('players.user_id = ', $userId)
            ->orderBy('games.id DESC')
            ->fetchRecords();
    }

    /**
     * @param int $gameId
     * @return UserRecord[]
     */
    public function getGameUsers(int $gameId): array
    {
        $gameUserIds = $this->getGameUserIds($gameId);

        if (count($gameUserIds) > 0) {
            /** @var UserRecord[] $users */
            $users = $this->atlas->select(User::class)
                ->columns('id, username')
                ->where('id IN ', $gameUserIds)
                ->fetchRecords();

            $usersMap = [];
            foreach ($users as $user) {
                $usersMap[$user->id] = $user;
            }

            $usersSorted = [];
            foreach ($gameUserIds as $gameUserId) {
                $usersSorted[] = $usersMap[$gameUserId];
            }
        } else {
            $usersSorted = [];
        }

        return $usersSorted;
    }

    /**
     * @param int $gameId
     * @return int
     */
    public function getGameMovesCount(int $gameId): int
    {
        return $this->atlas->select(Event::class)
            ->where('game_id = ', $gameId)
            ->where('type = ', 'move')
            ->fetchCount();
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
    public function getCurrentUserId(int $gameId): int
    {
        $gameUserIds = $this->getGameUserIds($gameId, true);
        $lastEvent = $this->getLastEvent($gameId, 'move');
        if ($lastEvent instanceof EventRecord) {
            $index = array_search($lastEvent->user_id, $gameUserIds);
            return $gameUserIds[($index + 1) % count($gameUserIds)];
        } else {
            return $gameUserIds[0];
        }
    }

    /**
     * @param int $userId
     * @param int $gameId
     * @return bool
     */
    public function isUserInGame(int $userId, int $gameId): bool
    {
        return (bool) $this->atlas->select(Player::class)
            ->where('user_id = ', $userId)
            ->where('game_id = ', $gameId)
            ->where('active = ', 1)
            ->fetchCount();
    }

    /**
     * @param string $type
     * @param string|null $word
     * @param int $userId
     * @param int $gameId
     */
    public function addEventToGame(string $type, ?string $word, int $userId, int $gameId)
    {
        $event = $this->atlas->newRecord(Event::class, [
            'game_id' => $gameId,
            'type' => $type,
            'user_id' => $userId,
            'word' => $word,
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

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

class GameWordSubmit extends AbstractAction
{
    use ProfileTrait;
    use GameTrait;

    public function __invoke(ServerRequest $request)
    {
        $loggedUserId = $this->getLoggedUserId($request);
        $word = trim($request->getParsedBody()['word']);
        $game = $this->getRequestedGame($request);
        $gameService = new GameService($this->resources->getAtlas());
        if ($gameService->getCurrentUserId($game->id) != $loggedUserId) {
            return new Response\JsonResponse([
                'error' => 'Сейчас не ваш ход',
            ]);
        }

        if (!self::checkWordValidChars($word)) {
            return new Response\JsonResponse([
                'error' => 'Недопустимые символы, только одно слово кириллицей',
            ]);
        }

        $lastMoveEvent = $gameService->getLastEvent($game->id, 'move');
        if ($lastMoveEvent instanceof EventRecord) {
            if (self::getWordLastChar($lastMoveEvent->word) != self::getWordFirstChar($word)) {
                return new Response\JsonResponse([
                    'error' => 'Слово должно начинаться на букву "' . self::getWordLastChar($lastMoveEvent->word) . '"',
                ]);
            }
        }

        if ($gameService->isWordAlreadyUsed($word, $game->id)) {
            return new Response\JsonResponse([
                'error' => 'Слово "' . $word . '" уже было использовано',
            ]);
        }

        $gameService->addEventToGame('move', $word, $loggedUserId, $game->id);
        return new Response\JsonResponse([]);
    }

    private static function checkWordValidChars(string $word): bool
    {
        return preg_match('/^[А-Я]+$/ui', $word) != 0;
    }

    private static function getWordFirstChar(string $word): string
    {
        return mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8');
    }

    private static function getWordLastChar(string $word): string
    {
        $pos = mb_strlen($word, 'UTF-8') - 1;
        do {
            $char = mb_strtoupper(mb_substr($word, $pos, 1, 'UTF-8'), 'UTF-8');
            $pos--;
        } while ($char == 'Ь' && $pos >= 0);
        return $char;
    }

}

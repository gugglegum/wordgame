<?php

declare(strict_types = 1);

namespace App\Web\Components\Authentication;

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CookieAuthenticator
{
    /**
     * @var string
     */
    private $sessionCookieName = 'session_id';

    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $sessionCache;

    /**
     * @var int
     */
    private $sessionTtl = 14 * 24 * 3600;

    private static $sessionIdLength = 32;

    public function __construct(\Psr\SimpleCache\CacheInterface $sessionCache)
    {
        $this->sessionCache = $sessionCache;
    }

    /**
     * @param ResponseInterface $response
     * @param int $userId
     * @return ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public function createSession(ResponseInterface $response, int $userId): ResponseInterface
    {
        $sessionId = $this->generateSessionId();

        // We use max-age cookie parameter only since expires parameter deprecated in HTTP 1.1. However IE browser
        // doesn't support max-age, it will save cookie as session. Expire parameter may have issue in case of
        // difference in clock on the server and the client.
        $response = FigResponseCookies::set($response, SetCookie::create($this->sessionCookieName)
            ->withValue($sessionId)
            ->withMaxAge($this->sessionTtl)
            ->withHttpOnly(true));

        $this->sessionCache->set($sessionId, $userId, $this->sessionTtl);

        return $response;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function prolongateSession(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $sessionId = $this->getSessionIdFromRequestCookies($request);
        if ($sessionId) {
            $userId = $this->sessionCache->get($sessionId);
            if ($userId) {
                $this->sessionCache->set($sessionId, $userId, $this->sessionTtl);

                if (!empty($sessionId)) {
                    $response = FigResponseCookies::set($response, SetCookie::create($this->sessionCookieName)
                        ->withValue($sessionId)
                        ->withMaxAge($this->sessionTtl)
                        ->withHttpOnly(true)
                    );
                }
            }
        }
        return $response;
    }

//    /**
//     * @param ResponseInterface $response
//     * @return ResponseInterface
//     */
//    public function setRemoveSessionCookie(ResponseInterface $response): ResponseInterface
//    {
//        $response = FigResponseCookies::set($response, SetCookie::create($this->sessionCookieName)
//            ->withValue('')
//            ->withMaxAge(-1)
//            ->withHttpOnly(true));
//        return $response;
//    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function killSession(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $sessionId = $this->getSessionIdFromRequestCookies($request);
        if ($sessionId !== null) {
            $this->sessionCache->delete($sessionId);
        }
        $response = FigResponseCookies::set($response, SetCookie::create($this->sessionCookieName)
            ->withValue('')
            ->withMaxAge(-1)
            ->withHttpOnly(true));
        return $response;
    }

    /**
     * @param RequestInterface $request
     * @return string|null
     */
    public function getSessionIdFromRequestCookies(RequestInterface $request)
    {
        $sessionId = FigRequestCookies::get($request, $this->sessionCookieName)->getValue();
        return $sessionId;
    }

//    /**
//     * @param string $sessionId
//     * @return int
//     * @throws \Psr\SimpleCache\InvalidArgumentException
//     */
//    private function resolveSessionIdToUserId(string $sessionId): int
//    {
//        return $this->sessionCache->get($sessionId, 0);
//    }

    /**
     * @param RequestInterface $request
     * @return int
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getLoggedUserId(RequestInterface $request): int
    {
        $sessionId = $this->getSessionIdFromRequestCookies($request);
        if ($sessionId !== null) {
            return $this->sessionCache->get($sessionId, 0);
        }
        return 0;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private static function generateSessionId(): string
    {
        static $alphabet;
        if (!$alphabet) {
            $alphabet = array_merge(
                range('a', 'z'),
                range('A', 'Z'),
                range('0', '9')
            );
        }
        $sessionId = '';
        for ($i = 0; $i < self::$sessionIdLength; $i++) {
            $sessionId .= $alphabet[random_int(0, count($alphabet) - 1)];
        }
        return $sessionId;
    }
}

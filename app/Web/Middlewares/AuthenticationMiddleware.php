<?php

declare(strict_types = 1);

namespace App\Web\Middlewares;

use App\Web\Components\Authentication\CookieAuthenticator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\RedirectResponse;

class AuthenticationMiddleware implements MiddlewareInterface
{
    const LOGGED_USER_ID_ATTRIBUTE = 'loggedUserId';

    /**
     * @var CookieAuthenticator
     */
    private $authenticator;

    /**
     * @var string
     */
    private $loginUrl;

    /**
     * @var array
     */
    private $needAuthHandlers;

    public function __construct(CookieAuthenticator $authenticator, string $loginUrl, array $needAuthHandlers)
    {
        $this->authenticator = $authenticator;
        $this->loginUrl = $loginUrl;
        $this->needAuthHandlers = $needAuthHandlers;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $loggedUserId = $this->authenticator->getLoggedUserId($request);

        if ($loggedUserId != 0) {
            $request = $request->withAttribute(self::LOGGED_USER_ID_ATTRIBUTE, $loggedUserId);
        } elseif (in_array($request->getAttribute('request-handler'), $this->needAuthHandlers)) {
            return new RedirectResponse($this->loginUrl);
        }

        $response = $handler->handle($request);

        $response = $this->authenticator->prolongateSession($request, $response);

        return $response;
    }

}

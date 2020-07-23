<?php

declare(strict_types = 1);

namespace App\Web\Middlewares;

use App\ResourceManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TemplateMiddleware implements MiddlewareInterface
{
    /**
     * @var ResourceManager
     */
    private $resources;

    /**
     * Constructor
     *
     * @param ResourceManager $resources
     */
    public function __construct(ResourceManager $resources)
    {
        $this->resources = $resources;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Logged user

        $loggedUserId = $request->getAttribute(AuthenticationMiddleware::LOGGED_USER_ID_ATTRIBUTE);
        if ($loggedUserId) {
            $loggedUser = $this->resources->getAtlas()->select(\App\DataSource\User\User::class)
                ->where('id = ', $loggedUserId)
                ->fetchRecord();
        } else {
            $loggedUser = null;
        }

        // CSRF-token

        $cookieAuthenticator = $this->resources->getCookieAuthenticator();
        $csrfToken = $cookieAuthenticator->getSessionIdFromRequestCookies($request);

        // Set data to template engine

        $this->resources->getTemplateEngine()->addData([
            'loggedUser' => $loggedUser,
            'csrfToken' => $csrfToken,
            'request' => $request,
        ]);
        return $handler->handle($request);
    }
}

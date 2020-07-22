<?php

declare(strict_types = 1);

namespace App\Web\Actions\Auth;

use App\Exceptions\Http\Http400BadRequestException;
use App\Web\Actions\AbstractAction;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class LogoutAction extends AbstractAction
{
    /**
     * @param ServerRequest $request
     * @return \Psr\Http\Message\ResponseInterface|Response\HtmlResponse|Response\RedirectResponse
     * @throws \Aura\Router\Exception\RouteNotFound
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws Http400BadRequestException
     */
    public function __invoke(ServerRequest $request)
    {
        $cookieAuthenticator = $this->resources->getCookieAuthenticator();

        $loggedUserId = $cookieAuthenticator->getLoggedUserId($request);

        if (!$loggedUserId) {
            $response = new Response\RedirectResponse($this->resources->getWebRouter()->getGenerator()->generate('auth.login'));
            $response = $cookieAuthenticator->killSession($request, $response);
            return $response;
        }

        if ($request->getMethod() === 'POST') {
            $formData = $request->getParsedBody();

            if (($formData['csrf_token'] ?? null) !== $cookieAuthenticator->getSessionIdFromRequestCookies($request)) {
                throw new Http400BadRequestException('POST request must contain valid csrf_token to prevent CSRF-attack');
            }

            $response = new Response\RedirectResponse($this->resources->getWebRouter()->getGenerator()->generate('auth.login'));
            $response = $cookieAuthenticator->killSession($request, $response);
            return $response;
        }

        $response = new Response\HtmlResponse(
            $this->resources->getTemplateEngine()->render('auth/logout', [
                'csrfToken' => $cookieAuthenticator->getSessionIdFromRequestCookies($request),
            ])
        );
        return $response;
    }

}

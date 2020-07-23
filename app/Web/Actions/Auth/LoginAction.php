<?php

declare(strict_types = 1);

namespace App\Web\Actions\Auth;

use App\DataSource\User\User;
use App\DataSource\User\UserRecord;
use App\Services\UserService;
use App\Web\Actions\AbstractAction;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class LoginAction extends AbstractAction
{
    /**
     * @param ServerRequest $request
     * @return \Psr\Http\Message\ResponseInterface|Response\HtmlResponse|Response\RedirectResponse
     * @throws \Aura\Router\Exception\RouteNotFound
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function __invoke(ServerRequest $request)
    {
        $cookieAuthenticator = $this->resources->getCookieAuthenticator();
        if ($request->getMethod() === 'POST') {
            $formData = $request->getParsedBody();

            $userService = new UserService($this->resources->getAtlas());
            $user = $userService->authorizeUser($formData['username'], $formData['password']);
            if ($user instanceof UserRecord) {
                $redirectUrl = !array_key_exists('redirect', $request->getQueryParams())
                    ? $this->resources->getWebRouter()->getGenerator()->generate('profile', ['user' => $user->username])
                    : $request->getQueryParams()['redirect'];
                $response = new Response\RedirectResponse($redirectUrl);
                $response = $cookieAuthenticator->createSession($response, (int) $user->id);
                return $response;
            } else {
                $error = 'Username or password incorrect';
            }
        } else {
            $error = null;
        }

        $loggedUserId = $cookieAuthenticator->getLoggedUserId($request);
        /** @var null|UserRecord $loggedUser */
        $loggedUser = $loggedUserId ? $this->resources->getAtlas()->fetchRecord(User::class, $loggedUserId) : null;
        $response = new Response\HtmlResponse(
            $this->resources->getTemplateEngine()->render('auth/login', [
                'username' => $formData['username'] ?? '',
                'password' => $formData['password'] ?? '',
                'error' => $error,
                'loggedUser' => $loggedUser,
                'csrfToken' => $cookieAuthenticator->getSessionIdFromRequestCookies($request),
            ])
        );
        return $response;
    }

}

<?php

declare(strict_types = 1);

namespace App\Web\Actions\Auth;

use App\DataSource\User\User;
use App\DataSource\User\UserRecord;
use App\Exceptions\Http\Http400BadRequestException;
use App\Services\UserService;
use App\Web\Actions\AbstractAction;
use App\Web\Actions\FormTrait;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

/**
 * RegisterAction
 *
 * @TODO add CAPTCHA
 * @TODO add check if email or password begins or ends with space
 * @TODO add check that username is not valid e-mail
 */
class RegisterAction extends AbstractAction
{
    use FormTrait;

    /**
     * @param ServerRequest $request
     * @return \Psr\Http\Message\ResponseInterface|Response\HtmlResponse|Response\RedirectResponse
     * @throws \Aura\Router\Exception\RouteNotFound
     * @throws Http400BadRequestException
     * @throws \Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function __invoke(ServerRequest $request)
    {
        if ($loggedUserId = $this->resources->getCookieAuthenticator()->getLoggedUserId($request)) {
            /** @var UserRecord $loggedUser */
            $loggedUser = $this->resources->getAtlas()->fetchRecord(User::class, $loggedUserId);
            $response = new Response\RedirectResponse($this->resources->getWebRouter()->getGenerator()->generate('profile', ['user' => $loggedUser->username]));
            return $response;
        }

        if ($request->getMethod() === 'POST') {
            $formData = $request->getParsedBody();

            self::checkIfAllMandatoryFieldsArePresent($formData, ['username', 'password', 'password_again', 'email']);
            self::checkIfSomeFieldsUnexpected($formData, ['username', 'password', 'password_again', 'email']);

            $errors = $this->validateFormData($formData);

            if (count($errors) == 0) {
                $userService = new UserService($this->resources->getAtlas());
                $userService->createUser($formData['username'], $formData['password'], $formData['email']);
                $response = new Response\RedirectResponse($this->resources->getWebRouter()->getGenerator()->generate('auth.login'));
                return $response;
            }
        } else {
            $errors = [];
        }

        $response = new Response\HtmlResponse(
            $this->resources->getTemplateEngine()->render('auth/register', [
                'username' => $formData['username'] ?? '',
                'password' => $formData['password'] ?? '',
                'passwordAgain' => $formData['password_again'] ?? '',
                'email' => $formData['email'] ?? '',
                'errors' => $errors,
            ])
        );
        return $response;
    }

    private function validateFormData(array $formData): array
    {
        $usernameMinLength = 6;
        $usernameMaxLength = 32;
        $emailMinLength = 6;
        $emailMaxLength = 100;
        $passwordMinLength = 6;
        $passwordMaxLength = 100;

        $errors = [];

        // username

        if ($formData['username'] == '') {
            $errors['username'][] = "Username is required field";
        }

        if (mb_strlen($formData['username'], 'UTF-8') < $usernameMinLength) {
            $errors['username'][] = "Username should be minimum {$usernameMinLength} chars length";
        }
        if (mb_strlen($formData['username'], 'UTF-8') > $usernameMaxLength) {
            $errors['username'][] = "Username should be maximum {$usernameMaxLength} chars length";
        }

        $usersCount = $this->resources->getAtlas()
            ->select(User::class)
            ->where('username = ', $formData['username'])
            ->fetchCount();

        if ($usersCount != 0) {
            $errors['username'][] = "Username \"{$formData['username']}\" is already registered";
        }

        // password

        if ($formData['password'] == '') {
            $errors['password'][] = "Password is required field";
        }

        if (mb_strlen($formData['password'], 'UTF-8') < $passwordMinLength) {
            $errors['password'][] = "Password should be minimum {$passwordMinLength} chars length";
        }
        if (mb_strlen($formData['password'], 'UTF-8') > $passwordMaxLength) {
            $errors['password'][] = "Password should be maximum {$passwordMaxLength} chars length";
        }

        // password_again

        if ($formData['password_again'] == '') {
            $errors['password_again'][] = "Password again is required field";
        }

        if ($formData['password_again'] != $formData['password']) {
            $errors['password_again'][] = "Passwords are not match";
        }

        // email

        if ($formData['email'] == '') {
            $errors['email'][] = "E-mail is required field";
        }

        if (mb_strlen($formData['email'], 'UTF-8') < $emailMinLength) {
            $errors['email'][] = "E-mail should be minimum {$emailMinLength} chars length";
        }
        if (mb_strlen($formData['email'], 'UTF-8') > $emailMaxLength) {
            $errors['email'][] = "E-mail should be maximum {$emailMaxLength} chars length";
        }

        $usersCount = $this->resources->getAtlas()
            ->select(User::class)
            ->where('email = ', $formData['email'])
            ->fetchCount();

        if ($usersCount != 0) {
            $errors['email'][] = "E-mail \"{$formData['email']}\" is already registered";
        }

        return $errors;
    }

}

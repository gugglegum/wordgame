<?php

declare(strict_types = 1);

namespace App\Web\Actions;

use App\Exceptions\Http\Http403ForbiddenException;
use App\Exceptions\Http\Http404NotFoundException;
use App\ResourceManager;
use App\Web\Middlewares\AuthenticationMiddleware;
use Zend\Diactoros\ServerRequest;

/**
 * Trait ProfileTrait
 *
 * @property ResourceManager $resources
 * @package App\Web\actions\Profile
 */
trait ProfileTrait
{
    /**
     * @param ServerRequest $request
     * @return \App\DataSource\User\UserRecord
     */
    protected function getRequestedUser(ServerRequest $request): \App\DataSource\User\UserRecord
    {
        $user = $this->resources->getAtlas()->select(\App\DataSource\User\User::class)
            ->where('username = ', (string) $request->getAttribute('user'))
            ->fetchRecord();
        if ($user instanceof \App\DataSource\User\UserRecord) {
            return $user;
        } else {
            throw new Http404NotFoundException();
        }
    }

    /**
     * @param ServerRequest $request
     * @return int|null
     */
    protected function getLoggedUserId(ServerRequest $request): ?int
    {
        return $request->getAttribute(AuthenticationMiddleware::LOGGED_USER_ID_ATTRIBUTE);
    }

    /**
     * @param ServerRequest $request
     * @return \App\DataSource\User\UserRecord
     */
    protected function getLoggedUser(ServerRequest $request): \App\DataSource\User\UserRecord
    {
        $user = $this->resources->getAtlas()->select(\App\DataSource\User\User::class)
            ->where('id = ', $this->getLoggedUserId($request))
            ->fetchRecord();
        if ($user instanceof \App\DataSource\User\UserRecord) {
            return $user;
        } else {
            throw new Http404NotFoundException();
        }
    }

    /**
     * Returns whether current logged user is the same as $user
     *
     * @param ServerRequest $request
     * @param \App\DataSource\User\UserRecord $user
     * @return bool
     */
    protected function isOwnerUser(ServerRequest $request, \App\DataSource\User\UserRecord $user): bool
    {
        return $request->getAttribute(AuthenticationMiddleware::LOGGED_USER_ID_ATTRIBUTE) == $user->id;
    }

    /**
     * Checks if current logged user is the same as $user, if not - throw Http403ForbiddenException
     *
     * @param ServerRequest $request
     * @param \App\DataSource\User\UserRecord $user
     */
    protected function accessOnlyForOwnerUser(ServerRequest $request, \App\DataSource\User\UserRecord $user)
    {
        if (!$this->isOwnerUser($request, $user)) {
            throw new Http403ForbiddenException();
        }
    }
}

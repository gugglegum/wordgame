<?php

declare(strict_types = 1);

namespace App\Web\Actions\Profile;

use App\Web\Actions\AbstractAction;
use App\Web\Actions\ProfileTrait;
use App\Web\Middlewares\AuthenticationMiddleware;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class ProfileViewAction extends AbstractAction
{
    use ProfileTrait;

    public function __invoke(ServerRequest $request)
    {
        $user = $this->getRequestedUser($request);

        return new Response\HtmlResponse(
            $this->resources->getTemplateEngine()->render('profile/view', [
                'user' => $user,
//                'isOwnerUser' => $this->isOwnerUser($request, $user),
            ])
        );
    }
}

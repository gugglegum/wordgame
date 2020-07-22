<?php

declare(strict_types = 1);

namespace App\Web\Actions;

use App\DataSource\User\User;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class StartAction extends AbstractAction
{
    public function __invoke(ServerRequest $request)
    {
        return new Response\HtmlResponse(
            $this->resources->getTemplateEngine()->render('start', [
            ])
        );
    }

}

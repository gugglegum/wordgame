<?php

declare(strict_types = 1);

namespace App\Web\Middlewares;

use gugglegum\I18n\Translate\Translate;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class I18nMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        Translate::setLanguage('RU');
        return $handler->handle($request);
    }
}

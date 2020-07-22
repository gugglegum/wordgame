<?php

declare(strict_types = 1);

namespace App\Web\Middlewares;

use App\Exceptions\Http\HttpException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;

class HttpErrorMiddleware implements MiddlewareInterface
{
    /**
     * @var \League\Plates\Engine
     */
    private $templateEngine;

    /**
     * HttpErrorMiddleware constructor.
     * @param \League\Plates\Engine $templateEngine
     */
    public function __construct(\League\Plates\Engine $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $response = $handler->handle($request);
        } catch (HttpException $e) {
            if ($e->getCode() === 400) {
                $response = new HtmlResponse(
                    $this->templateEngine->render('http-error/400', [
                        'details' => $e->getMessage(),
                    ]),
                    $e->getCode()
                );
            } else {
                // Maybe Factory::createResponse(404); ?
                $response = new HtmlResponse("<h1>HTTP {$e->getCode()}</h1>", $e->getCode());
            }
        }
        return $response;
    }

}

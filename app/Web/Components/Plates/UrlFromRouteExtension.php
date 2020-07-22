<?php

declare(strict_types = 1);

namespace App\Web\Components\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class UrlFromRouteExtension implements ExtensionInterface
{
    /**
     * @var \Aura\Router\RouterContainer
     */
    private $webRouter;

    /**
     * UrlFromRouteExtension constructor.
     * @param \Aura\Router\RouterContainer $webRouter
     */
    public function __construct(\Aura\Router\RouterContainer $webRouter)
    {
        $this->webRouter = $webRouter;
    }

    /**
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction('urlFromRoute', [$this, 'urlFromRoute']);
    }

    /**
     * @param string $routeName
     * @param array $data
     * @return string
     * @throws \Aura\Router\Exception\RouteNotFound
     */
    public function urlFromRoute(string $routeName, array $data = []): string
    {
        return $this->webRouter->getGenerator()->generate($routeName, $data);
    }
}

<?php

declare(strict_types = 1);

namespace App;

class ResourceManager
{
    /**
     * @var \Luracast\Config\Config
     */
    private $config;

    /**
     * @var \Aura\Router\RouterContainer
     */
    private $router;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var \Atlas\Orm\Atlas
     */
    private $atlas;

    /**
     * @return \Luracast\Config\Config
     */
    public function getConfig(): \Luracast\Config\Config
    {
        if ($this->config === null) {
            $dotenv = new \Dotenv\Dotenv(__DIR__ . '/..');
            $dotenv->overload();
            $dotenv->required('DB_HOST')->notEmpty();
            $this->config = \Luracast\Config\Config::init(__DIR__ . '/../config');
        }
        return $this->config;
    }

    /**
     * @return \Aura\Router\RouterContainer
     */
    public function getWebRouter(): \Aura\Router\RouterContainer
    {
        if ($this->router === null) {
            $this->router = new \Aura\Router\RouterContainer();
            $map = $this->router->getMap();
            $map->get('start', '/', Web\Actions\StartAction::class);

            // Auth
            $map->route('auth.login', '/login', Web\Actions\Auth\LoginAction::class)->allows(['GET', 'POST']);
            $map->route('auth.register', '/register', Web\Actions\Auth\RegisterAction::class)->allows(['GET', 'POST']);
            $map->route('auth.logout', '/logout', Web\Actions\Auth\LogoutAction::class)->allows(['GET', 'POST']);

            // Profile
            $map->get('profile', '/{user}', Web\Actions\Profile\ProfileViewAction::class);

            // Game
            $map->post('game.create', '/create-game', Web\Actions\Game\GameCreate::class);
            $map->get('game.view', '/game/{gameCode}', Web\Actions\Game\GameView::class);
            $map->post('game.join', '/game/{gameCode}/join', Web\Actions\Game\GameJoin::class);
            $map->post('game.leave', '/game/{gameCode}/leave', Web\Actions\Game\GameLeave::class);
            $map->get('game.ajax.events', '/game/{gameCode}/events', Web\Actions\Game\GameEvents::class);
            $map->post('game.ajax.word-submit', '/game/{gameCode}/word-submit', Web\Actions\Game\GameWordSubmit::class);

        }
        return $this->router;
    }

    /**
     * @return \mindplay\middleman\Dispatcher
     * @throws \Aura\Router\Exception\RouteNotFound
     */
    public function getDispatcher(): \mindplay\middleman\Dispatcher
    {
        $dispatcher = new \mindplay\middleman\Dispatcher([
            new \Middlewares\AuraRouter($this->getWebRouter()),
            new Web\Middlewares\AuthenticationMiddleware(
                $this->getCookieAuthenticator(),
                $this->getWebRouter()->getGenerator()->generate('auth.login'),
                [
                    Web\Actions\Auth\LogoutAction::class,
                    Web\Actions\Game\GameCreate::class,
                    Web\Actions\Game\GameView::class,
                    Web\Actions\Game\GameJoin::class,
                    Web\Actions\Game\GameLeave::class,
                ]
            ),
            new Web\Middlewares\HttpErrorMiddleware($this->getTemplateEngine()),
            new Web\Middlewares\I18nMiddleware(),
            new \Middlewares\RequestHandler(new \Middlewares\Utils\RequestHandlerContainer([$this])),
        ]);
        return $dispatcher;
    }

    /**
     * @param bool $newInstance
     * @return \PDO
     */
    public function getPdo(bool $newInstance = false): \PDO
    {
        $dbConfig = $this->getConfig()->get('database.connections.master');

        if ($this->pdo === null || $newInstance) {

            $pdo = new \PDO(
                'mysql:dbname=' . $dbConfig['database'] . ';host=' . $dbConfig['host'] . ($dbConfig['port'] ? ';port=' . $dbConfig['port'] : ''),
                $dbConfig['username'],
                $dbConfig['password'],
                [
                    // STRICT_TRANS_TABLES,​ERROR_FOR_DIVISION_BY_ZERO,​NO_AUTO_CREATE_USER,​NO_ENGINE_SUBSTITUTION
                    \PDO::MYSQL_ATTR_INIT_COMMAND =>
                        "SET NAMES '{$dbConfig['charset']}' COLLATE '{$dbConfig['collation']}', @@session.sql_mode='" . implode(',', [
                            'STRICT_TRANS_TABLES',
                            'ERROR_FOR_DIVISION_BY_ZERO',
                            'NO_AUTO_CREATE_USER',
                            'NO_ENGINE_SUBSTITUTION',
                            'NO_ZERO_DATE,NO_ZERO_IN_DATE',
                        ]) . "'",
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]
            );
            // This is necessary to get native types in results (for example, INT columns as PHP integers)
            $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            if ($this->pdo === null) {
                $this->pdo = $pdo;
            }
            return $pdo;
        }
        return $this->pdo;
    }

    /**
     * @param bool $newInstance
     * @return \Atlas\Orm\Atlas
     */
    public function getAtlas(bool $newInstance = false): \Atlas\Orm\Atlas
    {
        if ($this->atlas === null || $newInstance) {
            $atlas = \Atlas\Orm\Atlas::new($this->getPdo());

            if ($this->atlas === null) {
                $this->atlas = $atlas;
            }
            return $atlas;
        }
        return $this->atlas;
    }

    /**
     * @return \League\Plates\Engine
     */
    public function getTemplateEngine(): \League\Plates\Engine
    {
        $templateEngine = new \League\Plates\Engine(__DIR__ . '/web/views', 'phtml');
        $templateEngine->loadExtension(new Web\Components\Plates\UrlFromRouteExtension($this->getWebRouter()));
        return $templateEngine;
    }

    /**
     * @return \Psr\SimpleCache\CacheInterface
     */
    public function getSessionCache(): \Psr\SimpleCache\CacheInterface
    {
        return new \Kodus\Cache\FileCache(__DIR__ . '/../temp/cache/session', 3600 * 24 * 14);
    }

    /**
     * @return Web\Components\Authentication\CookieAuthenticator
     */
    public function getCookieAuthenticator(): Web\Components\Authentication\CookieAuthenticator
    {
        $authenticator = new Web\Components\Authentication\CookieAuthenticator($this->getSessionCache());
        return $authenticator;
    }
}

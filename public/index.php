<?php

declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

(function() {
    $resources = new \App\ResourceManager();
    $dispatcher = $resources->getDispatcher();
    $request = \Zend\Diactoros\ServerRequestFactory::fromGlobals();
    $response = $dispatcher->dispatch($request);
    $emitter = new Zend\Diactoros\Response\SapiEmitter();
    $emitter->emit($response);
})();

<?php

declare(strict_types = 1);

namespace App\Web\Actions;

use App\ResourceManager;
use Zend\Diactoros\ServerRequest;

abstract class AbstractAction
{
    /**
     * @var ResourceManager
     */
    protected $resources;

    public function __construct(ResourceManager $resourceManager)
    {
        $this->resources = $resourceManager;
    }

    abstract public function __invoke(ServerRequest $request);
}

<?php

namespace Waxwink\Orbis\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;
use Weebel\Contracts\EventDispatcher as EventDispatcherInterface;
use Weebel\Contracts\Container;
use Weebel\Dispatcher\EventDispatcher;

class EventDispatcherProvider
{
    public function __construct(protected Container $container)
    {
    }

    public function __invoke(): void
    {
        $this->container->alias(PsrEventDispatcherInterface::class, EventDispatcher::class);
        $this->container->alias(EventDispatcherInterface::class, EventDispatcher::class);
    }
}

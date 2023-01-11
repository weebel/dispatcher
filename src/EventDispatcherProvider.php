<?php

namespace Weebel\Dispatcher;

use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;
use Weebel\Contracts\Bootable;
use Weebel\Contracts\EventDispatcher as EventDispatcherInterface;
use Weebel\Contracts\Container;
use Weebel\Dispatcher\EventDispatcher;

class EventDispatcherProvider implements Bootable
{
    public function __construct(protected Container $container)
    {
    }

    public function boot(): void
    {
        $this->container->alias(PsrEventDispatcherInterface::class, EventDispatcher::class);
        $this->container->alias(EventDispatcherInterface::class, EventDispatcher::class);
    }
}

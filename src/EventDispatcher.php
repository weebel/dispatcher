<?php

namespace Weebel\Dispatcher;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Weebel\Contracts\EventDispatcher as EventDispatcherInterface;
use Weebel\Contracts\Queueable;
use Weebel\Contracts\QueueManager;

class EventDispatcher implements EventDispatcherInterface, ListenerProviderInterface
{
    protected array $listeners = [];

    public function __construct(protected ContainerInterface $container, protected ?QueueManager $queueConnection = null)
    {
    }

    public function dispatch(object $event): object
    {
        if (!array_key_exists(get_class($event), $this->listeners)) {
            return $event;
        }

        foreach ($this->listeners[get_class($event)] as $listener) {
            $listener = $this->container->get($listener);
            if ($listener instanceof Queueable) {
                $this->queueListener($listener, $event);
                continue;
            }
            $listener($event);
        }

        return $event;
    }

    public function dispatchByTag(string $tag, mixed $payload = null)
    {
        if (!array_key_exists($tag, $this->listeners)) {
            return $payload;
        }

        foreach ($this->listeners[$tag] as $listener) {
            $listener = $this->container->get($listener);
            if (!$payload && $this->container->has($tag)) {
                $payload = $this->container->get($tag);
            }

            if ($listener instanceof Queueable) {
                $this->queueListener($listener, $payload);
                continue;
            }
            $listener($payload);
        }
    }

    public function addListener(string $event, string $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    public function getListeners(): array
    {
        return $this->listeners;
    }

    private function queueListener(Queueable $listener, object $event = null): void
    {
        // Maybe the queue manager is not loaded as it is not mandatory
        if (!$this->queueConnection) {
            return;
        }

        $this->queueConnection->addJob($listener, [$event]);
    }

    public function getListenersForEvent(object $event): iterable
    {
        return array_key_exists(get_class($event), $this->listeners) ? $this->listeners[get_class($event)] : [];
    }
}

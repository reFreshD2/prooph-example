<?php

namespace App\Domain\Message\Event;

use Prooph\EventSourcing\AggregateChanged;

class MessageWasSent extends AggregateChanged
{
    private string $id;

    public static function createWith(string $id): self
    {
        $event = self::occur($id, ['id' => $id]);

        $event->id = $id;

        return $event;
    }

    public function getId(): string
    {
        return $this->id ?? $this->payload['id'];
    }
}

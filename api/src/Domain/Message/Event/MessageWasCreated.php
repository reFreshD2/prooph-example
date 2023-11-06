<?php

namespace App\Domain\Message\Event;

use Prooph\EventSourcing\AggregateChanged;

class MessageWasCreated extends AggregateChanged
{
    private string $id;
    private string $from;
    private string $value;

    public static function createWith(string $id, string $from, string $value): self
    {
        $event = self::occur($id, [
            'id' => $id,
            'from' => $from,
            'value' => $value,
        ]);

        $event->id = $id;
        $event->from = $from;
        $event->value = $value;

        return $event;
    }

    public function getId(): string
    {
        return $this->id ?? $this->payload['id'];
    }

    public function getFrom(): string
    {
        return $this->from ?? $this->payload['from'];
    }

    public function getValue(): string
    {
        return $this->value ?? $this->payload['value'];
    }
}

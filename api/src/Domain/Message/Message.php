<?php

namespace App\Domain\Message;

use App\Domain\Message\Event\MessageWasCreated;
use App\Domain\Message\Event\MessageWasSent;
use DomainException;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Ramsey\Uuid\Uuid;

class Message extends AggregateRoot
{
    private string $id;
    private string $from;
    private string $value;
    private bool $wasSend = false;

    public static function create(string $from, string $value): self
    {
        $message = new self();
        $message->recordThat(MessageWasCreated::createWith(Uuid::uuid4()->toString(), $from, $value));

        return $message;
    }

    public function send(): void
    {
        $this->recordThat(MessageWasSent::createWith($this->id));
    }

    protected function aggregateId(): string
    {
        return $this->id;
    }

    protected function apply(AggregateChanged $event): void
    {
        if ($event instanceof MessageWasCreated) {
            $this->id = $event->getId();
            $this->from = $event->getFrom();
            $this->value = $event->getValue();
            return;
        }

        if ($event instanceof MessageWasSent) {
            $this->wasSend = true;
            return;
        }

        throw new DomainException('Unsupported event');
    }
}

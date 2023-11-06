<?php

namespace App\Infrastructure\Message\Repository;

use App\Domain\Message\Message;
use App\Domain\Message\MessageStorageInterface;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\EventStore;

class MessageRepository extends AggregateRepository implements MessageStorageInterface
{
    public function __construct(EventStore $eventStore)
    {
        parent::__construct(
            $eventStore,
            AggregateType::fromAggregateRootClass(Message::class),
            new AggregateTranslator()
        );
    }

    public function save(Message $message): void
    {
        $this->saveAggregateRoot($message);
    }
}

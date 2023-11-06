<?php

namespace App\Infrastructure\Message\Projection;

use App\Domain\Message\Event\MessageWasCreated;
use App\Domain\Message\Event\MessageWasSent;
use App\Infrastructure\Message\Query\MessageRepositoryInterface;
use App\Infrastructure\Message\Query\View\MessageView;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventStore\Projection\AbstractReadModel;

class MessageReadModel extends AbstractReadModel
{
    private const DB_TABLE = 'message';
    private Schema $schema;
    private MessageRepositoryInterface $repository;
    private EntityManagerInterface $entityManager;

    /**
     * @throws Exception
     */
    public function __construct(
        Connection $connection,
        MessageRepositoryInterface $repository,
        EntityManagerInterface $entityManager
    ) {
        $this->schema = $connection->createSchemaManager()->introspectSchema();
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(AggregateChanged $event)
    {
        if ($event instanceof MessageWasCreated) {
            $message = new MessageView($event->getId(), $event->getFrom(), $event->getValue());
            $this->repository->add($message);
            $this->entityManager->flush();
            return;
        }

        if ($event instanceof MessageWasSent) {
            $message = $this->repository->findById($event->getId());
            if ($message === null) {
                throw new DomainException('Message not found');
            }
            $message->send();
            $this->repository->add($message);
            $this->entityManager->flush();
            return;
        }

        throw new DomainException('Unexpected event');
    }

    /**
     * @throws SchemaException
     */
    public function init(): void
    {
        $this->schema->createTable(self::DB_TABLE);
    }

    public function isInitialized(): bool
    {
        return $this->schema->hasTable(self::DB_TABLE);
    }

    /**
     * @throws SchemaException
     */
    public function reset(): void
    {
        $this->schema->dropTable(self::DB_TABLE);
        $this->schema->createTable(self::DB_TABLE);
    }

    public function delete(): void
    {
        $this->schema->dropTable(self::DB_TABLE);
    }
}

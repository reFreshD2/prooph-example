<?php

namespace App\Infrastructure\Message\Query\Repository;

use App\Infrastructure\Message\Query\MessageRepositoryInterface;
use App\Infrastructure\Message\Query\View\MessageView;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineMessageRepository implements MessageRepositoryInterface
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(MessageView $view): void
    {
        $this->entityManager->persist($view);
    }

    public function findById(string $id): ?MessageView
    {
        return $this->entityManager->getRepository(MessageView::class)->find($id);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(MessageView::class)->findAll();
    }
}

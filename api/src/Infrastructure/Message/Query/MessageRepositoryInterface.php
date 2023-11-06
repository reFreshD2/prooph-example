<?php

namespace App\Infrastructure\Message\Query;

use App\Infrastructure\Message\Query\View\MessageView;

interface MessageRepositoryInterface
{
    public function add(MessageView $view): void;

    public function findById(string $id): ?MessageView;

    /**
     * @return MessageView[]
     */
    public function findAll(): array;
}

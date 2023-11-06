<?php

namespace App\Domain\Message;

interface MessageStorageInterface
{
    public function save(Message $message): void;
}

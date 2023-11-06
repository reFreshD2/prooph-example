<?php

namespace App\Application\Message\Send;

use App\Application\Exception\ValidationException;
use App\Domain\Message\Message;
use App\Domain\Message\MessageStorageInterface;

class SendMessageCommandHandler
{
    private MessageStorageInterface $storage;

    public function __construct(MessageStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @throws ValidationException
     */
    public function __invoke(SendMessageCommand $command): void
    {
        $message = Message::create($command->user(), $command->message());
        $message->send();
        $this->storage->save($message);
    }
}

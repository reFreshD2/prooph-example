<?php

namespace App\Application\Message\Send;

use App\Application\Exception\ValidationException;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class SendMessageCommand extends Command implements PayloadConstructable
{
    use PayloadTrait;

    /**
     * @throws ValidationException
     */
    public function message(): string
    {
        if (!isset($this->payload['message'])) {
            throw new ValidationException('Message doesn\'t exist');
        }

        return $this->payload['message'];
    }

    /**
     * @throws ValidationException
     */
    public function user(): string
    {
        if (!isset($this->payload['user'])) {
            throw new ValidationException('User doesn\'t exist');
        }

        return $this->payload['user'];
    }
}

<?php

namespace App\Infrastructure\Message\Query\View;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="message")
 */
class MessageView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private string $id;

    /**
     * @ORM\Column(type="string",name="from_user")
     */
    private string $from;

    /**
     * @ORM\Column(type="string")
     */
    private string $value;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $wasSend;

    public function __construct(string $id, string $from, string $value, bool $wasSend = false)
    {
        $this->id = $id;
        $this->from = $from;
        $this->value = $value;
        $this->wasSend = $wasSend;
    }

    public function send(): void
    {
        $this->wasSend = true;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'from' => $this->from,
            'message' => $this->value,
            'wasSent' => $this->wasSend,
        ];
    }
}

<?php

namespace App\UI\Http;

use App\Infrastructure\Message\Query\MessageRepositoryInterface;
use App\Infrastructure\Message\Query\View\MessageView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReceiveMessageController extends AbstractController
{
    private MessageRepositoryInterface $repository;

    public function __construct(MessageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            array_map(
                static function (MessageView $view) {
                    return $view->toArray();
                    },
                $this->repository->findAll()
            ),
        );
    }
}

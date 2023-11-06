<?php

namespace App\UI\Http;

use App\Application\Exception\ValidationException;
use App\Application\Message\Send\SendMessageCommand;
use Prooph\ServiceBus\CommandBus;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SendMessageController extends AbstractController
{
    private CommandBus $commandBus;
    private LoggerInterface $logger;

    public function __construct(CommandBus $commandBus, LoggerInterface $logger)
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->cookies->get('user-id');
        if ($user === null) {
            $user = Uuid::uuid4()->toString();
        }

        try {
            $command = new SendMessageCommand([
                'message' => $request->get('message'),
                'user' => $user,
            ]);
            $this->commandBus->dispatch($command);
        } catch (ValidationException $exception) {
            $this->logger->error('validation error', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ]);
            return new JsonResponse(
                ['success' => false, 'error' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (Throwable $exception) {
            $this->logger->error('unexpected error', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ]);
            return new JsonResponse(
                ['success' => false, 'error' => $exception->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(['success' => true]);
    }
}

<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
class ExceptionListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        // Get the exception object from the event
        $exception = $event->getThrowable();

        // Log the exception message and additional details
        $this->logger->error('An error occurred', [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Optionally, you can customize the response displayed to the user
        $response = new Response();
        $response->setContent('Oops! An error occurred. The administrator has been notified.');
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        // Set the response to the event
        $event->setResponse($response);
    }
}
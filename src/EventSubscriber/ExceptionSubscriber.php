<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private $serializer;
    
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::EXCEPTION => [
                ['processException', 10]
            ],
        ];
    }

    public function processException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getException();
        //$statusCode = ($exception->getStatusCode() ? $exception->getStatusCode() : $exception->getCode());
        $message = [
            //'code' => $statusCode,
            'message' => $exception->getMessage()
        ];

        // Customize your response object to display the exception details
        $response = new Response();
        $response->setContent($this->serializer->serialize($message, 'json'));

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // sends the modified response object to the event
        $event->setResponse($response);
    }

}

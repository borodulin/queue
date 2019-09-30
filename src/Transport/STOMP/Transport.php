<?php

declare(strict_types=1);

namespace Borodulin\Queue\Transport\STOMP;

use Borodulin\Queue\MessageHandlerInterface;
use Borodulin\Queue\Transport\TransportInterface;
use Enqueue\Stomp\StompConnectionFactory;
use Enqueue\Stomp\StompContext;
use Interop\Queue\Exception;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;

class Transport implements TransportInterface
{
    /**
     * @var StompContext
     */
    private $context;

    public function __construct($config)
    {
        $factory = new StompConnectionFactory($config);
        $this->context = $factory->createContext();
    }

    public function consume(MessageHandlerInterface $consumer, $queueName = 'default'): void
    {
        $queue = $this->context->createQueue($queueName);

        $stompConsumer = $this->context->createConsumer($queue);

        while (true) {
            $message = $stompConsumer->receive();
            $stompMessage = new StompMessage($message);
            if ($consumer->handle($stompMessage)) {
                $stompConsumer->acknowledge($message);
            } else {
                $stompConsumer->reject($message);
            }
        }
    }

    /**
     * @param string $queueName
     * @param string $message
     * @param array $options
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function pushMessage(string $queueName, string $message, array $options = []): void
    {
        $message = $this->context->createMessage($message);

        $queue = $this->context->createQueue($queueName);

        $this->context->createProducer()->send($queue, $message);
    }
}

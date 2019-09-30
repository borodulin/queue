<?php

declare(strict_types=1);

namespace Borodulin\Queue\Transport\PhpAMQP;

use AMQPChannelException;
use AMQPConnectionException;
use AMQPEnvelope;
use AMQPEnvelopeException;
use AMQPExchangeException;
use AMQPQueue;
use AMQPQueueException;
use Borodulin\Queue\MessageHandlerInterface;
use Borodulin\Queue\Transport\TransportInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Throwable;

class Transport implements LoggerAwareInterface, TransportInterface
{
    use LoggerAwareTrait;

    /**
     * @var AMQP
     */
    private $amqp;

    /**
     * @var int
     */
    private $maxCount;

    /**
     * @var int
     */
    private $count = 0;

    public function __construct(
        $host = 'localhost',
        $port = 5672,
        $user = 'guest',
        $password = 'guest',
        $vhost = '/'
    ) {
        $this->amqp = new AMQP($host, $port, $user, $password, $vhost);
    }

    /**
     * @param MessageHandlerInterface $consumer
     * @param string $queueName
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPEnvelopeException
     * @throws AMQPExchangeException
     * @throws AMQPQueueException
     */
    public function consume(MessageHandlerInterface $consumer, $queueName = 'default'): void
    {
        $queue = $this->amqp->getQueue($queueName);

        $queue->consume(function (AMQPEnvelope $envelope, AMQPQueue $queue) use ($consumer) {
            $message = new AMQPMessage($envelope);
            try {
                $this->logger && $this->logger->debug('QUEUE message is received', [
                    'envelope' => $envelope,
                ]);
                if ($consumer->handle($message)) {
                    $this->logger && $this->logger->debug('QUEUE message is processed', [
                        'message' => $message
                    ]);
                    $queue->ack($envelope->getDeliveryTag());
                } else {
                    $this->logger && $this->logger->debug('QUEUE message is not processed', [
                        'message' => $message
                    ]);
                    $queue->nack($envelope->getDeliveryTag());
                }
            } catch (Throwable $exception) {
                $this->logger && $this->logger->error($exception);
                $queue->nack($envelope->getDeliveryTag());
            }
            $this->count++;
            if ($this->maxCount && $this->count > $this->maxCount) {
                return false;
            }
            return true;
        });
    }

    /**
     * @param string $queueName
     * @param string $message
     * @param array $options
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     * @throws AMQPQueueException
     */
    public function pushMessage(string $queueName, string $message, array $options = []): void
    {
        $this->amqp->getQueue($queueName);
        !isset($options['message_id']) && $options['message_id'] = uniqid('message', true);
        $this->logger && $this->logger->info('QUEUE message is pushed to queue', [
                'queueName' => $queueName,
                'options' => $options,
            ]);
        $this->amqp->sendJsonMessage($queueName, $message, null, $options);
    }

    /**
     * @return int|null
     */
    public function getMaxCount(): ?int
    {
        return $this->maxCount;
    }

    /**
     * @param int $maxCount
     * @return Transport
     */
    public function setMaxCount(?int $maxCount): self
    {
        $this->maxCount = $maxCount;
        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Borodulin\Queue\Transport\AmqpLib;

use Borodulin\Queue\MessageHandlerInterface;
use Borodulin\Queue\Transport\TransportInterface;
use ErrorException;
use PhpAmqpLib\Channel\AMQPChannel;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Throwable;

class Transport implements TransportInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var AmqpLib
     */
    private $amqpLib;

    public function __construct(
        $host = 'localhost',
        $port = 5672,
        $user = 'guest',
        $password = 'guest'
    ) {
        $this->amqpLib = new AmqpLib($host, $port, $user, $password);
    }


    /**
     * @param MessageHandlerInterface $consumer
     * @param string $queueName
     * @throws ErrorException
     */
    public function consume(MessageHandlerInterface $consumer, $queueName = 'default'): void
    {
        $callback = function ($msg) use ($consumer) {
            $message = new AmqpMessage($msg);
            /** @var AMQPChannel $channel */
            $channel = $msg->delivery_info['channel'] ?? null;
            if (!$channel) {
                $this->logger && $this->logger->emergency('QUEUE message error. Channel is not defined.', [
                    'envelope' => $msg,
                ]);
                return false;
            }
            try {
                $this->logger && $this->logger->debug('QUEUE message is received', [
                    'envelope' => $msg,
                ]);
                if ($consumer->handle($message)) {
                    $this->logger && $this->logger->debug('QUEUE message is processed', [
                        'message' => $message
                    ]);
                    $channel->basic_ack($msg->delivery_info['delivery_tag']);
                } else {
                    $this->logger && $this->logger->debug('QUEUE message is not processed', [
                        'message' => $message
                    ]);
                    $channel->basic_nack($msg->delivery_info['delivery_tag']);
                }
            } catch (Throwable $exception) {
                $this->logger && $this->logger->error($exception);
                $channel->basic_nack($msg->delivery_info['delivery_tag']);
            }
            return true;
        };
        $channel = $this->amqpLib->getChannel();

        $channel->basic_consume($queueName, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    public function pushMessage(string $queueName, string $message, array $options = []): void
    {
        $this->amqpLib->getQueue($queueName);
        !isset($options['message_id']) && $options['message_id'] = uniqid('message', true);
        $this->logger && $this->logger->info('QUEUE message is pushed to queue', [
            'queueName' => $queueName,
            'options' => $options,
        ]);
        $this->amqpLib->sendJsonMessage($queueName, $message, $options);
    }
}

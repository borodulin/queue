<?php

declare(strict_types=1);

namespace Borodulin\Queue\Job;

use Borodulin\Queue\MessageHandlerInterface;
use Borodulin\Queue\Transport\MessageInterface;
use Borodulin\Queue\Transport\TransportInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Throwable;

class JobMessageHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var TransportInterface
     */
    private $transport;

    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    public function handle(MessageInterface $message): bool
    {
        try {
            $job = unserialize($message->getBody());
        } catch (Throwable $exception) {
            $this->logger && $this->logger->error($exception);
            return false;
        }
        if (!$job instanceof JobInterface) {
            $this->logger && $this->logger->error('Message is not an instance of JobInterface');
            return false;
        }
        try {
            return $job->process($message);
        } catch (Throwable $exception) {
            $this->logger && $this->logger->error($exception);
            return false;
        }
    }
}

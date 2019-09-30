<?php

declare(strict_types=1);

namespace Borodulin\Queue;

use Borodulin\Queue\Transport\TransportInterface;

class Publisher implements PublisherInterface
{
    /**
     * @var TransportInterface
     */
    private $transport;

    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    public function pushMessage($message, string $queueName = 'default', array $options = []): void
    {
        if (!is_string($message)) {
            $message = serialize($message);
        }
        $this->transport->pushMessage($queueName, $message, $options);
    }
}

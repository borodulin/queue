<?php

declare(strict_types=1);

namespace Borodulin\Queue\Transport;

use Borodulin\Queue\MessageHandlerInterface;

interface TransportInterface
{
    public function consume(MessageHandlerInterface $consumer, $queueName = 'default'): void;

    public function pushMessage(string $queueName, string $message, array $options = []): void;
}

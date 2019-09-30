<?php

declare(strict_types=1);

namespace Borodulin\Queue;


interface PublisherInterface
{
    public function pushMessage($message, string $queueName = 'default', array $options = []): void;
}

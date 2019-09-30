<?php

declare(strict_types=1);

namespace Borodulin\Queue;

use Borodulin\Queue\Transport\MessageInterface;

interface MessageHandlerInterface
{
    public function handle(MessageInterface $message): bool;
}

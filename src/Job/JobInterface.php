<?php

declare(strict_types=1);

namespace Borodulin\Queue\Job;

use Borodulin\Queue\Transport\MessageInterface;

interface JobInterface
{
    public function process(MessageInterface $message): bool;
}

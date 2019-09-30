<?php

declare(strict_types=1);

namespace Borodulin\Queue\Transport;

interface MessageInterface
{
    /**
     * Get the body of the message.
     * @return string The contents of the message body.
     */
    public function getBody(): ?string;
}

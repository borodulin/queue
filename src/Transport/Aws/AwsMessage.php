<?php


namespace Borodulin\Queue\Transport\Aws;

use Borodulin\Queue\Transport\MessageInterface;

class AwsMessage implements MessageInterface
{
    /**
     * @var string
     */
    private $body;

    public function __construct(string $body)
    {
        $this->body = $body;
    }

    /**
     * Get the body of the message.
     * @return string The contents of the message body.
     */
    public function getBody(): ?string
    {
        return $this->body;
    }
}

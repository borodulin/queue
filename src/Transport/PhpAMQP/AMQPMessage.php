<?php

declare(strict_types=1);

namespace Borodulin\Queue\Transport\PhpAMQP;

use AMQPEnvelope;
use Borodulin\Queue\Transport\AmqpMessageInterface;

class AMQPMessage implements AmqpMessageInterface
{

    /**
     * @var AMQPEnvelope
     */
    private $envelope;

    public function __construct(AMQPEnvelope $envelope)
    {
        $this->envelope = $envelope;
    }


    /**
     * Get the body of the message.
     * @return string The contents of the message body.
     */
    public function getBody(): ?string
    {
        return $this->envelope->getBody();
    }

    /**
     * Get the routing key of the message.
     * @return string The message routing key.
     */
    public function getRoutingKey(): ?string
    {
        return $this->envelope->getRoutingKey();
    }

    /**
     * Get the consumer tag of the message.
     * @return string The consumer tag of the message.
     */
    public function getConsumerTag(): ?string
    {
        return $this->envelope->getConsumerTag();
    }

    /**
     * Get the delivery tag of the message.
     * @return string The delivery tag of the message.
     */
    public function getDeliveryTag(): ?string
    {
        return $this->envelope->getDeliveryTag();
    }

    /**
     * Get the exchange name on which the message was published.
     * @return string The exchange name on which the message was published.
     */
    public function getExchangeName(): ?string
    {
        return $this->envelope->getExchangeName();
    }

    /**
     * Whether this is a redelivery of the message.
     * Whether this is a redelivery of a message. If this message has been
     * delivered and AMQPEnvelope::nack() was called, the message will be put
     * back on the queue to be redelivered, at which point the message will
     * always return TRUE when this method is called.
     *
     * @return bool TRUE if this is a redelivery, FALSE otherwise.
     */
    public function isRedelivery(): bool
    {
        return $this->envelope->isRedelivery();
    }

    /**
     * Get a specific message header.
     * @param string $headerKey Name of the header to get the value from.
     * @return string|false The contents of the specified header or FALSE
     *                        if not set.
     */
    public function getHeader($headerKey)
    {
        return $this->envelope->getHeader($headerKey);
    }

    /**
     * Check whether specific message header exists.
     * @param string $headerKey Name of the header to check.
     * @return bool
     */
    public function hasHeader($headerKey): bool
    {
        return $this->envelope->hasHeader($headerKey);
    }

    /**
     * Get the message content type.
     * @return string The content type of the message.
     */
    public function getContentType(): ?string
    {
        return $this->envelope->getContentType();
    }

    /**
     * Get the content encoding of the message.
     * @return string The content encoding of the message.
     */
    public function getContentEncoding(): ?string
    {
        return $this->envelope->getContentEncoding();
    }

    /**
     * Get the headers of the message.
     * @return array An array of key value pairs associated with the message.
     */
    public function getHeaders(): array
    {
        return $this->envelope->getHeaders();
    }

    /**
     * Get the delivery mode of the message.
     * @return integer The delivery mode of the message.
     */
    public function getDeliveryMode(): int
    {
        return $this->envelope->getDeliveryMode();
    }

    /**
     * Get the priority of the message.
     * @return int The message priority.
     */
    public function getPriority(): int
    {
        return $this->envelope->getPriority();
    }

    /**
     * Get the message correlation id.
     * @return string The correlation id of the message.
     */
    public function getCorrelationId(): ?string
    {
        return $this->envelope->getCorrelationId();
    }

    /**
     * Get the reply-to address of the message.
     * @return string The contents of the reply to field.
     */
    public function getReplyTo(): ?string
    {
        return $this->envelope->getReplyTo();
    }

    /**
     * Get the expiration of the message.
     * @return string The message expiration.
     */
    public function getExpiration(): ?string
    {
        return $this->envelope->getExpiration();
    }

    /**
     * Get the message id of the message.
     * @return string The message id
     */
    public function getMessageId(): ?string
    {
        return $this->envelope->getMessageId();
    }

    /**
     * Get the timestamp of the message.
     * @return string The message timestamp.
     */
    public function getTimestamp(): ?string
    {
        return $this->envelope->getTimestamp();
    }

    /**
     * Get the message type.
     * @return string The message type.
     */
    public function getType(): ?string
    {
        return $this->envelope->getType();
    }

    /**
     * Get the message user id.
     * @return string The message user id.
     */
    public function getUserId(): ?string
    {
        return $this->envelope->getUserId();
    }

    /**
     * Get the application id of the message.
     * @return string The application id of the message.
     */
    public function getAppId(): ?string
    {
        return $this->envelope->getAppId();
    }

    /**
     * Get the cluster id of the message.
     * @return string The cluster id of the message.
     */
    public function getClusterId(): ?string
    {
        return $this->envelope->getClusterId();
    }
}

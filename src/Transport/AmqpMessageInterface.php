<?php

declare(strict_types=1);

namespace Borodulin\Queue\Transport;

interface AmqpMessageInterface extends MessageInterface
{
    /**
     * Get the routing key of the message.
     * @return string The message routing key.
     */
    public function getRoutingKey(): ?string;

    /**
     * Get the consumer tag of the message.
     * @return string The consumer tag of the message.
     */
    public function getConsumerTag(): ?string;

    /**
     * Get the delivery tag of the message.
     * @return string The delivery tag of the message.
     */
    public function getDeliveryTag(): ?string;

    /**
     * Get the exchange name on which the message was published.
     * @return string The exchange name on which the message was published.
     */
    public function getExchangeName(): ?string;

    /**
     * Whether this is a redelivery of the message.
     * Whether this is a redelivery of a message. If this message has been
     * delivered and AMQPEnvelope::nack() was called, the message will be put
     * back on the queue to be redelivered, at which point the message will
     * always return TRUE when this method is called.
     *
     * @return bool TRUE if this is a redelivery, FALSE otherwise.
     */
    public function isRedelivery(): bool;

    /**
     * Get a specific message header.
     * @param string $headerKey Name of the header to get the value from.
     * @return string|false The contents of the specified header or FALSE
     *                        if not set.
     */
    public function getHeader($headerKey);

    /**
     * Check whether specific message header exists.
     * @param string $headerKey Name of the header to check.
     * @return bool
     */
    public function hasHeader($headerKey): bool;

    /**
     * Get the message content type.
     * @return string The content type of the message.
     */
    public function getContentType(): ?string;

    /**
     * Get the content encoding of the message.
     * @return string The content encoding of the message.
     */
    public function getContentEncoding(): ?string;

    /**
     * Get the headers of the message.
     * @return array An array of key value pairs associated with the message.
     */
    public function getHeaders(): array;

    /**
     * Get the delivery mode of the message.
     * @return integer The delivery mode of the message.
     */
    public function getDeliveryMode(): int;

    /**
     * Get the priority of the message.
     * @return int The message priority.
     */
    public function getPriority(): int;

    /**
     * Get the message correlation id.
     * @return string The correlation id of the message.
     */
    public function getCorrelationId(): ?string;

    /**
     * Get the reply-to address of the message.
     * @return string The contents of the reply to field.
     */
    public function getReplyTo(): ?string;

    /**
     * Get the expiration of the message.
     * @return string The message expiration.
     */
    public function getExpiration(): ?string;

    /**
     * Get the message id of the message.
     * @return string The message id
     */
    public function getMessageId(): ?string;

    /**
     * Get the timestamp of the message.
     * @return string The message timestamp.
     */
    public function getTimestamp(): ?string;

    /**
     * Get the message type.
     * @return string The message type.
     */
    public function getType(): ?string;

    /**
     * Get the message user id.
     * @return string The message user id.
     */
    public function getUserId(): ?string;

    /**
     * Get the application id of the message.
     * @return string The application id of the message.
     */
    public function getAppId(): ?string;

    /**
     * Get the cluster id of the message.
     * @return string The cluster id of the message.
     */
    public function getClusterId(): ?string;
}

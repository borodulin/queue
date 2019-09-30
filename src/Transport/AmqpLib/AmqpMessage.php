<?php

declare(strict_types=1);

namespace Borodulin\Queue\Transport\AmqpLib;

use Borodulin\Queue\Transport\AmqpMessageInterface;
use PhpAmqpLib\Message\AMQPMessage as BasicAMQPMessage;

class AmqpMessage implements AmqpMessageInterface
{
    /**
     * @var BasicAMQPMessage
     */
    private $message;

    public function __construct(BasicAMQPMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get the routing key of the message.
     * @return string The message routing key.
     */
    public function getRoutingKey(): ?string
    {
        return $this->getProp('routing_key');
    }

    /**
     * Get the consumer tag of the message.
     * @return string The consumer tag of the message.
     */
    public function getConsumerTag(): ?string
    {
        return $this->getProp('consumer_tag');
    }

    /**
     * Get the delivery tag of the message.
     * @return string The delivery tag of the message.
     */
    public function getDeliveryTag(): ?string
    {
        return (string)$this->message->getDeliveryTag();
    }

    /**
     * Get the exchange name on which the message was published.
     * @return string The exchange name on which the message was published.
     */
    public function getExchangeName(): ?string
    {
        return $this->getProp('exchange_name');
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
        return (bool)$this->getProp('is_redelivery');
    }

    /**
     * Get a specific message header.
     * @param string $headerKey Name of the header to get the value from.
     * @return string|false The contents of the specified header or FALSE
     *                        if not set.
     */
    public function getHeader($headerKey)
    {
        return false;
    }

    /**
     * Check whether specific message header exists.
     * @param string $headerKey Name of the header to check.
     * @return bool
     */
    public function hasHeader($headerKey): bool
    {
        return false;
    }

    /**
     * Get the message content type.
     * @return string The content type of the message.
     */
    public function getContentType(): ?string
    {
        return $this->getProp('content_type');
    }

    /**
     * Get the content encoding of the message.
     * @return string The content encoding of the message.
     */
    public function getContentEncoding(): ?string
    {
        return $this->message->getContentEncoding();
    }

    /**
     * Get the headers of the message.
     * @return array An array of key value pairs associated with the message.
     */
    public function getHeaders(): array
    {
        return [];
    }

    /**
     * Get the delivery mode of the message.
     * @return integer The delivery mode of the message.
     */
    public function getDeliveryMode(): int
    {
        return (int)$this->getProp('delivery_mode');
    }

    /**
     * Get the priority of the message.
     * @return int The message priority.
     */
    public function getPriority(): int
    {
        return (int)$this->getProp('priority');
    }

    /**
     * Get the message correlation id.
     * @return string The correlation id of the message.
     */
    public function getCorrelationId(): ?string
    {
        return $this->getProp('correlation_id');
    }

    /**
     * Get the reply-to address of the message.
     * @return string The contents of the reply to field.
     */
    public function getReplyTo(): ?string
    {
        return $this->getProp('reply_to');
    }

    /**
     * Get the expiration of the message.
     * @return string The message expiration.
     */
    public function getExpiration(): ?string
    {
        return $this->getProp('expiration');
    }

    /**
     * Get the message id of the message.
     * @return string The message id
     */
    public function getMessageId(): ?string
    {
        return $this->getProp('message_id');
    }

    /**
     * Get the timestamp of the message.
     * @return string The message timestamp.
     */
    public function getTimestamp(): ?string
    {
        return $this->getProp('timestamp');
    }

    /**
     * Get the message type.
     * @return string The message type.
     */
    public function getType(): ?string
    {
        return $this->getProp('type');
    }

    /**
     * Get the message user id.
     * @return string The message user id.
     */
    public function getUserId(): ?string
    {
        return $this->getProp('user_id');
    }

    /**
     * Get the application id of the message.
     * @return string The application id of the message.
     */
    public function getAppId(): ?string
    {
        return $this->getProp('app_id');
    }

    /**
     * Get the cluster id of the message.
     * @return string The cluster id of the message.
     */
    public function getClusterId(): ?string
    {
        return $this->getProp('cluster_id');
    }

    /**
     * Get the body of the message.
     * @return string The contents of the message body.
     */
    public function getBody(): ?string
    {
        return $this->message->getBody();
    }

    private function getProp($name)
    {
        return $this->message->has($name) ? $this->message->get($name) : null;
    }
}

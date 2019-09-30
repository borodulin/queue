<?php

declare(strict_types=1);

namespace Borodulin\Queue\Transport\PhpAMQP;

use AMQPChannel;
use AMQPChannelException;
use AMQPConnection;
use AMQPConnectionException;
use AMQPExchange;
use AMQPExchangeException;
use AMQPQueue;
use AMQPQueueException;

class AMQP
{
    private $host;

    private $port;

    private $user;

    private $password;

    private $vhost;

    /**
     * @var AMQPConnection
     */
    private $connection;

    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * @var AMQPExchange[]
     */
    private $exchanges;

    /**
     * @var AMQPQueue[]
     */
    private $queues;

    public function __construct(
        $host = 'localhost',
        $port = 5672,
        $user = 'guest',
        $password = 'guest',
        $vHost = '/'
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->vhost = $vHost;
    }

    /**
     * @return AMQPConnection
     * @throws AMQPConnectionException
     */
    public function getConnection(): AMQPConnection
    {
        if (!$this->connection) {
            $this->connection = new AMQPConnection([
                'host' => $this->host,
                'port' => $this->port,
                'login' => $this->user,
                'password' => $this->password,
                'heartbeat' => 30,
            ]);

            $this->connection->connect();
        }

        return $this->connection;
    }

    /**
     * @return AMQPChannel
     * @throws AMQPConnectionException
     */
    public function getChannel(): AMQPChannel
    {
        if (!$this->channel) {
            $this->channel = new AMQPChannel($this->getConnection());
        }

        return $this->channel;
    }

    /**
     * @param string $name
     * @param string $type
     * @return AMQPExchange
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     */
    public function getExchange(string $name, $type = AMQP_EX_TYPE_DIRECT): AMQPExchange
    {
        if (!isset($this->exchanges[$name])) {
            $exchange = new AMQPExchange($this->getChannel());
            $exchange->setName($name);
            $exchange->setType($type);
            $exchange->setFlags(AMQP_DURABLE);
            $exchange->declareExchange();
            $this->exchanges[$name] = $exchange;
        }
        return $this->exchanges[$name];
    }

    /**
     * @param string $name
     * @param AMQPExchange|null $exchange
     * @param string|null $route
     * @return AMQPQueue
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     * @throws AMQPQueueException
     */
    public function getQueue(string $name, AMQPExchange $exchange = null, string $route = null): AMQPQueue
    {
        $exchange = $exchange ?: $this->getExchange($name);
        if (!isset($this->queues[$name])) {
            $queue = new AMQPQueue($this->getChannel());
            $queue->setFlags(AMQP_DURABLE);
            $queue->setName($name);
            $queue->declareQueue();
            $queue->bind($exchange->getName(), $route ?? $name);
            $this->queues[$name] = $queue;
        }
        return $this->queues[$name];
    }

    /**
     * @param $exchangeName
     * @param $route
     * @param $message
     * @param array $options reply_to,correlation_id
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     */
    public function sendJsonMessage(string $exchangeName, $message, string $route = null, $options = [])
    {
        $route = $route ?: $exchangeName;
        if (!is_string($message)) {
            $message = json_encode($message);
        }
        $options = array_replace([
            'delivery_mode' => AMQP_DURABLE,
            'content_type' => 'application/json',
        ], $options);
        $this->getExchange($exchangeName)->publish($message, $route, AMQP_NOPARAM, $options);
    }

    public function disconnect()
    {
        if ($this->connection) {
            $this->connection->disconnect();
        }
        $this->exchanges = null;
        $this->queues = null;
        $this->channel = null;
        $this->connection = null;
    }
}

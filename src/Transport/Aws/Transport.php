<?php


namespace Borodulin\Queue\Transport\Aws;

use Aws\Sqs\SqsClient;
use Borodulin\Queue\MessageHandlerInterface;
use Borodulin\Queue\Transport\TransportInterface;

class Transport implements TransportInterface
{

    /**
     * @var SqsClient
     */
    private $client;

    public function __construct($config)
    {
        $this->client = new SqsClient($config);
    }

    public function consume(MessageHandlerInterface $consumer, $queueName = 'default'): void
    {
        $result = $this->client->createQueue(array('QueueName' => $queueName));
        while (true) {
            $result = $this->client->receiveMessage(array(
                'QueueUrl' => $result->get('QueueUrl'),
                'MaxNumberOfMessages' => 10,
                'VisibilityTimeout' => 100,
                'WaitTimeSeconds' => 30,
            ));
            foreach ($result->getPath('Messages/*/Body') as $messageBody) {
                $message = new AwsMessage($messageBody);
                $consumer->handle($message);
            }
        }
    }

    public function pushMessage(string $queueName, string $message, array $options = []): void
    {
        $result = $this->client->createQueue(array('QueueName' => $queueName));
        $queueUrl = $result->get('QueueUrl');
        $options['QueueUrl'] = $queueUrl;
        $options['MessageBody'] = $message;
        $this->client->sendMessage($options);
    }
}

<?php
/**
 * DynamoDb Client service.
 *
 * Creates the client and handles getting and setting of the client and marshaler.
 */
namespace Nord\Lumen\DynamoDb;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Nord\Lumen\DynamoDb\Contracts\DynamoDbClientInterface;

/**
 * Class DynamoDbClientService.
 *
 * @package Nord\Lumen\DynamoDb
 */
class DynamoDbClientService implements DynamoDbClientInterface
{

    /**
     * Client instance from AWS SDK.
     *
     * @var \Aws\DynamoDb\DynamoDbClient $client The client.
     */
    protected $client;

    /**
     * Marshaler instance from AWS SDK.
     *
     * @var \Aws\DynamoDb\Marshaler $marshaler The marshaler.
     */
    protected $marshaler;

    /**
     * Class constructor.
     *
     * @param array     $config    The configuration for the client.
     * @param Marshaler $marshaler The marshaler object.
     */
    public function __construct(array $config = [], Marshaler $marshaler)
    {
        $this->client    = new DynamoDbClient($config);
        $this->marshaler = $marshaler;
    }

    /**
     * @inheritdoc
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @inheritdoc
     */
    public function getMarshaler()
    {
        return $this->marshaler;
    }
}

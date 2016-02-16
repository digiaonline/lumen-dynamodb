<?php
/**
 * Interface for the DynamoDB client.
 */
namespace Nord\Lumen\DynamoDb\Contracts;

/**
 * Interface DynamoDbClientInterface.
 *
 * @package Nord\Lumen\DynamoDb\Contracts
 */
interface DynamoDbClientInterface
{

    /**
     * Gets the DynamoDb client.
     *
     * @return \Aws\DynamoDb\DynamoDbClient
     */
    public function getClient();

    /**
     * Gets the DynamoDb Marshaler.
     *
     * @return \Aws\DynamoDb\Marshaler
     */
    public function getMarshaler();
}

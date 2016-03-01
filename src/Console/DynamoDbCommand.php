<?php
/**
 * DynamoDb Command.
 *
 * Base command for DynamoDB operations.
 */
namespace Nord\Lumen\DynamoDb\Console;

use Illuminate\Console\Command;
use Nord\Lumen\DynamoDb\Contracts\DynamoDbClientInterface;

/**
 * Class DynamoDbCommand.
 *
 * @package Nord\Lumen\DynamoDb\Console
 */
class DynamoDbCommand extends Command
{

    /**
     * @var DynamoDbClientInterface $dynamoDb The DynamoDb interface.
     */
    protected $dynamoDb;

    /**
     * List of tables, can be used instead of config.
     *
     * @var array
     */
    protected static $tables = [];

    /**
     * Class constructor.
     *
     * @param DynamoDbClientInterface $dynamoDb
     */
    public function __construct(DynamoDbClientInterface $dynamoDb)
    {
        parent::__construct();
        $this->dynamoDb = $dynamoDb;
    }

    /**
     * Checks if the given table exists.
     *
     * @param string $tableName The table name to check.
     *
     * @return bool True if the table exists, false otherwise.
     */
    protected function tableExists($tableName)
    {
        $found    = false;
        $iterator = $this->dynamoDb->getClient()->getIterator('ListTables');
        foreach ($iterator as $table) {
            if ($table === $tableName) {
                $found = true;
            }
        }

        return $found;
    }
}

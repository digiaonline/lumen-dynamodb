<?php
/**
 * DynamoDb Command.
 *
 * Create, delete and describe a simple DynamoDB table.
 * The create command just creates a simple table with one primary key
 * id which is of type string.
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
     * @var string $name The name of the command.
     */
    protected $name = 'dynamodb';

    /**
     * The name and signature of the command.
     *
     * @var string $signature The signature of the command.
     */
    protected $signature = 'dynamodb {--create : Create a database table.}
        {--delete : Delete a table.}
        {--describe : Describe the given table.}
        {--tableName= : The table name, required.}';

    /**
     * @var string $description The description of the command.
     */
    protected $description = 'DynamoDB management command.';

    /**
     * @var DynamoDbClientInterface $dynamoDb The DynamoDb interface.
     */
    protected $dynamoDb;

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
     * Run the command.
     */
    public function handle()
    {
        $create    = $this->input->getOption('create');
        $delete    = $this->input->getOption('delete');
        $describe  = $this->input->getOption('describe');
        $tableName = $this->input->getOption('tableName');

        if ($create) {
            $this->createTable($tableName);
        }

        if ($delete) {
            $this->deleteTable($tableName);
        }

        if ($describe) {
            $this->describeTable($tableName);
        }
    }

    /**
     * Creates the table to DynamoDB.
     *
     * @param string $tableName The table name to create.
     *
     * @throws \Exception
     */
    protected function createTable($tableName)
    {
        if (empty( $tableName )) {
            throw new \Exception('Table name cannot be empty!');
        }

        $client = $this->dynamoDb->getClient();
        $this->info(sprintf('Checking if table "%s" exists.', $tableName));

        if ( ! $this->tableExists($tableName)) {
            $this->comment(sprintf('Creating table %s', $tableName));
            $client->createTable([
                'TableName'             => $tableName,
                'AttributeDefinitions'  => [
                    [
                        'AttributeName' => 'id',
                        'AttributeType' => 'S',
                    ],
                ],
                'KeySchema'             => [
                    [
                        'AttributeName' => 'id',
                        'KeyType'       => 'HASH',
                    ],
                ],
                // ProvisionedThroughput is required
                'ProvisionedThroughput' => [
                    // ReadCapacityUnits is required
                    'ReadCapacityUnits'  => 10,
                    // WriteCapacityUnits is required
                    'WriteCapacityUnits' => 20,
                    'OnDemand'           => false,
                ],
            ]);

            $client->waitUntil('TableExists', array(
                'TableName' => $tableName,
            ));

            $this->info(sprintf('Table "%s" created.', $tableName));
        } else {
            $this->warn(sprintf('Table "%s" already exists.', $tableName));
        }
    }

    /**
     * Deletes a given table.
     *
     * @param string $tableName The table name to delete.
     *
     * @throws \Exception
     */
    protected function deleteTable($tableName)
    {
        if (empty( $tableName )) {
            throw new \Exception('Table name cannot be empty!');
        }

        $a = $this->ask(sprintf('Are you sure you want to delete table "%s"? [y/N]', $tableName), 'n');
        if (strtolower($a) === 'y') {
            $client = $this->dynamoDb->getClient();

            if ($this->tableExists($tableName)) {
                $this->info(sprintf('Deleting table "%s"', $tableName));
                $client->deleteTable([
                    'TableName' => $tableName,
                ]);
                $client->waitUntil('TableNotExists', ['TableName' => $tableName]);
                $this->info('Table deleted.');
            }
        }
    }

    /**
     * Describe a given table.
     *
     * @param string $tableName The table name to describe.
     *
     * @throws \Exception
     */
    protected function describeTable($tableName)
    {
        if (empty( $tableName )) {
            throw new \Exception('Table name cannot be empty!');
        }
        var_dump($this->dynamoDb->getClient()->describeTable([
            'TableName' => $tableName,
        ]));
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

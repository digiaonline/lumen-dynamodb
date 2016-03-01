<?php
/**
 * Create DynamoDB tables based on given configuration.
 */

namespace Nord\Lumen\DynamoDb\Console;

/**
 * Class CreateTablesCommand
 *
 * @package Nord\Lumen\DynamoDb\Console
 */
class CreateTablesCommand extends DynamoDbCommand
{

    /**
     * @var string $name The name of the command.
     */
    protected $name = 'dynamodb:create';

    /**
     * The name and signature of the command.
     *
     * @var string $signature The signature of the command.
     */
    protected $signature = 'dynamodb:create {--config= : The path to the table configuration.}';

    /**
     * @var string $description The description of the command.
     */
    protected $description = 'Create tables in DynamoDB, uses configuration file.';

    /**
     * Run the command.
     *
     * @throws \Exception
     */
    public function handle()
    {
        $tableConfigFile = $this->input->getOption('config');
        if ($tableConfigFile && ! file_exists($tableConfigFile)) {
            throw new \Exception(sprintf('The configuration file "%s" does not exist!', $tableConfigFile));
        }

        if ($tableConfigFile) {
            $tableConfig = include_once( $tableConfigFile );
        }
        if ( ! empty( $tableConfig )) {
            self::$tables = $tableConfig;
        }

        $this->createTables();
    }

    /**
     * Creates the tables defined in configuration file, or overridden.
     *
     * @throws \Exception
     */
    protected function createTables()
    {
        if (empty( self::$tables )) {
            throw new \Exception('Cannot create tables, as no configuration file given, or the ::$tables is not overridden.');
        }
        $client = $this->dynamoDb->getClient();

        foreach (self::$tables as $tableData) {
            $tableName = $tableData['TableName'];

            if ( ! $this->tableExists($tableName)) {
                $this->comment(sprintf('Creating table %s', $tableName));

                $client->createTable($tableData);

                $client->waitUntil('TableExists', array(
                    'TableName' => $tableName,
                ));

                $this->info(sprintf('Table "%s" created.', $tableName));
            } else {
                $this->warn(sprintf('Table "%s" already exists.', $tableName));
            }
        }
    }
}

<?php
/**
 * Delete DynamoDB tables based on given configuration.
 */

namespace Nord\Lumen\DynamoDb\Console;

/**
 * Class DeleteTablesCommand
 *
 * @package Nord\Lumen\DynamoDb\Console
 */
class DeleteTablesCommand extends DynamoDbCommand
{

    /**
     * @var string $name The name of the command.
     */
    protected $name = 'dynamodb:delete';

    /**
     * The name and signature of the command.
     *
     * @var string $signature The signature of the command.
     */
    protected $signature = 'dynamodb:delete {--config= : The path to the table configuration.}
    {--y|yes : Answer yes to all confirmations.}';

    /**
     * @var string $description The description of the command.
     */
    protected $description = 'Delete tables in DynamoDB, uses configuration file.';

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

        $this->deleteTables();
    }

    /**
     * Deletes the tables defined in configuration file, or overridden.
     *
     * @throws \Exception
     */
    protected function deleteTables()
    {
        if (empty( self::$tables )) {
            throw new \Exception('Cannot delete tables, as no configuration file given, or the ::$tables is not overridden.');
        }
        $client = $this->dynamoDb->getClient();

        $defaultAnswer = $this->input->getOption('yes');
        $a = 'n';
        foreach (self::$tables as $tableData) {
            $tableName = $tableData['TableName'];

            if ($this->tableExists($tableName)) {

                if ($defaultAnswer === false) {
                    $a = $this->ask(sprintf('Are you sure you want to delete table "%s"? [y/N]', $tableName), 'n');
                }

                if ($defaultAnswer || strtolower($a) === 'y') {
                    // Reset the answer.
                    $a = null;
                    $this->comment(sprintf('Deleting table "%s"', $tableName));

                    $client->deleteTable([
                        'TableName' => $tableName,
                    ]);
                    $client->waitUntil('TableNotExists', ['TableName' => $tableName]);

                    $this->info('Table deleted.');
                } else {
                    $this->comment(sprintf('Skipping table %s', $tableName));
                }
            } else {
                $this->warn(sprintf('Table "%s" does not exist.', $tableName));
            }
        }
    }
}

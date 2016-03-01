# lumen-dynamodb

Lumen implementation for DynamoDB, based on [Bao Pham's laravel-dynamodb](https://github.com/baopham/laravel-dynamodb).

# Prerequisites

To install DynamoDB locally, see [Running DynamoDB on Your Computer](http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/Tools.DynamoDBLocal.html).

When DynamoDB is set up, start it with `java -Djava.library.path=./DynamoDBLocal_lib -jar DynamoDBLocal.jar -sharedDb`

You can use the DynamoDB artisan command to manage the DynamoDB at the moment:

    php artisan dynamodb:create --config=<TABLE_CONFIGURATION> # To create tables
    php artisan dynamodb:delete --config=<TABLE_CONFIGURATION> -y # To delete tables

The table configuration file should return an array of table configurations:

    return [
        [
            'TableName'             => 'users',
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
            'ProvisionedThroughput' => [
                'ReadCapacityUnits'  => 10,
                'WriteCapacityUnits' => 20,
                'OnDemand'           => false,
            ],
        ],
        [
            'TableName'             => 'orders',
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
            'ProvisionedThroughput' => [
                'ReadCapacityUnits'  => 10,
                'WriteCapacityUnits' => 20,
                'OnDemand'           => false,
            ],
        ],
    ];


If you don't want to use a configuration file, you may as well override the Create/DeleteTablesCommand and put your
table definitions in the `protected static $tables = []` array to override the tables.

Remember to add the overridden command in your `Kernel.php` file.


# Install

    // .env:
    DYNAMODB_KEY=<AWS_KEY>
    DYNAMODB_SECRET=<AWS_SECRET_KEY>
    DYNAMODB_REGION=<AWS_REGION>
    DYNAMODB_VERSION=latest
    DYNAMODB_LOCAL_ENDPOINT=http://localhost:8000 # Only used for local DynamoDB
    
    
    // config/services.php
    return [
        ...
        'dynamodb' => [
            'key'      => env('DYNAMODB_KEY', 'dynamodb_local'),
            'secret'   => env('DYNAMODB_SECRET', 'secret'),
            'region'   => env('DYNAMODB_REGION', 'eu-central-1'),
            'version'  => env('DYNAMODB_VERSION', 'latest'),
            'endpoint' => env('DYNAMODB_LOCAL_ENDPOINT', 'http://localhost:8000'),
        ],
        ...
    ];
    
    // bootstrap/app.php
    ...
    $app->configure('services');
    ...
    $app->register(Nord\Lumen\DynamoDb\DynamoDBServiceProvider::class);

# Usage

Extend all your models from \Nord\Lumen\DynamoDb\Domain\Model\DynamoDbModel

You will need to set the following properties for your model to get the DynamoDbModel to work:

    // The keys are defined when creating the table. If you are using only one primary key, set the $primaryKey,
    // if using both primaryKey and sortKey, define the $compositeKey.
    protected $primaryKey = '<primaryKey>'; // Ignore if using composite key.
    protected $compositeKey = ['<primaryKey>', '<sortKey>']; // Ignore if you don't have a composite key.
    protected $table = '<table_name>'; // Set the DynamoDB table this model uses
    // If using global or local indexes, define the key => indexName values here.
    protected $dynamoDbIndexKeys = [
        '<globalIndexKey>' => '<globalIndexName>',
        '<localIndexKey>' => '<localIndexName>',
    ];
    
    // Set this to be able to mass assign attributes.
    protected $fillable = ['email', 'name'];
    protected $guarded = ['address'];

Using the models works pretty much in the same way as with Eloquent:

    $model = DynamoDbModel::find(1); // Find a model with the primary key 1
    // Using where:
    $model = DynamoDbModel::where(['email' => 'test@example.com']);
    $model->get()->first(); // Returns the first record.

    $model = new DynamoDbModel(['name' => 'Demo user', 'email' => 'test@example.com']); // Fillable attributes.
    $model->setAddress('Teststreet 1'); // Set the guarded attribute.
    $model->save();
    
# License
See [LICENSE](LICENSE).

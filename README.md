# lumen-dynamodb

Support for saving, updating and finding models to/from DynamoDB.

# Installation and usage

To install DynamoDB locally, see [Running DynamoDB on Your Computer](http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/Tools.DynamoDBLocal.html).

When DynamoDB is set up, start it with `java -Djava.library.path=./DynamoDBLocal_lib -jar DynamoDBLocal.jar -sharedDb`

You can use the DynamoDB artisan command to manage the DynamoDB at the moment:

    php artisan dynamodb --create --tableName=<TABLE_NAME> # To create a table
    php artisan dynamodb --describe --tableName=<TABLE_NAME> # To describe a table
    php artisan dynamodb --delete --tableName=<TABLE_NAME> # To delete a table


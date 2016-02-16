<?php
/*
 * Configuration file for services.php
 *
 * If you already have the services.php under your config/ directory, just copy the dynamodb part to the file.
 */
return [
    'dynamodb' => [
        // AWS Access Key.
        'key'      => env('DYNAMODB_KEY', 'dynamodb_local'),
        // AWS Secret Key.
        'secret'   => env('DYNAMODB_SECRET', 'secret'),
        // AWS Region.
        'region'   => env('DYNAMODB_REGION', 'eu-central-1'),
        // DynamoDB version.
        'version'  => env('DYNAMODB_VERSION', 'latest'),
        // Only used for local endpoint.
        'endpoint' => env('DYNAMODB_LOCAL_ENDPOINT', 'http://localhost:8000'),
    ],
];

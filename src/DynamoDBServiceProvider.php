<?php
/**
 * Service provider for DynamoDB
 *
 * Registers the DynamoDb client for the app to use.
 */
namespace Nord\Lumen\DynamoDb;

use Aws\DynamoDb\Marshaler;
use Illuminate\Support\ServiceProvider;

/**
 * Class DynamoDBServiceProvider.
 *
 * @package Nord\Lumen\DynamoDb
 */
class DynamoDBServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('App\DynamoDb\DynamoDbClientInterface', function ($app) {
            $config = [
                'credentials' => [
                    'key'    => config('services.dynamodb.key'),
                    'secret' => config('services.dynamodb.secret'),
                ],
                'region'      => config('services.dynamodb.region'),
                'version'     => config('services.dynamodb.version'),
            ];

            // Set endpoint if local environment.
            if (env('APP_ENV') === 'local') {
                $config['endpoint'] = config('services.dynamodb.endpoint');
            }

            $client = new DynamoDbClientService($config, new Marshaler(['nullify_invalid' => true]));

            return $client;
        });
    }
}

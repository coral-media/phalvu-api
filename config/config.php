<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

require_once \dirname(__DIR__) . '/vendor/autoload.php';

use Phalcon\Config\Config;
use Phalcon\Logger\AbstractLogger;

$dotenv = Dotenv\Dotenv::createMutable(\dirname(__DIR__));
$dotenv->safeLoad();

$config = [
    'database'    => [
        'adapter'  => env('DB_ADAPTER'),
        'host'     => env('DB_HOST'),
        'port'     => env('DB_PORT'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
        'dbname'   => env('DB_NAME'),
        'charset'  => env('DB_CHARSET'),
    ],
    'migrations' => [
        'directory' => root_path('migrations/'),
        'namespace' => 'Migrations',
        'table'     => 'migrations_log', // Optional: table to keep track of applied migrations
        'timestamp' => true,
    ],
    'cache' => [
        'adapter' => env('APP_ENV') === 'dev' ? 'stream' : 'apcu',
        'options' => [
            'prefix' => env('APP_ENV') === 'dev' ? '' : 'fearless-bull-',
            'serializer' => 'Php',
            'storageDir' => root_path('var/cache'),
        ],
    ],
    'application' => [
        'logInDb'              => true,
        'viewsDir'          => root_path('templates/'),
        'baseUri'           => env('APP_BASE_URI'),
        'publicUrl'         => env('APP_PUBLIC_URL'),
        'sessionDir'        => root_path('var/cache/session/'),
        'appSecretKey'      => env('APP_SECRET'),
    ],
    'logger' => [
        'name' => 'file',
        'options' => [
            'adapters' => [
                'main' => [
                    'adapter' => 'stream',
                    'name' => root_path(
                        \sprintf(
                            'var/logs/%s.log',
                            env('APP_ENV'),
                        ),
                    ),
                    'options' => [],
                ],
            ],
        ],
    ],
    'jwt' => [
        'secret' => env('JWT_SECRET'),
        'expiration' => 3600,
    ],
];

if (env('APP_ENV') == 'dev') {
    $config['application']['logLevel'] = AbstractLogger::DEBUG;
} else {
    $config['application']['logLevel'] = AbstractLogger::ERROR;
}

return new Config($config);

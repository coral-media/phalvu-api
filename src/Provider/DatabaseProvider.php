<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Provider;

use Phalcon\Config\Config;
use Phalcon\Db\Adapter\Pdo;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use RuntimeException;

class DatabaseProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected string $providerName = 'db';

    /**
     * Class map of database adapters, indexed by PDO::ATTR_DRIVER_NAME.
     *
     * @var array
     */
    protected array $adapters = [
        'mysql'  => Pdo\Mysql::class,
        'pgsql'  => Pdo\Postgresql::class,
        'sqlite' => Pdo\Sqlite::class,
    ];


    /**
     * @param DiInterface $di
     *
     * @return void
     * @throws RuntimeException
     */
    public function register(DiInterface $di): void
    {
        /** @var Config $config */
        $config = $di->getShared('config')->get('database');

        $class  = $this->getClass($config);
        $config = $this->createConfig($config);

        $di->setShared($this->providerName, function () use ($class, $config) {
            return new $class($config);
        });
    }

    /**
     * Get an adapter class by name.
     *
     * @param Config $config
     *
     * @return string
     * @throws RuntimeException
     */
    private function getClass(Config $config): string
    {
        $name = $config->get('adapter', 'Unknown');

        if (empty($this->adapters[$name])) {
            throw new RuntimeException(
                \sprintf(
                    'Adapter "%s" has not been registered',
                    $name,
                ),
            );
        }

        return $this->adapters[$name];
    }

    private function createConfig(Config $config): array
    {
        // To prevent error: SQLSTATE[08006] [7] invalid connection option "adapter"
        $dbConfig = $config->toArray();
        unset($dbConfig['adapter']);

        $name = $config->get('adapter');

        switch ($this->adapters[$name]) {
            case Pdo\Sqlite::class:
                // Resolve database path
                $dbConfig = ['dbname' => root_path($config->get('dbname'))];
                break;
            case Pdo\Postgresql::class:
                // Postgres does not allow the charset to be changed in the DSN.
                unset($dbConfig['charset']);
                break;
        }

        return $dbConfig;
    }
}

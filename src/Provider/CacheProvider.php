<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Provider;

use Phalcon\Cache\AdapterFactory;
use Phalcon\Cache\Cache;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Storage\SerializerFactory;

class CacheProvider implements ServiceProviderInterface
{
    protected string $providerName = 'cache';

    public function register(DiInterface $di): void
    {
        $config = $di->getShared('config');

        $di->setShared(
            $this->providerName,
            function () use ($config) {
                $cache   = $config->get('cache')
                    ->toArray()
                ;
                $adapter = $cache['adapter'];
                $options = $cache['options'] ?? [];

                $serializerFactory = new SerializerFactory();
                $adapterFactory    = new AdapterFactory($serializerFactory);
                $adapter           = $adapterFactory->newInstance($adapter, $options);

                return new Cache($adapter);
            },
        );
    }
}

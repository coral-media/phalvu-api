<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Provider;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Logger\AdapterFactory;
use Phalcon\Logger\LoggerFactory;

class LoggerProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected string $providerName = 'logger';

    /**
     * @param DiInterface $di
     *
     * @return void
     */
    public function register(DiInterface $di): void
    {
        $config = $di->getShared('config')->get('logger');
        $di->setShared($this->providerName, function () use ($config) {
            $adapterFactory = new AdapterFactory();
            $loggerFactory  = new LoggerFactory($adapterFactory);

            return $loggerFactory->load($config);
        });
    }
}

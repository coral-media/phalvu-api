<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Provider;

use App\Market\TradeStation\Service;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class TradestationProvider implements ServiceProviderInterface
{
    protected string $providerName = 'tradestation';

    public function register(DiInterface $di): void
    {
        $di->setShared($this->providerName, function () use ($di) {
            $config = $di->getShared('config');

            return new Service(
                $config->tradestation,
                $di->getShared('session'),
                $di->getShared('logger'),
            );
        });
    }
}

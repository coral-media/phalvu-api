<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Provider;

use App\Market\TradingDays;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class TradingDaysProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->set('tradingDays', function () use ($di) {
            return new TradingDays();
        });
    }
}

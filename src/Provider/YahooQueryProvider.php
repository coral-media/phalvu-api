<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Provider;

use App\Market\YahooFinances\ChartQuery;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Logger\LoggerInterface;

class YahooQueryProvider implements ServiceProviderInterface
{
    protected string $providerName = 'yahooQuery';

    public function register(DiInterface $di): void
    {
        $di->setShared($this->providerName, function () use ($di) {
            /** @var LoggerInterface $logger */
            $logger = $di->getShared('logger');

            return new ChartQuery($logger, $di->get('httpClient'));
        });
    }
}

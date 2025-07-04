<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use Phalcon\Cache\CacheInterface;
use Phalcon\Config\ConfigInterface;
use Phalcon\Db\Adapter\AdapterInterface;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Psr\Log\LoggerInterface;

/**
 * @property LoggerInterface $logger
 * @property ConfigInterface $config
 * @property CacheInterface $cache
 * @property AdapterInterface $db
 */
abstract class AbstractController extends Controller
{
    public function beforeExecuteRoute(): void
    {
        $this->logger->info(
            \sprintf(
                'Route matched [%s]',
                $this->router->getMatchedRoute()->getName(),
            ),
        );
    }

    public function afterExecuteRoute(Dispatcher $dispatcher): void
    {

    }
}

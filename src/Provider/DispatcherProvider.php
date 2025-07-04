<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Provider;

use Phalcon\Cli\Dispatcher as CliDispatcher;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;

class DispatcherProvider implements ServiceProviderInterface
{
    protected string $providerName = 'dispatcher';

    public function register(DiInterface $di): void
    {
        /** @var ManagerInterface $eventsManager */
        $eventsManager = $di->get('eventsManager');

        $di->set($this->providerName, function () use ($eventsManager, $di) {
            if ($di->getShared('app.isCli') === false) {
                $dispatcher = new MvcDispatcher();
                $dispatcher->setDefaultNamespace('App\Controller');
            } else {
                $dispatcher = new CliDispatcher();
                $dispatcher->setDefaultNamespace('App\Command');
            }

            $dispatcher->setEventsManager($eventsManager);

            return $dispatcher;
        });
    }
}

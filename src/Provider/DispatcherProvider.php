<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Provider;

use App\Http\Middleware\JwtTokenAuth;
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
                $dispatcher->setEventsManager($eventsManager);

                $request = $di->getShared('request');
                $uri = trim($request->getURI(), '/');

                $segments = explode('/', $uri);
                $firstSegment = $segments[0] ?? null;

                // Build namespace dynamically
                if ($firstSegment && preg_match('/^[a-z0-9_-]+$/i', $firstSegment)) {
                    $namespace = 'App\\Controller\\' . ucfirst($firstSegment);
                } else {
                    $namespace = 'App\\Controller';
                }

                $dispatcher->setDefaultNamespace($namespace);

                // Attach middleware only for certain namespaces
                if ('App\\Controller\\Api' === $namespace) {
                    $eventsManager->attach('dispatch:beforeExecuteRoute', new JwtTokenAuth());
                }

                return $dispatcher;
            }

            $dispatcher = new CliDispatcher();
            $dispatcher->setDefaultNamespace('App\\Command');
            return $dispatcher;
        });
    }
}

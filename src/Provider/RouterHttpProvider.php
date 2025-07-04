<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Provider;

use App\Kernel;
use Exception;
use Phalcon\Cache\Cache;
use Phalcon\Config\Config;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\Router;

class RouterHttpProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected string $providerName = 'httpRouter';

    /**
     * @param DiInterface $di
     *
     * @return void
     */
    public function register(DiInterface $di): void
    {
        $routerProvider = $this;
        /**
         * @var Cache $cache
         */
        $cache = $di->getShared('cache');

        $di->setShared($this->providerName, function () use ($routerProvider, $cache, $di) {

            $cachedRouter = $cache->get('httpRouter');

            if ($cachedRouter instanceof Router) {
                return $cachedRouter;
            }

            $routes = BASE_PATH . '/config/routes.php';
            if (!file_exists($routes) || !is_readable($routes)) {
                throw new Exception($routes . ' file does not exist or is not readable.');
            }

            $routesConfig = require_once $routes;

            $router = new Router($routesConfig['http']['default']);

            if (null !== $routesConfig['http']['groups']) {
                $routerProvider->mountConfiguredRoutes($router, $routesConfig['http']['groups']);
            }

            $router->notFound([
                'namespace'  => 'App\\Controller',
                'controller' => 'error',
                'action'     => 'notFound',
            ]);

            $cache->set('router', $router);

            return $router;
        });
    }

    protected function mountConfiguredRoutes(Router $router, array $groupsConfig): void
    {
        foreach ($groupsConfig as $groupName => $groupConfig) {
            $group = new Router\Group([
                'namespace' => $groupConfig['namespace'] ?? null,
            ]);

            $group->setPrefix($groupConfig['prefix'] ?? '');

            foreach ($groupConfig['routes'] as $routeKey => $routeData) {
                $pattern = $routeData['pattern'] ?? $routeKey;
                $methods = $routeData['methods'] ?? ['GET'];

                unset($routeData['pattern'], $routeData['methods']);

                foreach ((array) $methods as $method) {
                    $method = strtoupper($method);
                    $routeObject = match ($method) {
                        'POST'   => $group->addPost($pattern, $routeData),
                        'PUT'    => $group->addPut($pattern, $routeData),
                        'PATCH'    => $group->addPatch($pattern, $routeData),
                        'DELETE' => $group->addDelete($pattern, $routeData),
                        default  => $group->addGet($pattern, $routeData),
                    };

                    $routeObject->setName(
                        $groupName . '_' . $routeKey . '_' . strtolower($method),
                    );
                }
            }

            $router->mount($group);
        }
    }
}

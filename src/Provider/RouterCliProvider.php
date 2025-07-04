<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Provider;

use Phalcon\Cli\Router;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Exception;

class RouterCliProvider implements ServiceProviderInterface
{
    protected string $providerName = 'cliRouter';

    public function register(DiInterface $di): void
    {

        $di->setShared($this->providerName, function () {
            $basePath = BASE_PATH;
            $routesFile = BASE_PATH . '/config/routes.php';

            if (!file_exists($routesFile) || !is_readable($routesFile)) {
                throw new Exception('CLI routes file not found: ' . $routesFile);
            }

            $routesConfig = require $routesFile;

            $router = new Router($routesConfig['cli']['default']);

            $router->add('/:task/:action/:params', [
                'task'   => 1,
                'action' => 2,
                'params' => 3,
            ])->setName('default_cli');

            return $router;
        });
    }
}

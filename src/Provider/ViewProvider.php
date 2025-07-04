<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Provider;

use Phalcon\Config\Config;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;

class ViewProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected string $providerName = 'view';

    /**
     * @param DiInterface $di
     */
    public function register(DiInterface $di): void
    {
        /** @var Config $config */
        $config = $di->getShared('config');
        /** @var string $viewsDir */
        $viewsDir = $config->path('application.viewsDir');
        /** @var string $cacheDir */
        $cacheDir = $config->path('cache.options.storageDir');

        $di->setShared($this->providerName, function () use ($viewsDir, $cacheDir, $di) {
            $view = new View();
            $view->setViewsDir($viewsDir);
            $view->registerEngines(
                [
                    '.volt' => function (View $view) use ($cacheDir, $di) {
                        $volt = new VoltEngine($view, $di);
                        $volt->setOptions([
                            'path'      => $cacheDir . '/volt/',
                            'separator' => '_',
                        ]);

                        return $volt;
                    },
                    '.phtml' => PhpEngine::class,
                ],
            );

            return $view;
        });
    }
}

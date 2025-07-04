<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App;

use Exception;
use Phalcon\Application\AbstractApplication;
use Phalcon\Cli\Console;
use Phalcon\Di\DiInterface;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Application;

class Kernel
{
    protected AbstractApplication $app;

    protected DiInterface $di;

    /**
     * @throws Exception
     */
    public function __construct(private readonly bool $cli = false)
    {
        $this->di = new FactoryDefault();

        $cli = $this->cli;
        $this->di->setShared('app.isCli', fn () => $cli);

        $this->initializeProviders();

        $this->app = $this->createApplication();
    }

    public function run(): ResponseInterface
    {
        $baseUri = $this->di->getShared('url')->getBaseUri();
        $position = strpos($_SERVER['REQUEST_URI'], $baseUri) + \strlen($baseUri);
        $uri = '/' . substr($_SERVER['REQUEST_URI'], $position);

        /** @var ResponseInterface $response */
        $response = $this->app->handle($uri);

        return $response->send();
    }

    public function getApplication(): AbstractApplication
    {
        return $this->app;
    }

    /**
     * @throws Exception
     */
    protected function createApplication(): AbstractApplication
    {
        if (false === $this->cli) {
            $router = $this->di->get('httpRouter');
            $this->di->set('router', $router);
            $application = new Application($this->di);
        } else {
            $router = $this->di->get('cliRouter');
            $this->di->set('router', $router);
            $application = new Console($this->di);
        }

        return $application;
    }

    /**
     * @throws Exception
     */
    protected function initializeProviders(): void
    {
        $filename = BASE_PATH . '/config/providers.php';
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new Exception('File providers.php does not exist or is not readable.');
        }

        $providers = require $filename;

        foreach ($providers as $providerClass) {
            /** @var ServiceProviderInterface $provider */
            $provider = new $providerClass();
            $provider->register($this->di);
        }
    }
}

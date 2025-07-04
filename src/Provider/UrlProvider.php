<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Provider;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\Url as UrlResolver;

class UrlProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected string $providerName = 'url';

    /**
     * @param DiInterface $di
     */
    public function register(DiInterface $di): void
    {
        /** @var string $baseUri */
        $baseUri = $di->getShared('config')->path('application.baseUri');

        $di->setShared($this->providerName, function () use ($baseUri) {
            $url = new UrlResolver();
            $url->setBaseUri($baseUri);

            return $url;
        });
    }
}

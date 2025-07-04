<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Provider;

use App\Http\Client;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class HttpClientProvider implements ServiceProviderInterface
{
    protected string $providerName = 'httpClient';

    public function register(DiInterface $di): void
    {
        $di->set($this->providerName, function () {
            // You can customize default headers or allow insecure SSL here
            return new Client([], false); // Pass true for SSL verification
        });
    }
}

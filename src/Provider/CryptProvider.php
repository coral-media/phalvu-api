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
use Phalcon\Encryption\Crypt;

class CryptProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected string $providerName = 'crypt';

    /**
     * @param DiInterface $di
     *
     * @return void
     */
    public function register(DiInterface $di): void
    {
        /** @var string $cryptSalt */
        $cryptSalt = $di->getShared('config')
            ->path('application.appSecretKey')
        ;

        $di->set($this->providerName, function () use ($cryptSalt) {
            $crypt = new Crypt();
            $crypt->setKey($cryptSalt);

            return $crypt;
        });
    }
}

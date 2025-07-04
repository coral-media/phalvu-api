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
use Phalcon\Encryption\Security;

class SecurityProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected string $providerName = 'security';

    /**
     * @param DiInterface $di
     *
     * @return void
     */
    public function register(DiInterface $di): void
    {
        $di->setShared($this->providerName, function () use ($di) {
            $security = new Security();
            $security->setDI($di);

            return $security;
        });
    }
}

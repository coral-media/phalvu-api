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
use Phalcon\Session\Adapter\Stream as SessionAdapter;
use Phalcon\Session\Manager as SessionManager;

class SessionProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected string $providerName = 'session';

    /**
     * @param DiInterface $di
     *
     * @return void
     */
    public function register(DiInterface $di): void
    {
        /** @var string $savePath */
        $savePath = $di->getShared('config')->path('application.sessionDir');
        $handler  = new SessionAdapter([
            'savePath' => $savePath,
        ]);

        $di->setShared($this->providerName, function () use ($handler) {
            $session = new SessionManager();
            $session->setAdapter($handler);
            $session->start();

            return $session;
        });
    }
}

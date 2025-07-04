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

class ConfigProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected string $providerName = 'config';

    /**
     * @param DiInterface $di
     *
     * @return void
     */
    public function register(DiInterface $di): void
    {

        $di->setShared($this->providerName, function () {
            return $config = include BASE_PATH . '/config/config.php';
        });
    }
}

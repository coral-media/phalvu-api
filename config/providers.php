<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

use App\Provider\CacheProvider;
use App\Provider\ConfigProvider;
use App\Provider\CryptProvider;
use App\Provider\DatabaseProvider;
use App\Provider\DispatcherProvider;
use App\Provider\HttpClientProvider;
use App\Provider\LoggerProvider;
use App\Provider\RouterCliProvider;
use App\Provider\RouterHttpProvider;
use App\Provider\SecurityProvider;
use App\Provider\SessionProvider;
use App\Provider\UrlProvider;
use App\Provider\ViewProvider;

return [
    ConfigProvider::class,
    CacheProvider::class,
    LoggerProvider::class,
    RouterCliProvider::class,
    RouterHttpProvider::class,
    DispatcherProvider::class,
    ViewProvider::class,
    SessionProvider::class,
    UrlProvider::class,
    CryptProvider::class,
    SecurityProvider::class,
    DatabaseProvider::class,
    HttpClientProvider::class,
];

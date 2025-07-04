<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

use Phalcon\Di\Di;
use Phalcon\Di\DiInterface;

/**
 * Call Dependency Injection container
 *
 * @return mixed|null|DiInterface
 */
function container(): mixed
{
    $default = Di::getDefault();
    $args    = \func_get_args();
    if (empty($args)) {
        return $default;
    }

    return \call_user_func_array([$default, 'get'], $args);
}

/**
 * Get project relative root path
 *
 * @param string $prefix
 *
 * @return string
 */
function root_path(string $prefix = ''): string
{
    return join(
        DIRECTORY_SEPARATOR,
        [BASE_PATH, ltrim($prefix, DIRECTORY_SEPARATOR)],
    );
}

/**
 * @param string $varName
 * @param string|null $default
 * @return mixed
 */
function env(string $varName, ?string $default = null): ?string
{
    if (isset($_SERVER[$varName])) {
        return $_SERVER[$varName];
    }
    if (isset($_ENV[$varName])) {
        return $_ENV[$varName];
    }

    $value = getenv($varName);
    return false !== $value ? $value : $default;
}

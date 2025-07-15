<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

return [
    'http' => [
        'default' => false,
        'groups' => [
            'default' => [
                'namespace' => 'App\\Controller',
                'prefix' => '/',
                'routes'   => [
                    'default_home_index' => [
                        'pattern' => '',
                        'controller' => 'home',
                        'action' => 'index',
                    ],
                    'default' => [
                        'pattern' => ':controller/:action/:params',
                        'controller' => 1,
                        'action' => 2,
                        'params' => 3,
                    ],
                ],
            ],
            'api' => [
                'namespace' => 'App\\Controller\\Api',
                'prefix' => '/api/',
                'routes' => [
                    'security_login' => [
                        'pattern'    => 'security/login',
                        'controller' => 'security',
                        'action'     => 'login',
                        'methods'    => ['POST'],
                    ],
                    'default_get_collection' => [
                        'pattern' => ':controller',
                        'controller' => 1,
                        'action' => 'index',
                        'methods' => ['GET'],
                    ],
                    'default_post' => [
                        'pattern' => ':controller',
                        'controller' => 1,
                        'action' => 'post',
                        'methods' => ['POST'],
                    ],
                    'default_get' => [
                        'pattern' => ':controller/:int',
                        'controller' => 1,
                        'action' => 'get',
                        'params' => 2,
                        'methods' => ['GET'],
                    ],
                    'default_patch' => [
                        'pattern' => ':controller/:int',
                        'controller' => 1,
                        'action' => 'patch',
                        'params' => 2,
                        'methods' => ['PATCH'],
                    ],
                    'default_put' => [
                        'pattern' => ':controller/:int',
                        'controller' => 1,
                        'action' => 'put',
                        'params' => 2,
                        'methods' => ['PUT'],
                    ],
                    'default_delete' => [
                        'pattern' => ':controller/:int',
                        'controller' => 1,
                        'action' => 'delete',
                        'params' => 2,
                        'methods' => ['DELETE'],
                    ],
                ],
            ],
        ],
    ],
    'cli' => [
        'default' => false,
    ],
];

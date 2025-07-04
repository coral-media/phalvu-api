<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller\Api;

use App\Controller\AbstractController;
use Phalcon\Http\ResponseInterface;

abstract class AbstractApiController extends AbstractController
{
    protected function json(array $data, int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setContentType('application/json', 'utf-8')
            ->setJsonContent($data)
        ;
    }
}

<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller;

class ErrorController extends Controller
{
    public function notFoundAction(): ?ResponseInterface
    {
        $accept = $this->request->getBestAccept();

        $this->logger->error(
            \sprintf(
                '%s, %s',
                json_encode(
                    [
                        'code' => 404,
                        "title" => "Not Found",
                        "detail" => "The requested resource does not exist.",
                    ],
                ),
                $this->request->getURI(),
            ),
        );

        if (str_starts_with($accept, 'application/json')) {
            return $this
                ->response
                ->setStatusCode(404, 'Not Found')
                ->setContentType('application/json')
                ->setJsonContent([
                    'error' => [
                        'code' => 404,
                        "title" => "Not Found",
                        "detail" => "The requested resource does not exist.",
                    ],
                ])
            ;
        }

        $this->response->setStatusCode(404, 'Not Found');
        $this->view->pick('error/404'); // If using views

        $this->view->setVars([
            'code' => 404,
            'title' => 'Not Found',
            'detail' => 'The requested resource does not exist.',
        ]);

        return null;
    }
}

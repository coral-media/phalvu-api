<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Middleware;

use Phalcon\Di\Injectable;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Encryption\Security\JWT\Token\Parser;
use Phalcon\Events\Event;
use Phalcon\Http\Response;
use Phalcon\Mvc\Dispatcher;

class JwtTokenAuth extends Injectable
{
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher): bool
    {
        $controller = $dispatcher->getControllerName();
        $action     = $dispatcher->getActionName();

        // Allow login endpoint without token
        if ('security' === $controller && 'login' === $action) {
            return true;
        }

        $authHeader = $this->request->getHeader('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->unauthorized('Missing or malformed Authorization header');
        }

        $tokenString = trim(str_replace('Bearer', '', $authHeader));

        try {
            $parser = new Parser();
            $token  = $parser->parse($tokenString);

            $signer = new Hmac();
            $secret = $this->config->get('jwt')->secret;
            ;

            if (!$token->verify($signer, $secret)) {
                return $this->unauthorized('Invalid token signature');
            }

            if ($token->getClaims()->has('exp') && time() >= $token->getClaims()->get('exp')) {
                return $this->unauthorized('Token expired');
            }

            // Share user ID in DI
            $this->di->setShared('authUserId', fn () => (int) $token->getClaims()->get('sub'));

            return true;

        } catch (\Throwable $e) {
            return $this->unauthorized('Invalid token format');
        }
    }

    private function unauthorized(string $message): bool
    {
        $response = new Response();
        $response->setStatusCode(401);
        $response->setJsonContent(['error' => $message]);
        $response->send();
        return false;
    }

}

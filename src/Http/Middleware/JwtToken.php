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
use Random\RandomException;

class JwtToken extends Injectable
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

        if (!$authHeader || 0 !== stripos($authHeader, 'bearer ')) {
            $this->unauthorized('Missing or malformed Authorization header');
            return false;
        }

        $tokenString = trim(str_ireplace('bearer', '', $authHeader));

        try {
            $parser = new Parser();
            $token  = $parser->parse($tokenString);

            $signer = new Hmac();
            $secret = $this->config->get('jwt')->secret;

            if (!$token->verify($signer, $secret)) {
                $this->unauthorized('Invalid token signature');
                return false;
            }

            if ($token->getClaims()->has('exp') && time() >= $token->getClaims()->get('exp')) {
                $this->unauthorized('Token expired');
                return false;
            }

            // Share user ID in DI
            $this->di->setShared('authUserId', fn () => (int) $token->getClaims()->get('sub'));

            return true;

        } catch (\Throwable $e) {
            $this->unauthorized('Invalid token format');
        }
        return false;
    }

    private function unauthorized(string $message): void
    {
        $this->response->setStatusCode(401);
        $this->response->setJsonContent(['error' => $message]);
    }

    /**
     * Generates a secure random string equivalent to:
     * tr -dc 'A-Za-z0-9!@#$%^&*()-_=+[]{}<>?' </dev/urandom | head -c 32
     *
     * @throws RandomException
     */
    public static function generateRandomSecret(int $length = 32): string
    {
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()-_=+[]{}<>?';
        $max = \strlen($charset) - 1;
        $secret = '';

        for ($i = 0; $i < $length; ++$i) {
            $secret .= $charset[random_int(0, $max)];
        }

        return $secret;
    }

}

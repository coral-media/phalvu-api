<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller\Api;

use App\Model\User;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Exceptions\ValidatorException;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Http\Response;

class SecurityController extends AbstractApiController
{
    public function loginAction(): Response
    {
        $response = new Response();

        // Parse JSON body
        $json = $this->request->getJsonRawBody(true); // as associative array

        if (empty($json['email']) || empty($json['password'])) {
            return $this->json(['error' => 'Missing credentials'], 400);
        }

        $email = $json['email'];
        $password = $json['password'];

        $user = User::findByEmail($email);

        if (!$user || !$user->verifyPassword($password)) {
            return $this->json(['error' => 'Invalid credentials'], 401);
        }

        // JWT setup
        $signer = new Hmac();
        $secret = $this->config->get('jwt')->secret;

        $issuedAt   = time();
        $expiresAt  = $issuedAt + $this->config->get('jwt')->expiration;

        $builder = new Builder($signer);
        try {
            $builder
                ->setIssuer('api')
                ->setAudience('web')
                ->setSubject(
                    json_encode(
                        [
                            'email' => $user->email,
                        ],
                    ),
                )
                ->setIssuedAt($issuedAt)
                ->setExpirationTime($expiresAt)
                ->setNotBefore($issuedAt)
                ->setPassphrase($secret)
            ;

            $token = $builder->getToken()->getToken();
        } catch (ValidatorException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode());
        }

        return $this->json([
            'token'      => $token,
            'expires_at' => $expiresAt,
        ]);
    }
}

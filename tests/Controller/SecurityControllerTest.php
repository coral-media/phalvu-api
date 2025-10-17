<?php

namespace App\Tests\Controller;

use App\Model\User;
use App\Tests\Helper\JwtParser;
use App\Tests\KernelTestCase;
use App\Tests\Stub\FakeRequest;
use Phalcon\Mvc\Model\TransactionInterface;

use Phalcon\Mvc\Model\Transaction\Manager as TxManager;

class SecurityControllerTest extends KernelTestCase
{
    protected User $user;
    protected TransactionInterface $transaction;

    protected const LOGIN_URL = '/api/security/login';

    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = self::LOGIN_URL;

        // Clean shared services that must be redefined
        $this->di->remove('request');
        $this->di->setShared('request', new FakeRequest());

        // Transaction for rollback
        $txManager = new TxManager();
        $this->transaction = $txManager->get();

        // Create test user
        $this->user = new User();
        $this->user->setTransaction($this->transaction);
        $this->user->email = 'test@example.com';
        $this->user->password = 'testpass';
        $this->user->active = true;
        $this->user->save();
    }

    protected function tearDown(): void
    {
        $_SERVER = [];
        $this->di->remove('authUserId');

        try {
            $this->transaction->rollback();
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function testLoginSuccess(): void
    {
        $request = $this->di->getShared('request');
        $request->setJsonRawBody([
            'email' => 'test@example.com',
            'password' => 'testpass',
        ]);

        $response = $this->app->handle(self::LOGIN_URL);

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $json);
        $this->assertArrayHasKey('expires_at', $json);
        $this->assertEquals(['email' => 'test@example.com'], JwtParser::getSubject($json['token']));
    }

    public function testLoginInvalidPassword(): void
    {
        $request = $this->di->getShared('request');
        $request->setJsonRawBody([
            'email' => 'test@example.com',
            'password' => 'wrongpass',
        ]);

        $response = $this->app->handle(self::LOGIN_URL);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testLoginMissingFields(): void
    {
        $request = $this->di->getShared('request');
        $request->setJsonRawBody(['email' => 'test@example.com']); // No password

        $response = $this->app->handle(self::LOGIN_URL);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testLoginUnknownUser(): void
    {
        $request = $this->di->getShared('request');
        $request->setJsonRawBody([
            'email' => 'ghost@example.com',
            'password' => 'anypass',
        ]);

        $response = $this->app->handle(self::LOGIN_URL);

        $this->assertEquals(401, $response->getStatusCode());
    }
}

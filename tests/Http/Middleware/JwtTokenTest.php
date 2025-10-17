<?php

namespace App\Tests\Http\Middleware;

use App\Http\Middleware\JwtToken;
use App\Kernel;
use App\Tests\KernelTestCase;
use Phalcon\Application\AbstractApplication;
use Phalcon\Di\DiInterface;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Exceptions\ValidatorException;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Event;

class JwtTokenTest extends KernelTestCase
{
    protected DiInterface $di;
    protected AbstractApplication $app;
    protected JwtToken $middleware;

    protected function setUp(): void
    {
        parent::setUp();

        $this->middleware = new JwtToken();
        $this->middleware->setDI($this->di);
    }

    protected function tearDown(): void
    {
        $_SERVER = [];
        $this->di->remove('authUserId');
    }

    public function testAllowsLoginRoute(): void
    {
        $dispatcher = $this->mockDispatcher('security', 'login');

        $result = $this->middleware->beforeExecuteRoute(
            new Event('dispatch:beforeExecuteRoute'),
            $dispatcher
        );

        $this->assertTrue($result);
    }

    public function testRejectsMissingAuthorizationHeader(): void
    {
        $dispatcher = $this->mockDispatcher('api', 'index');

        ob_start();
        $result = $this->middleware->beforeExecuteRoute(new Event('dispatch:beforeExecuteRoute'), $dispatcher);
        ob_end_clean();

        $this->assertFalse($result);
    }

    public function testRejectsInvalidTokenFormat(): void
    {
        $this->setAuthorizationHeader('Bearer not.a.jwt.token');
        $dispatcher = $this->mockDispatcher('api', 'index');

        ob_start();
        $result = $this->middleware->beforeExecuteRoute(new Event('dispatch:beforeExecuteRoute'), $dispatcher);
        ob_end_clean();

        $this->assertFalse($result);
    }

    /**
     * @throws ValidatorException
     */
    public function testRejectsExpiredToken(): void
    {
        $secret = $this->di->getShared('config')->jwt->secret;

        $issuedAt   = time();
        $expiresAt  = $issuedAt + 1;

        $token = $this->generateToken($secret, '99', $issuedAt, $expiresAt);
        $this->setAuthorizationHeader($token);
        sleep(2);

        $dispatcher = $this->mockDispatcher('default', 'index');

        ob_start();
        $result = $this->middleware->beforeExecuteRoute(new Event('dispatch:beforeExecuteRoute'), $dispatcher);
        ob_end_clean();

        $this->assertFalse($result);
    }

    public function testAcceptsValidToken(): void
    {
        $secret = $this->di->getShared('config')->jwt->secret;
        $this->setAuthorizationHeader($this->generateToken($secret, 42));
        $dispatcher = $this->mockDispatcher('default', 'index');

        ob_start();
        $result = $this->middleware->beforeExecuteRoute(new Event('dispatch:beforeExecuteRoute'), $dispatcher);
        ob_end_clean();

        $this->assertTrue($result);
        $this->assertEquals(42, $this->di->getShared('authUserId'));
    }

    private function setAuthorizationHeader(?string $token): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = sprintf('Bearer %s', $token);
    }

    private function mockDispatcher(string $controller, string $action): Dispatcher
    {
        $dispatcher = $this->createMock(Dispatcher::class);
        $dispatcher->method('getControllerName')->willReturn($controller);
        $dispatcher->method('getActionName')->willReturn($action);
        return $dispatcher;
    }

    /**
     * @throws ValidatorException
     */
    private function generateToken(string $secret, int $userId, ?int $iat = null, ?int $exp = null): string
    {
        $iat = $iat ?? time();
        $exp = $exp ?? ($iat + 3600);

        return (new Builder(new Hmac()))
            ->setSubject((string) $userId)
            ->setIssuedAt($iat)
            ->setNotBefore($iat)
            ->setExpirationTime($exp)
            ->setPassphrase($secret)
            ->getToken()
            ->getToken();
    }
}
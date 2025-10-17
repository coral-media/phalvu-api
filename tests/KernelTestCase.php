<?php

namespace App\Tests;

use App\Kernel;
use Phalcon\Application\AbstractApplication;
use Phalcon\Di\DiInterface;
use PHPUnit\Framework\TestCase;

abstract class KernelTestCase extends TestCase
{
    protected DiInterface $di;
    protected AbstractApplication $app;
    protected function setUp(): void
    {
        $kernel = new Kernel();
        $this->app = $kernel->getApplication();
        $this->di = $this->app->getDI();
    }
}
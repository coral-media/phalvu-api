<?php

namespace App\Tests;

use Phalcon\Db\Adapter\AdapterInterface;
use Phalcon\Di\DiInterface;

trait DatabaseTrait
{
    protected DiInterface $di;
    protected AdapterInterface $db;

    protected function dbUp(): void
    {
        $this->db = $this->di->getShared('db');
        $this->db->begin();
    }

    protected function dbDown(): void
    {
        $this->db->rollback();
    }
}

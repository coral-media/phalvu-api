<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Migrations;

use Phalcon\Db\Adapter\Pdo\AbstractPdo;

interface MigrationInterface
{
    public function up(AbstractPdo $db): void;

    public function down(AbstractPdo $db): void;
}

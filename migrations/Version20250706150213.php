<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Migrations;

use Phalcon\Db\Adapter\Pdo\AbstractPdo;
use Phalcon\Db\Column;

// Users table
class Version20250706150213 implements MigrationInterface
{
    public function up(AbstractPdo $db): void
    {
        $db->createTable(
            'security_users',
            '',
            [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'size'          => 10,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'primary'       => true,
                        ],
                    ),
                    new Column(
                        'email',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 255,
                            'notNull' => true,
                        ],
                    ),
                    new Column(
                        'password',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 255,
                            'notNull' => true,
                        ],
                    ),
                    new Column(
                        'created_at',
                        [
                            'type'    => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'default' => 'CURRENT_TIMESTAMP',
                        ],
                    ),
                    new Column(
                        'updated_at',
                        [
                            'type'    => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'default' => 'CURRENT_TIMESTAMP',
                        ],
                    ),
                ],
            ],
        );
    }

    public function down(AbstractPdo $db): void
    {
        $db->dropTable('security_users');
    }
}

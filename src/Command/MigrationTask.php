<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use Phalcon\Cli\Task;
use Phalcon\Db\Adapter\Pdo\AbstractPdo;
use Phalcon\Db\Enum;

class MigrationTask extends Task
{
    public function runAction(...$params): void
    {
        $direction = 'up';
        $targetVersion = null;

        foreach ($params as $param) {
            if ('--down' === $param) {
                $direction = 'down';
            }

            if (str_starts_with($param, '--version=')) {
                $targetVersion = trim(substr($param, \strlen('--version=')));
            }
        }

        $config = $this->di->getShared('config')->get('migrations');
        $migrationsDir = rtrim($config->get('directory'), '/');
        $namespace = $config->get('namespace');
        $logTable = $config->get('logTable', 'migrations_log');

        /** @var AbstractPdo $db */
        $db = $this->di->getShared('db');

        // Ensure the log table exists
        $this->ensureLogTable($db, $logTable);

        $executedVersions = array_column(
            $db->fetchAll("SELECT version FROM {$logTable} ORDER BY version ASC"),
            'version',
        );

        $files = glob("{$migrationsDir}/Version*.php");
        sort($files);

        if ('down' === $direction) {
            // Reverse the order for down migrations
            $files = array_reverse($files);
        }

        foreach ($files as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);

            if (null !== $targetVersion && !str_contains($className, $targetVersion)) {
                continue;
            }

            if ('up' === $direction && \in_array($className, $executedVersions, true)) {
                continue; // Already migrated
            }

            if ('down' === $direction && !\in_array($className, $executedVersions, true)) {
                continue; // Not migrated yet
            }

            require_once $file;
            $fqcn = $namespace . '\\' . $className;

            if (!class_exists($fqcn)) {
                echo "Class {$fqcn} not found.\n";
                continue;
            }

            $migration = new $fqcn();

            if (!method_exists($migration, $direction)) {
                echo "Method {$direction} not found in {$fqcn}\n";
                continue;
            }

            echo "Running {$direction} on {$className}...\n";
            $migration->{$direction}($db);

            if ('up' === $direction) {
                $this->logMigration($db, $logTable, $className);
            } else {
                $db->execute("DELETE FROM {$logTable} WHERE version = ?", [$className]);
            }

            if (null !== $targetVersion) {
                // Stop after handling the specified version
                break;
            }
        }

        echo "Migration {$direction} completed.\n";
    }

    public function makeAction(...$params): void
    {
        $description = $params[0] ?? null;
        if (!$description) {
            echo "Please provide a description for the migration.\n";
            return;
        }

        $config = $this->di->getShared('config')->get('migrations');
        $directory = rtrim($config->get('directory'), '/');
        $namespace = $config->get('namespace');

        $timestamp = date('YmdHis');
        $className = "Version" . $timestamp;
        $filename = "{$directory}/{$className}.php";

        $descriptionComment = '// ' . ucfirst(str_replace('_', ' ', $description));

        $template = <<<PHP
<?php
declare(strict_types=1);

namespace {$namespace};

use Phalcon\Db\Adapter\Pdo\AbstractPdo;

{$descriptionComment}
class {$className} implements MigrationInterface
{
    public function up(AbstractPdo \$db): void
    {
        // TODO: implement migration logic
    }

    public function down(AbstractPdo \$db): void
    {
        // TODO: revert migration logic
    }
}
PHP;

        file_put_contents($filename, $template);
        echo "Created migration: {$filename}\n";
    }

    private function ensureLogTable(AbstractPdo $db, string $table): void
    {
        $db->execute("CREATE TABLE IF NOT EXISTS {$table} (version VARCHAR(255) PRIMARY KEY, executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
    }

    private function getExecutedVersions(AbstractPdo $db, string $table): array
    {
        $result = $db->fetchAll("SELECT version FROM {$table}", Enum::FETCH_ASSOC);
        return array_column($result, 'version');
    }

    private function logMigration(AbstractPdo $db, string $table, string $version): void
    {
        $db->insertAsDict($table, [
            'version' => $version,
            'executed_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

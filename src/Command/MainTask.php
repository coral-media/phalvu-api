<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use Phalcon\Cli\Task;
use Random\RandomException;

class MainTask extends Task
{
    /**
     * @throws RandomException
     */
    public function environmentAction(): void
    {
        $envFile = BASE_PATH . '/.env';

        if (!file_exists($envFile) || !is_writable($envFile)) {
            echo ".env file does not exist or is not writable.\n";
            return;
        }

        // Use internal generator instead of shell command
        $jwtSecret = $this->generateRandomSecret(32);
        $appSecret = sha1($this->generateRandomSecret(32));

        $contents = file_get_contents($envFile);

        // Replace or add APP_SECRET and JWT_SECRET
        $replacements = [
            'APP_SECRET' => $appSecret,
            'JWT_SECRET' => $jwtSecret,
        ];

        foreach ($replacements as $key => $value) {
            if (preg_match("/^{$key}=.*$/m", $contents)) {
                $contents = preg_replace("/^{$key}=.*$/m", "{$key}='{$value}'", $contents);
            } else {
                $contents .= PHP_EOL . "{$key}='{$value}'";
            }
        }

        file_put_contents($envFile, $contents);

        echo "Environment secrets updated successfully:\n";
        echo "- APP_SECRET: {$appSecret}\n";
        echo "- JWT_SECRET: {$jwtSecret}\n";
    }

    /**
     * Generates a secure random string equivalent to:
     * tr -dc 'A-Za-z0-9!@#$%^&*()-_=+[]{}<>?' </dev/urandom | head -c 32
     *
     * @throws RandomException
     */
    private function generateRandomSecret(int $length = 32): string
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

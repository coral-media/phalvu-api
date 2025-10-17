<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Http\Middleware\JwtToken;
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
        $jwtSecret = JwtToken::generateRandomSecret();
        $appSecret = sha1(rand() . rand() . rand());

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
}

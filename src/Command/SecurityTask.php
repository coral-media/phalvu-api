<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Model\User;
use Phalcon\Cli\Task;

class SecurityTask extends Task
{
    public function createUserAction(): void
    {
        $email = $this->prompt('Email: ');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email format.\n";
            return;
        }

        $password = $this->promptHidden('Password: ');
        if (\strlen($password) < 6) {
            echo "Password must be at least 6 characters long.\n";
            return;
        }

        // Check if user already exists
        if (User::findByEmail($email)) {
            echo "A user with that email already exists.\n";
            return;
        }

        $user = new User();
        $user->email = $email;
        $user->password = $password;
        $user->active = true;

        if ($user->save()) {
            echo "User created successfully.\n";
        } else {
            echo "Failed to create user:\n";
            foreach ($user->getMessages() as $message) {
                echo " - {$message}\n";
            }
        }
    }

    public function changePasswordAction(): void
    {
        $email = $this->prompt('Email: ');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email format.\n";
            return;
        }

        $user = User::findByEmail($email);
        if (!$user) {
            echo "User not found.\n";
            return;
        }

        $newPassword = $this->promptHidden('New Password: ');
        $confirmPassword = $this->promptHidden('Confirm Password: ');

        if (\strlen($newPassword) < 6) {
            echo "Password must be at least 6 characters long.\n";
            return;
        }

        if ($newPassword !== $confirmPassword) {
            echo "Passwords do not match.\n";
            return;
        }

        $user->password = $newPassword;

        if ($user->save()) {
            echo "Password updated successfully.\n";
        } else {
            echo "Failed to update password:\n";
            foreach ($user->getMessages() as $message) {
                echo " - {$message}\n";
            }
        }
    }

    protected function prompt(string $label): string
    {
        echo $label;
        return trim(fgets(STDIN));
    }

    protected function promptHidden(string $label): string
    {
        echo $label;
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            // No native hidden prompt on Windows
            return trim(fgets(STDIN));
        }

        system('stty -echo');
        $value = trim(fgets(STDIN));
        system('stty echo');
        echo PHP_EOL;
        return $value;
    }
}

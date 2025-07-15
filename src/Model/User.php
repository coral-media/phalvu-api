<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Model;

use App\Model\Traits\TimestampableTrait;
use Phalcon\Encryption\Security;

class User extends AbstractModel
{
    use TimestampableTrait;

    public ?int $id = null;
    public string $email;
    public string $password;
    public bool $active;

    public function initialize(): void
    {
        $this->setSource('security_users');
        $this->addTimestampableBehavior();
    }

    public function beforeSave(): void
    {
        if ($this->hasChanged('password')) {
            $this->password = $this->getDI()->get('security')->hash($this->password);
        }
    }

    public static function findByEmail(string $email): ?self
    {
        return self::findFirst([
            'conditions' => 'email = :email:',
            'bind'       => ['email' => $email],
        ]);
    }

    public function verifyPassword(string $password): bool
    {
        return $this->getDI()->get('security')->checkHash($password, $this->password);
    }

    public function columnMap(): array
    {
        return [
            'id'         => 'id',
            'email'      => 'email',
            'password'   => 'password',
            'created_at' => 'createdAt',
            'updated_at' => 'updatedAt',
        ];
    }
}

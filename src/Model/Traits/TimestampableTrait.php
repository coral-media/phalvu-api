<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Model\Traits;

use Phalcon\Mvc\Model\BehaviorInterface;

/**
 * @method addBehavior(BehaviorInterface $behavior)
 */
trait TimestampableTrait
{
    public ?string $createdAt = null;
    public ?string $updatedAt = null;

    public function addTimestampableBehavior(): void
    {
        $this->addBehavior(
            new \Phalcon\Mvc\Model\Behavior\Timestampable([
                'beforeCreate' => [
                    'field'  => 'created_at',
                    'format' => 'Y-m-d H:i:s',
                ],
                'beforeUpdate' => [
                    'field'  => 'updated_at',
                    'format' => 'Y-m-d H:i:s',
                ],
            ]),
        );
    }
}

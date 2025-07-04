<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use Phalcon\Cli\Task;

class MainTask extends Task
{
    public function mainAction(): void
    {
        echo "CLI Task is working\n";
    }
}

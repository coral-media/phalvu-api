<?php

declare(strict_types=1);

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

use App\Kernel;
use Psr\Log\LoggerInterface;

require_once \dirname(__DIR__) . '/vendor/autoload.php';

try {
    Dotenv\Dotenv::createMutable(BASE_PATH)->load();
    $application = (new Kernel(false))->run();

} catch (Throwable $e) {
    /** @var LoggerInterface $logger */
    //    $logger = container('logger');
    //    $logger->error(sprintf('%s', $e->getMessage()));
    echo $e->getMessage(), '<br>';
    echo nl2br(htmlentities($e->getTraceAsString()));
}

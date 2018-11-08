<?php
declare(strict_types=1);

/**
 * @deprecated 0.2:1.0 Use of package root as web root is deprecated in favor of `public/` directory.
 */

use Rarst\ReleaseBelt\Application;

trigger_error('Use of package root as web root is deprecated in favor of `public/` directory.', E_USER_DEPRECATED);

require __DIR__.'/vendor/autoload.php';

$configPath = __DIR__.'/config.php';

$app = new Application(
    file_exists($configPath) ? require $configPath : []
);

$app->run();

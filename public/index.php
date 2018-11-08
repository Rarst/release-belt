<?php
declare(strict_types=1);

use Rarst\ReleaseBelt\Application;

require __DIR__.'/../vendor/autoload.php';

$configPath = __DIR__.'/../config/config.php';

$app = new Application(
    file_exists($configPath) ? require $configPath : []
);

$app->run();

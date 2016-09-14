<?php

use Rarst\ReleaseBelt\Application;
use Silex\Provider\SecurityServiceProvider;

require __DIR__.'/vendor/autoload.php';

$configPath = __DIR__.'/config.php';

$app = new Application(
    file_exists($configPath) ? require $configPath : []
);

if (empty($app['security.default_encoder'])) {
    $app->register(new SecurityServiceProvider());
}

if (! empty($argv[1])) {

    $encoder = $app['security.default_encoder'];
    echo  $encoder->encodePassword($argv[1], '');
}

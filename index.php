<?php

use Rarst\ReleaseBelt\Application;

require __DIR__ . '/vendor/autoload.php';

$app = new Application([
//    'debug' => true,
]);

$app->run();

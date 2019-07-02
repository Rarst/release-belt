<?php
declare(strict_types=1);

echo password_hash($argv[1], PASSWORD_DEFAULT) . PHP_EOL;

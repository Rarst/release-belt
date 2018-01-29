<?php

$commit = escapeshellarg(exec('git rev-list --tags --max-count=1'));
$tag    = escapeshellarg(exec("git describe --tags {$commit}"));

passthru("git checkout {$tag}");

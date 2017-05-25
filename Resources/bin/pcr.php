<?php

set_time_limit(0);

require __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Pcr\Command\PcrCommand;
use Pcr\Command\SqliteCommand;

$application = new Application();
$application->add(new PcrCommand());
$application->add(new  SqliteCommand());
$application->run();
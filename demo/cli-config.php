<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Jad\Database\Manager;

// replace with file to your own project bootstrap
require_once 'bootstrap.php';

// replace with mechanism to retrieve EntityManager in your app
$em = Manager::getInstance()->getEm();

$platform = $em->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping(' ', 'string');

return ConsoleRunner::createHelperSet($em);

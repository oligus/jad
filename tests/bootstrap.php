<?php

require_once __DIR__.'/../vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use Jad\Database\Manager;

$paths = array(realpath(__DIR__ . '/Database/Entities' ));

$isDevMode = true;

$connectionParams = array(
    'url' => 'sqlite:///' . realpath(__DIR__ . '/../tests/fixtures' ) .'/test_db.sqlite'
);

$config = Setup::createConfiguration($isDevMode);
$driver = new AnnotationDriver(new AnnotationReader(), $paths);

AnnotationRegistry::registerLoader('class_exists');
$config->setMetadataDriverImpl($driver);

$em = EntityManager::create($connectionParams, $config);

$manager = Manager::getInstance();
$manager->setEm($em);

// Clear genres
$rsm = new ResultSetMapping();

$query = $em->createNativeQuery('delete from genres;', $rsm);
$query->getResult();

$query = $em->createNativeQuery(' delete from sqlite_sequence where name="genres"', $rsm);
$query->getResult();

<?php

use Jad\Jad;
use Jad\Map\AnnotationsMapper;
use Jad\Database\Manager;
use Jad\Configure;

require './bootstrap.php';

$em = Manager::getInstance()->getEm();

$mapper = new AnnotationsMapper($em);

Configure::getInstance()->setConfig('debug', true);
$jad = new Jad($mapper);
$jad->setPathPrefix('/api/jad');

$jad->jsonApiResult();

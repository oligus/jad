<?php

use Jad\Jad;
use Jad\Map\AnnotationsMapper;
use Jad\Database\Manager;
use Jad\Configure;

require './bootstrap.php';

$em = Manager::getInstance()->getEm();

$mapper = new AnnotationsMapper($em);

$config = Configure::getInstance();
$config->setConfig('debug', true);
$config->setConfig('max_page_size', 100);

$jad = new Jad($mapper);
$jad->setPathPrefix('/api/jad');

$jad->jsonApiResult();

<?php

use Jad\Jad;
use Jad\Map\AnnotationsMapper;
use Jad\Database\Manager;

require '../bootstrap.php';

$em = Manager::getInstance()->getEm();

$mapper = new AnnotationsMapper($em);

$jad = new Jad($mapper);
$jad->setPathPrefix('/api/jad');

echo $jad->jsonApiResult();

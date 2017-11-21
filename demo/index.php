<?php

use Jad\Jad;
use Jad\Map\AnnotationsMapper;
use Jad\Database\Manager;
use Jad\Configure;
use Jad\Response\Error;

require './bootstrap.php';

try {
    $em = Manager::getInstance()->getEm();
    $mapper = new AnnotationsMapper($em);

    $config = Configure::getInstance();
    $config->setConfig('debug', true);
    $config->setConfig('max_page_size', 100);
    $config->setConfig('validation', true);

    $jad = new Jad($mapper);
    $jad->setPathPrefix('/api/jad');

    $jad->jsonApiResult();
} catch (\Exception $e) {
    $error = new Error($e);
    $error->render();
}


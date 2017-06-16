<?php

namespace Jad\Map\Annotations;

use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class Head implements Annotation
{
    /**
     * @var string
     */
    public $type;
}
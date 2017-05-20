<?php

namespace Jad\Map;

use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class Annotations implements Annotation
{
    /**
     * @var string
     */
    public $type;
}
<?php

namespace Jad\Map\Annotations;

use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class Header implements Annotation
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var bool
     */
    public $paginate;
}
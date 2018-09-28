<?php declare(strict_types=1);

namespace Jad\Map\Annotations;

use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Attribute implements Annotation
{
    /**
     * @var boolean
     */
    public $visible;

    /**
     * @var boolean
     */
    public $readOnly;
}
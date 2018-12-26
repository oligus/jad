<?php declare(strict_types=1);

namespace Jad\Map\Annotations;

use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @phan-file-suppress PhanPluginDescriptionlessCommentOnClass
 * @Target("CLASS")
 */
final class Header implements Annotation
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var boolean
     */
    public $readOnly;

    /**
     * @var bool
     */
    public $paginate;

    /**
     * @var string
     */
    public $aliases;
}

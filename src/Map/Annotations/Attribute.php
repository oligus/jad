<?php declare(strict_types=1);

namespace Jad\Map\Annotations;

use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @phan-file-suppress PhanPluginDescriptionlessCommentOnClass
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

    public function isReadOnly(): bool
    {
        return $this->readOnly ?? false;
    }

    public function isVisible(): bool
    {
        return $this->visible ?? true;
    }
}

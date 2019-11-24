<?php declare(strict_types=1);

namespace Jad\Map;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Jad\Common\Text;

/**
 * Class AnnotationsMapper
 * @package Jad\Map
 */
class AnnotationsMapper extends AbstractMapper
{
    /**
     * AnnotationsMapper constructor.
     * @param EntityManagerInterface $em
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);

        $reader = new AnnotationReader();
        $metaData = $em->getMetadataFactory()->getAllMetadata();

        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $meta */
        foreach ($metaData as $meta) {
            $head = $reader->getClassAnnotation($meta->getReflectionClass(), Annotations\Header::class);

            if (empty($head) || empty($head->type)) {
                continue;
            }

            $className = $meta->getName();
            $paginate = !!$head->paginate;
            $this->add($head->type, $className, $paginate);

            if (!empty($head->aliases)) {
                $aliases = explode(',', $head->aliases);

                foreach ($aliases as $type) {
                    $this->add($type, $className, $paginate);
                }
            }

            // Set auto aliases for relationship mappings that do not
            // @phan-suppress-next-line PhanUndeclaredMethod
            foreach ($meta->getAssociationMappings() as $associatedType => $associatedData) {
                $targetType = $associatedData['targetEntity'];
                $targetType = preg_replace('/.*\\\(.+?)/', '$1', $targetType);
                $associatedType = ucfirst($associatedType);

                if ($targetType === $associatedType) {
                    continue;
                }

                $this->add(
                    Text::kebabify($associatedType),
                    $associatedData['targetEntity'],
                    $paginate
                );
            }
        }
    }
}

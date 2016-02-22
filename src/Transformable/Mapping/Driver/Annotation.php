<?php

namespace MediaMonks\Doctrine\Transformable\Mapping\Driver;

use Gedmo\Mapping\Driver\AbstractAnnotationDriver;

/**
 * @author Robert Slootjes <robert@mediamonks.com>
 */
class Annotation extends AbstractAnnotationDriver
{
    const TRANSFORMABLE = 'MediaMonks\Doctrine\Mapping\Annotation\Transformable';

    /**
     * {@inheritDoc}
     */
    public function readExtendedMetadata($meta, array &$config)
    {
        $class = $this->getMetaReflectionClass($meta);
        foreach ($class->getProperties() as $property) {
            if ($meta->isMappedSuperclass && !$property->isPrivate() ||
                $meta->isInheritedField($property->name) ||
                isset($meta->associationMappings[$property->name]['inherited'])
            ) {
                continue;
            }
            if ($transformable = $this->reader->getPropertyAnnotation($property, self::TRANSFORMABLE)) {
                $field = $property->getName();

                $config['transformable'][] = [
                    'field'   => $field,
                    'name'    => $transformable->name
                ];
            }
        }
    }
}

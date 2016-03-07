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
            if ($this->isInherited($meta, $property)) {
                continue;
            }
            if ($transformable = $this->reader->getPropertyAnnotation($property, self::TRANSFORMABLE)) {
                $config['transformable'][] = $this->getConfig($property, $transformable);
            }
        }
    }

    /**
     * @param $meta
     * @param $property
     * @return bool
     */
    protected function isInherited($meta, $property)
    {
        return ($meta->isMappedSuperclass && !$property->isPrivate()
            || $meta->isInheritedField($property->name)
            || isset($meta->associationMappings[$property->name]['inherited'])
        );
    }

    /**
     * @param $property
     * @param $transformable
     * @return array
     */
    protected function getConfig($property, $transformable)
    {
        return [
            'field' => $property->getName(),
            'name'  => $transformable->name
        ];
    }
}

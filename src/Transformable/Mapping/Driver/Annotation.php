<?php

namespace MediaMonks\Doctrine\Transformable\Mapping\Driver;

use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Mapping\Driver\AbstractAnnotationDriver;
use JetBrains\PhpStorm\ArrayShape;
use MediaMonks\Doctrine\Mapping\Transformable;

/**
 * @author Robert Slootjes <robert@mediamonks.com>
 */
class Annotation extends AbstractAnnotationDriver
{
    const TRANSFORMABLE = 'MediaMonks\Doctrine\Mapping\Transformable';

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

    protected function isInherited(ClassMetadata $meta, \ReflectionProperty $property): bool
    {
        return ($meta->isMappedSuperclass && !$property->isPrivate()
            || $meta->isInheritedField($property->name)
            || isset($meta->associationMappings[$property->name]['inherited'])
        );
    }

    #[ArrayShape(['field' => "mixed", 'name' => "mixed"])] protected function getConfig(\ReflectionProperty $property, Transformable $transformable): array
    {
        return [
            'field' => $property->getName(),
            'name'  => $transformable->name
        ];
    }
}

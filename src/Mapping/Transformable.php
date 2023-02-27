<?php

namespace MediaMonks\Doctrine\Mapping;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Gedmo\Mapping\Annotation\Annotation as GedmoAnnotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Transformable implements GedmoAnnotation
{
    public function __construct(array $data = [], public string $name = 'noop')
    {
        $this->name = $data['name'] ?? $name;
    }
}

<?php

namespace MediaMonks\Doctrine\Mapping;

use Attribute;
use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Transformable implements Annotation
{
    public function __construct(array $data = [], public string $name = 'noop')
    {
        $this->name = $data['name'] ?? $name;
    }
}

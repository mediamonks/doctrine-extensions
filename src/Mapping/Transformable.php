<?php

namespace MediaMonks\Doctrine\Mapping;

use Attribute;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Transformable
{
    public function __construct(array $data = [], public string $name = 'noop')
    {
        $this->name = $data['name'] ?? $name;
    }
}

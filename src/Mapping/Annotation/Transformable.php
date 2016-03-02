<?php

namespace MediaMonks\Doctrine\Mapping\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class Transformable extends Annotation
{
    /**
     * @var string
     */
    public $name = 'noop';
}

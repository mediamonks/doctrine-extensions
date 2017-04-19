<?php

namespace MediaMonks\Doctrine\Transformable\Transformer\Traits;

use MediaMonks\Doctrine\Exception\InvalidArgumentException;

trait GetValue
{
    /**
     * @param $value
     * @return string
     * @throws InvalidArgumentException
     */
    public function getValue($value)
    {
        if (!is_object($value)) {
            return $value;
        }

        if (method_exists($value, '__toString')) {
            return $value->__toString();
        }

        throw new InvalidArgumentException(sprintf('class %s should implement __toString()', get_class($value)));
    }
}
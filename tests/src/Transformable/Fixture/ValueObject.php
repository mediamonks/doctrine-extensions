<?php

namespace Transformable\Fixture;

class ValueObject
{

    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public static function fromNative($value)
    {
        return new self($value);
    }

    public function toNative()
    {
        return $this->__toString();
    }

    public function __toString()
    {
        return $this->value;
    }
}
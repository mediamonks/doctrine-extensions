<?php

namespace Transformable\Fixture;

class InvalidValueObject
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
}
<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

abstract class AbstractHmacTransformer extends AbstractHashTransformer
{
    public function __construct(private string $key, array $options = [])
    {
        parent::__construct($options);
    }

    public function getKey(): string
    {
        return $this->key;
    }
}

<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

interface TransformerInterface
{
    /**
     * @param array $options
     * @return TransformerInterface
     */
    public function setOptions(array $options);

    /**
     * @param mixed $value
     * @return mixed
     */
    public function transform($value);

    /**
     * @param mixed $value
     * @return mixed
     */
    public function reverseTransform($value);
}
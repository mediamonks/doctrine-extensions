<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

abstract class AbstractHmacTransformer extends AbstractHashTransformer
{
    /**
     * @var string
     */
    private $key;

    /**
     * @param string $key
     * @param array $options
     */
    public function __construct($key, array $options = [])
    {
        $this->key = $key;

        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}

<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use MediaMonks\Doctrine\Transformable\Transformer\Traits\GetValue;
use Zend\Crypt\Hmac;

class ZendCryptHmacTransformer extends ZendCryptHashTransformer
{
    use GetValue;

    /**
     * @var string
     */
    protected $key;

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
     * @param string $value
     * @return string
     */
    public function transform($value)
    {
        return Hmac::compute($this->key, $this->algorithm, $this->getValue($value), $this->binary);
    }
}


<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Zend\Crypt\Hash;

class ZendCryptHashTransformer extends AbstractTransformer
{
    /**
     * @var string
     */
    protected $method = 'sha256';

    /**
     * @var bool
     */
    protected $binary = true;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if(isset($options['method'])) {
            $this->method = $options['method'];
        }
        if(isset($options['binary'])) {
            $this->binary = $options['binary'];
        }
    }

    /**
     * @param string $value
     * @return string
     */
    public function transform($value)
    {
        return Hash::compute($this->method, $value, $this->binary);
    }
}


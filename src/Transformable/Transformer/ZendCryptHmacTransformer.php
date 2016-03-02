<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Zend\Crypt\Hmac;

class ZendCryptHmacTransformer extends AbstractTransformer
{
    /**
     * @var string
     */
    protected $algorithm = 'sha256';

    /**
     * @var string
     */
    protected $key;

    /**
     * @var bool
     */
    protected $binary = true;

    /**
     * @param string $key
     * @param array $options
     */
    public function __construct($key, array $options = [])
    {
        $this->key = $key;

        if(isset($options['algorithm'])) {
            $this->algorithm = $options['algorithm'];
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
        return Hmac::compute($this->key, $this->algorithm, $value, $this->binary);
    }
}


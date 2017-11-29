<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

class DefuseCryptoEncryptKeyTransformer implements TransformerInterface
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var bool
     */
    private $binary = true;

    /**
     * @param $key
     * @param array $options
     */
    public function __construct($key, array $options = [])
    {
        if (is_string($key)) {
            $key = Key::loadFromAsciiSafeString($key);
        }
        $this->key = $key;
        $this->setOptions($options);
    }

    /**
     * @param array $options
     */
    protected function setOptions(array $options)
    {
        if (array_key_exists('binary', $options)) {
            $this->binary = $options['binary'];
        }
    }

    /**
     * @return bool
     */
    public function getBinary()
    {
        return $this->binary;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function transform($value)
    {
        return Crypto::encrypt($value, $this->key, $this->getBinary());
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function reverseTransform($value)
    {
        return Crypto::decrypt($value, $this->key, $this->getBinary());
    }

}

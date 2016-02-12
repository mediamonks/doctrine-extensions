<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Zend\Crypt\Symmetric\Mcrypt;
use Zend\Math\Rand;

class AES256Transformer extends AbstractTransformer
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var
     */
    protected $mcrypt;

    /**
     * AES256Encryptor constructor.
     * @param $key
     * @param Mcrypt|null $mcrypt
     */
    public function __construct($key, Mcrypt $mcrypt = null)
    {
        $this->key = md5($key);
        if(is_null($mcrypt)) {
            $mcrypt = new Mcrypt();
        }
        $mcrypt->setSalt(Rand::getBytes($mcrypt->getSaltSize(), true));
        $this->mcrypt = $mcrypt;
    }

    /**
     * @param string $value
     * @return string
     */
    public function transform($value)
    {
        return $this->mcrypt->encrypt($value);
    }

    /**
     * @param string $value
     * @return string
     */
    public function reverseTransform($value)
    {
        return $this->mcrypt->decrypt($value);
    }
}
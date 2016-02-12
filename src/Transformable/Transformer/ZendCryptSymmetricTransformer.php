<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Zend\Crypt\Symmetric\SymmetricInterface;
use Zend\Math\Rand;

class ZendCryptSymmetricTransformer extends AbstractTransformer
{
    /**
     * @var SymmetricInterface
     */
    protected $crypt;

    /**
     * @var bool
     */
    protected $hex = true;

    /**
     * @param SymmetricInterface $crypt
     * @param array $options
     */
    public function __construct(SymmetricInterface $crypt, array $options = [])
    {
        $this->crypt = $crypt;
        $this->setOptions($options);
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options = [])
    {
        if(isset($options['hex'])) {
            $this->setHex($options['hex']);
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function getHex()
    {
        return $this->hex;
    }

    /**
     * @param boolean $hex
     * @return $this
     */
    public function setHex($hex)
    {
        $this->hex = $hex;
        return $this;
    }

    /**
     * Implementation of EncryptorInterface encrypt method
     * @param string $data
     * @return string
     */
    public function transform($data)
    {
        $this->crypt->setSalt(Rand::getBytes($this->crypt->getSaltSize(), true));
        $data = $this->crypt->encrypt($data);
        if($this->getHex()) {
            $data = bin2hex($data);
        }
        return $data;
    }

    /**
     * Implementation of EncryptorInterface decrypt method
     * @param string $data
     * @return string
     */
    public function reverseTransform($data)
    {
        if($this->getHex()) {
            $data = hex2bin($data);
        }
        return $this->crypt->decrypt($data);
    }
}


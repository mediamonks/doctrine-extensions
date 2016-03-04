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
    protected $binary = true;

    /**
     * @var bool
     */
    protected $requireStrongRandomGenerator = true;

    /**
     * @param SymmetricInterface $crypt
     * @param array $options
     */
    public function __construct(SymmetricInterface $crypt, array $options = [])
    {
        $this->crypt = $crypt;
        if(isset($options['binary'])) {
            $this->binary = $options['binary'];
        }
        if(isset($options['requireStrongRandomGenerator'])) {
            $this->requireStrongRandomGenerator = $options['requireStrongRandomGenerator'];
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
     * @return bool
     */
    public function getRequireStrongRandomGenerator()
    {
        return $this->requireStrongRandomGenerator;
    }

    /**
     * @param string $value
     * @return string
     */
    public function transform($value)
    {
        $this->updateSalt();
        $value = $this->crypt->encrypt($value);
        if(!$this->getBinary()) {
            $value = bin2hex($value);
        }
        return $value;
    }

    /**
     * @param string $value
     * @return string
     */
    public function reverseTransform($value)
    {
        if(!$this->getBinary()) {
            $value = hex2bin($value);
        }
        return $this->crypt->decrypt($value);
    }

    /**
     *
     */
    protected function updateSalt()
    {
        $this->crypt->setSalt(Rand::getBytes($this->crypt->getSaltSize(), $this->getRequireStrongRandomGenerator()));
    }
}


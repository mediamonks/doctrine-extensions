<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Laminas\Crypt\BlockCipher;

class LaminasCryptSymmetricTransformer implements TransformerInterface
{
    /**
     * @var BlockCipher
     */
    private $crypt;

    private $defaultAlgo = 'aes';

    /**
     * @param array $options
     */
    public function __construct(string $key, array $options = [])
    {
        $this->crypt = BlockCipher::factory('openssl', $options['encryption_options'] ?? ['algo' => $this->defaultAlgo]);
        $this->crypt->setKey($key);
        $this->crypt->setBinaryOutput(true);

        $this->setOptions($options);
    }

    /**
     * @param array $options
     */
    protected function setOptions(array $options)
    {
        if (array_key_exists('binary', $options)) {
            $this->crypt->setBinaryOutput((bool)$options['binary']);
        }
    }

    /**
     * @return bool
     */
    public function getBinary(): bool
    {
        return $this->crypt->getBinaryOutput();
    }

    /**
     * @param string $value
     * @return string
     */
    public function transform($value): string
    {
        return $this->crypt->encrypt($value);
    }

    /**
     * @param string $value
     * @return string | null
     */
    public function reverseTransform($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return $this->crypt->decrypt($value);
    }

}

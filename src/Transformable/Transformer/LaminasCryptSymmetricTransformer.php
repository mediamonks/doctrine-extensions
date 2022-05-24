<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

use Laminas\Crypt\BlockCipher;

class LaminasCryptSymmetricTransformer implements TransformerInterface
{
    private BlockCipher $crypt;

    private string $defaultAlgo = 'aes';

    public function __construct(string $key, array $options = [])
    {
        $this->crypt = BlockCipher::factory('openssl', $options['encryption_options'] ?? ['algo' => $this->defaultAlgo]);
        $this->crypt->setKey($key);
        $this->crypt->setBinaryOutput(true);

        $this->setOptions($options);
    }

    protected function setOptions(array $options)
    {
        if (array_key_exists('binary', $options)) {
            $this->crypt->setBinaryOutput((bool)$options['binary']);
        }
    }

    public function getBinary(): bool
    {
        return $this->crypt->getBinaryOutput();
    }

    public function transform(?string $value): string|bool
    {
        if (empty($value)) {
            return false;
        }

        return $this->crypt->encrypt($value);
    }

    public function reverseTransform(?string $value): bool|string|null
    {
        if ($value === null) {
            return null;
        }

        return $this->crypt->decrypt($value);
    }

}

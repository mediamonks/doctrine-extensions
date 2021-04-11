[![Build Status](https://travis-ci.org/mediamonks/doctrine-extensions.svg?branch=master)](https://travis-ci.org/mediamonks/doctrine-extensions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/MediaMonks/doctrine-extensions/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mediamonks/doctrine-extensions/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/mediamonks/doctrine-extensions/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mediamonks/doctrine-extensions/?branch=master)
[![Total Downloads](https://poser.pugx.org/mediamonks/doctrine-extensions/downloads)](https://packagist.org/packages/mediamonks/doctrine-extensions)
[![Latest Stable Version](https://poser.pugx.org/mediamonks/doctrine-extensions/v/stable)](https://packagist.org/packages/mediamonks/doctrine-extensions)
[![Latest Unstable Version](https://poser.pugx.org/mediamonks/doctrine-extensions/v/unstable)](https://packagist.org/packages/mediamonks/doctrine-extensions)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/c42e43fd-9c7b-47e1-8264-3a98961e9236.svg)](https://insight.sensiolabs.com/projects/c69936a4-afbf-4889-8b15-cf041a056d43)
[![License](https://poser.pugx.org/mediamonks/doctrine-extensions/license)](https://packagist.org/packages/mediamonks/doctrine-extensions)

# MediaMonks Doctrine2 behavioral extensions

These extensions add more functionality to Doctrine2.

> Breaking changes! All Zend transformers are now renamed to Laminas.
  
## Transformable

This extension uses transform and reverseTransform methods to convert data to and from the database. This can for example be used to encrypt a field when it's sent to the database and it will be decrypted when it is retrieved from the database.

The field's value will only be transformed when the value changed which also makes it possible to implement only a transform function for one way transformations like hashing.

Currently, these adapters are provided in order of recommendation:

- HaliteSymmetricTransformer - Encrypt/decrypts the value
- DefuseCryptoEncryptKeyTransformer - Encrypt/decrypts the value
- PhpHashTransformer - Hashes the value
- PhpHmacTransformer - Hashes the value with a key
- LaminasCryptHashTransformer - Hashes the value
- LaminasCryptHmacTransformer - Hashes the value with a key
- LaminasCryptSymmetricTransformer - Encrypts/decrypts the value

You can easily create your own transformers by implementing the [TransformableInterface](src/Transformable/Transformer/TransformerInterface.php)

## System Requirements

You need:

- **PHP >= 7.3**

To use the library.

## Install

Install this package by using Composer.

```
$ composer require mediamonks/doctrine-extensions
```

## Security

If you discover any security related issues, please email devmonk@mediamonks.com instead of using the issue tracker.

# Documentation

Please refer to the files in the [/doc](/doc) folder.

# Credits

This package was inspired by/uses code from [gedmo/doctrine-extensions](https://packagist.org/packages/gedmo/doctrine-extensions).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

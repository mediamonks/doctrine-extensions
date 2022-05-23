[![Build Status](https://img.shields.io/github/workflow/status/mediamonks/doctrine-extensions/CI?label=CI&logo=github&style=flat-square)](https://github.com/mediamonks/doctrine-extensions/actions?query=workflow%3ACI)
[![Code Coverage](https://img.shields.io/codecov/c/gh/mediamonks/doctrine-extensions?label=codecov&logo=codecov&style=flat-square)](https://codecov.io/gh/mediamonks/doctrine-extensions)
[![Total Downloads](https://poser.pugx.org/mediamonks/doctrine-extensions/downloads)](https://packagist.org/packages/mediamonks/doctrine-extensions)
[![Latest Stable Version](https://poser.pugx.org/mediamonks/doctrine-extensions/v/stable)](https://packagist.org/packages/mediamonks/doctrine-extensions)
[![Latest Unstable Version](https://poser.pugx.org/mediamonks/doctrine-extensions/v/unstable)](https://packagist.org/packages/mediamonks/doctrine-extensions)
[![License](https://poser.pugx.org/mediamonks/doctrine-extensions/license)](https://packagist.org/packages/mediamonks/doctrine-extensions)

# MediaMonks Doctrine2 behavioral extensions

These extensions add more functionality to Doctrine2.

> Breaking changes!  
  All Zend transformers are now renamed to Laminas.  
  YAML support has been removed

> New features!  
  Attribute support
  
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
- LaminasCryptSymmetricTransformer - Encrypts/decrypts the value using openssl (Mcrypt is deprecated), with aes as default algorithm

You can easily create your own transformers by implementing the [TransformableInterface](src/Transformable/Transformer/TransformerInterface.php)

## System Requirements

You need:

- **PHP >= 8.0**

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

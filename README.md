[![Build Status](https://travis-ci.org/MediaMonks/doctrine-extensions.svg?branch=master)](https://travis-ci.org/MediaMonks/doctrine-extensions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/MediaMonks/doctrine-extensions/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/MediaMonks/doctrine-extensions/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/MediaMonks/doctrine-extensions/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/MediaMonks/doctrine-extensions/?branch=master)
[![Total Downloads](https://poser.pugx.org/mediamonks/doctrine-extensions/downloads)](https://packagist.org/packages/mediamonks/doctrine-extensions)
[![Latest Stable Version](https://poser.pugx.org/mediamonks/doctrine-extensions/v/stable)](https://packagist.org/packages/mediamonks/doctrine-extensions)
[![Latest Unstable Version](https://poser.pugx.org/mediamonks/doctrine-extensions/v/unstable)](https://packagist.org/packages/mediamonks/doctrine-extensions)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/c42e43fd-9c7b-47e1-8264-3a98961e9236.svg)](https://insight.sensiolabs.com/projects/c69936a4-afbf-4889-8b15-cf041a056d43)
[![License](https://poser.pugx.org/mediamonks/doctrine-extensions/license)](https://packagist.org/packages/mediamonks/doctrine-extensions)

# MediaMonks Doctrine2 behavioral extensions

These extensions add more functionality to Doctrine2.

## Transformable

This extension uses transform and reverseTransform methods to convert data to and from the database. This can for example be used to encrypt a field when it's sent to the database and it will be decrypted when it is retrieved from the database.

The field's value will only be transformed when the value changed which also makes it possible to implement only a transform function for one way transformations like hashing.

Currently these adapters are provided and require the [Zend\Crypt](https://packagist.org/packages/zendframework/zend-crypt) package to work.

- ZendCryptHashTransformer - Hashes the value
- ZendCryptHmacTransformer - Hashes the value with a key
- ZendCryptSymmetricTransformer - Encrypts/Decrypts the value

You can easily create your own transformers by implementing the [TransformableInterface](src/Transformable/Transformer/TransformerInterface.php)

# Documentation

Please refer to the files in the [/doc](/doc) folder.

# Credits

This package was inspired by/uses code from [gedmo/doctrine-extensions](https://packagist.org/packages/gedmo/doctrine-extensions).

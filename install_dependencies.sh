#!/bin/bash

sudo apt-get update
sudo apt-get install make build-essential automake php-pear

if [[ "$TRAVIS_PHP_VERSION" < "7.2" ]]; then
    sudo apt-get install libsodium-dev
fi;

pecl channel-update pecl.php.net

if [[ "$TRAVIS_PHP_VERSION" == "5.6" ]]; then
    pecl install libsodium-1.0.7
elif [[ "$TRAVIS_PHP_VERSION" < "7.2" ]]; then
    pecl install libsodium
fi;
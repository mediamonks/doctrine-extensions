#!/bin/bash

sudo apt-get update
sudo apt-get install make build-essential automake php5-dev php-pear

if [[ "$TRAVIS_PHP_VERSION" < "7.2" ]]; then
    git clone git://github.com/jedisct1/libsodium.git
    cd libsodium
    git checkout 1.0.15
    ./autogen.sh
    ./configure
    make check
    sudo make install
    cd ..
fi;

pecl channel-update pecl.php.net

if [[ "$TRAVIS_PHP_VERSION" == "5.6" ]]; then
    pecl install libsodium-1.0.7
fi;

if [[ "$TRAVIS_PHP_VERSION" > "5.6" ]] && [[ "$TRAVIS_PHP_VERSION" < "7.2" ]]; then
    pecl install libsodium
fi;
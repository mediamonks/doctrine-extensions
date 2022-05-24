FROM php:8.0-cli

RUN apt-get update && apt-get install -y git unzip && pecl install pcov && docker-php-ext-enable pcov && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

FROM php:8.1-fpm-alpine

WORKDIR /var/www/html
COPY src /var/www/html
COPY php /usr/local/etc/php
# Setup GD extension
RUN apk add --no-cache \
    freetype \
    libjpeg-turbo \
    libpng \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd \
    --with-freetype=/usr/include/ \
    # --with-png=/usr/include/ \ # No longer necessary as of 7.4; https://github.com/docker-library/php/pull/910#issuecomment-559383597
    --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable gd \
    && apk del --no-cache \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    && rm -rf /tmp/*

RUN apk add libzip-dev
RUN set -ex && apk --no-cache add postgresql-dev
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql zip bcmath

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN /usr/local/bin/composer install

#RUN chown -R sunya:www-data .
RUN  find . -type f -exec chmod 644 {} \;    
RUN  find . -type d -exec chmod 755 {} \;    
RUN  chmod -R 777 storage bootstrap
RUN  rm -R /var/www/html/storage/logs
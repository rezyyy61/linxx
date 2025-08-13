FROM php:8.2-fpm

ARG USER_UID=1000
ARG USER_GID=1000

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev libjpeg-dev libfreetype6-dev libwebp-dev \
    libzip-dev libxml2-dev \
    git unzip curl gnupg \
    ffmpeg poppler-utils \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get update && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        gd mysqli pdo_mysql bcmath zip opcache pcntl sockets

RUN pecl install redis \
    && docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN groupadd -g ${USER_GID} www \
    && useradd -u ${USER_UID} -g www -m www \
    && usermod -a -G www-data www

WORKDIR /var/www
COPY --chown=www:www . /var/www
RUN chown -R www:www /var/www

USER www
EXPOSE 9000
CMD ["php-fpm"]

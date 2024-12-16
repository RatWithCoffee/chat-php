FROM php:8.1-fpm

RUN apt-get update

# Установка необходимых пакетов для работы с MySQL, включая mysqli
RUN apt-get install -y default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql mysqli

# Копирование приложения в контейнер
COPY ./app /var/www/html

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Отключение opcache
RUN echo "opcache.enable=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

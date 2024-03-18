FROM php:8.2-fpm

RUN apt-get update && apt-get install -y nginx ...
COPY nginx.conf /etc/nginx/nginx.conf
COPY laravel.conf /etc/nginx/conf.d/default.conf

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN apt-get update && \
    apt-get install -y mysql-client && \
    rm -rf /var/lib/apt/lists/*

# Установка расширений
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www

# Копирование приложения
COPY . /var/www

# Установка зависимостей Composer
RUN composer clear-cache
RUN composer install

# Права на папку для кеша и логов
RUN chown -R www-data:www-data /var/www/storage
RUN chmod -R 775 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]

FROM php:8.2-fpm

# Instala extensiones necesarias para Laravel y PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Crea y entra al directorio del proyecto
WORKDIR /var/www/html

# Copia el código del proyecto
COPY . .

# Instala las dependencias de Laravel
RUN composer install --optimize-autoloader --no-dev

# Da permisos adecuados
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expone el puerto PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]

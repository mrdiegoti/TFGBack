FROM php:8.2-cli

# Instala dependencias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libssl-dev \
    && pecl install swoole \
    && docker-php-ext-enable swoole \
    && docker-php-ext-install pdo pdo_pgsql zip

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia todo el código
COPY . .

# Instala dependencias
RUN composer install --optimize-autoloader --no-dev

# Da permisos
RUN chmod -R 775 storage bootstrap/cache

# Expone el puerto HTTP de Octane
EXPOSE 8000

# Comando de inicio
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]

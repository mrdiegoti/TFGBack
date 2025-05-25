FROM php:8.2-cli

# Instalar herramientas del sistema necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libssl-dev \
    gcc \
    make \
    autoconf \
    pkg-config \
    && pecl install swoole \
    && docker-php-ext-enable swoole \
    && docker-php-ext-install pdo pdo_pgsql zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias PHP
RUN composer install --optimize-autoloader --no-dev

# Asignar permisos
RUN chmod -R 775 storage bootstrap/cache

# Puerto HTTP que usará Laravel Octane
EXPOSE 8000

# Comando de arranque
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]

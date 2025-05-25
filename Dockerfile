FROM php:8.2-fpm

# Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    mariadb-client \
    nano \
    vim \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia el proyecto Laravel
COPY . .

# Instala dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Permisos (ajusta según tus necesidades)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Expone el puerto (no necesario para php-fpm, pero útil si activas Laravel dev server)
EXPOSE 9000

# Comando por defecto: inicia PHP-FPM (se mantiene activo)
CMD ["php-fpm"]

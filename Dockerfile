# Usamos una imagen base oficial de PHP 8.2 o superior con FPM
FROM php:8.2-fpm

# Instalamos dependencias del sistema necesarias para Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    unzip

# Instalamos las extensiones de PHP necesarias para Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Instalamos Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecemos el directorio de trabajo en el contenedor
WORKDIR /var/www/html

# Copiamos el contenido del proyecto al contenedor
COPY . .

# Instalamos las dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Exponemos el puerto en el que Laravel escuchará
EXPOSE 8000

# Ejecutamos el servidor de desarrollo de Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
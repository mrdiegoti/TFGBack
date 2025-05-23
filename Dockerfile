# Imagen base oficial de PHP con FPM
FROM php:8.2-fpm

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Instala extensiones de PHP necesarias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Copia Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece directorio de trabajo
WORKDIR /var/www/html

# Copia solo composer.json y lock para instalar dependencias primero
COPY composer.json composer.lock ./

# Configura Git y limpia vendor
RUN git config --global --add safe.directory /var/www/html \
    && rm -rf vendor/ \
    && composer install --no-dev --optimize-autoloader --no-interaction

# Copia el resto del proyecto
COPY . .

# Expone el puerto de Laravel
EXPOSE 8000

# Comando por defecto para desarrollo
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

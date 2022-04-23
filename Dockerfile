FROM php:7.4-fpm

# Arguments defined in docker-compose.yml
ARG user=unilibrary
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mbstring exif pcntl bcmath gd pdo pdo_pgsql pgsql zip soap

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

COPY --chown=www-data:www-data . /var/www
RUN usermod -a -G www-data $user
RUN find /var/www -type f -exec chmod 644 {} \;
RUN find /var/www/ -type d -exec chmod 755 {} \;
RUN chown -R $user:www-data /var/www
RUN chown -R unilibrary:www-data /var/www/storage/app/public/

# Set working directory
WORKDIR /var/www

RUN chgrp -R www-data storage bootstrap/cache
RUN chmod -R ug+rwx storage bootstrap/cache
RUN chmod +x /var/www/init.sh
ADD custom-upload.ini /usr/local/etc/php/conf.d/custom-upload.ini

USER $user

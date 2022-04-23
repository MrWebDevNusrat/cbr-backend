#!/bin/bash

composer install
composer dump-autoload
php artisan jwt:secret
php artisan key:generate
php artisan storage:link
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan config:cache
php artisan migrate
php artisan db:seed
php-fpm

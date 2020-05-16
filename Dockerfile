FROM php:7.2-fpm-alpine
COPY ./EnerjoyApi/ /var/www/html
RUN docker-php-ext-install pdo pdo_mysql mysqli 

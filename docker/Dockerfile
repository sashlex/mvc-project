# FROM php:7.0-apache
FROM php:7-apache

# install php extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# .htaccess mod rewrite
RUN a2enmod rewrite

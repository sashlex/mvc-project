FROM php:7-apache

# PHP utilities
RUN docker-php-ext-configure gd --with-freetype-dir=/usr --with-png-dir=/usr --with-jpeg-dir=/usr

# install php extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql gd

# .htaccess mod rewrite
RUN a2enmod rewrite

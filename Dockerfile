FROM php:8.4-apache
RUN docker-php-ext-install mysqli
COPY public/ /var/www/html/
COPY app/ /var/www/app/

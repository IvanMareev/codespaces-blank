FROM php:8.4-apache


RUN docker-php-ext-install mysqli \
    && a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

COPY . /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader

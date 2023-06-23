FROM php:8.2-apache
RUN apt-get update && apt-get install -y libpq-dev curl unzip && docker-php-ext-install pdo pdo_pgsql
RUN cd /tmp && curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
# CMD ["composer", "install"]
COPY . /var/www/html
RUN composer install --no-interaction --optimize-autoloader
RUN a2enmod rewrite
RUN service apache2 restart
EXPOSE 80
CMD ["apache2-foreground"]
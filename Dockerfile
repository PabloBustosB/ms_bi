FROM php:8.2-apache
RUN apt-get update && apt-get install -y libpq-dev curl && docker-php-ext-install pdo pdo_pgsql
RUN cd /tmp && curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
RUN a2enmod rewrite 
RUN systemctl restart apache2
COPY . /var/www/html
EXPOSE 80
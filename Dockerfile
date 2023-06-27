FROM php:8.2-apache
FROM php:8.2-apache

# Instala las dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    curl \
    unzip

# Instala las extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_pgsql

# Instala Composer
RUN cd /tmp && curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Copia los archivos de tu aplicación al contenedor
COPY . /var/www/html

# Instala las dependencias utilizando Composer
RUN composer install --no-interaction --optimize-autoloader

# Habilita el módulo de Apache necesario para permitir reescritura de URL
RUN a2enmod rewrite

# Habilita el módulo de Apache necesario para configurar las cabeceras CORS
RUN a2enmod headers

# Configura las cabeceras CORS en el archivo de configuración de Apache
RUN echo "Header set Access-Control-Allow-Origin '*'" >> /etc/apache2/apache2.conf

# Reinicia el servicio de Apache
RUN service apache2 restart

# Expone el puerto 80 para el servidor web
EXPOSE 80

# Inicia el servidor Apache
CMD ["apache2-foreground"]

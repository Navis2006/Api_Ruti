FROM php:8.2-apache
RUN docker-php-ext-install mysqli pdo_mysql
RUN apt-get update && apt-get install -y git unzip zip


RUN mkdir -p /var/www/html/config
# --- ¡AQUÍ ESTÁ LA MAGIA! ---
# Este comando se ejecuta DENTRO de Render durante el despliegue.
# Crea el archivo config.php en el servidor web
# y lo llena con las Variables de Entorno de Render.

RUN echo "<?php \n \
    \$host = getenv('DB_HOST'); \n \
    \$db   = getenv('DB_NAME'); \n \
    \$user = getenv('DB_USER'); \n \
    \$pass = getenv('DB_PASS'); \n \
    \$charset = getenv('DB_CHARSET'); \n \
?>" > /var/www/html/config.php

# -----------------------------------

# Ahora, copia el RESTO de tu código (que NO incluye config.php)
COPY . /var/www/html/
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN cd /var/www/html && rm -f composer.lock && composer install --no-dev --optimize-autoloader

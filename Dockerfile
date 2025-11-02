# 1. Usar una imagen oficial que ya tiene PHP y el servidor Apache
FROM php:8.2-apache

# 2. (Opcional) Instalar las extensiones de PHP que necesitas para la base de datos
# (Si usas MySQL/MariaDB, necesitas 'mysqli' y 'pdo_mysql')
RUN docker-php-ext-install mysqli pdo_mysql

# 3. Copiar todo tu código (los archivos .php, .html, etc.)
# al directorio web público del servidor Apache
COPY . /var/www/html/
# Use a imagem oficial do PHP como base
FROM php:8.1-apache

# Instale as extensões necessárias
RUN docker-php-ext-install pdo pdo_mysql

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Rodar o Composer install
RUN composer install --no-dev --optimize-autoloader

# Copie os arquivos do seu projeto para o diretório do Apache

WORKDIR /var/www/html

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

# Exponha a porta 80
EXPOSE 80


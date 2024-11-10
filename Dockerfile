# Use a imagem oficial do PHP como base
FROM php:8.1-apache

# Instale as extensões necessárias
RUN docker-php-ext-install pdo pdo_mysql

# Copie os arquivos do seu projeto para o diretório do Apache

WORKDIR /var/www/html

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

# Exponha a porta 80
EXPOSE 80


# Usar imagem base do PHP
FROM php:8.0-apache

# Copiar arquivos para o diretório de trabalho
COPY . /var/www/html/

# Instalar dependências necessárias
RUN docker-php-ext-install mysqli

# Configurar a porta do Apache
EXPOSE 80

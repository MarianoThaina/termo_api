FROM php:8.2-cli

WORKDIR /var/www

# Dependências do sistema
RUN apt-get update && apt-get install -y \
    unzip zip curl git libzip-dev \
    && docker-php-ext-install zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia projeto
COPY . .

# Instala dependências
RUN composer install --no-interaction --prefer-dist

# Permissões
RUN chmod -R 775 storage bootstrap/cache

# Porta do Render
EXPOSE 10000

# 🔥 IMPORTANTE: usar /public e porta dinâmica
CMD php -S 0.0.0.0:$PORT -t public
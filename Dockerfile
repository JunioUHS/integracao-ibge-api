FROM php:8.2-fpm-alpine

# Instalar dependências do sistema
RUN apk add --no-cache \
    libcurl \
    curl-dev \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    bash \
    && docker-php-ext-install \
    mbstring \
    xml \
    && docker-php-ext-enable mbstring

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos de dependências
COPY composer.json composer.lock ./

# Instalar dependências PHP sem executar scripts
RUN composer install --optimize-autoloader --no-scripts

# Copiar código da aplicação
COPY . .

# Copiar .env.example para .env se não existir
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Executar scripts do composer após copiar os arquivos
RUN composer run-script post-autoload-dump

# Definir permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Executar comandos do Laravel
RUN composer dump-autoload --optimize

# Expor porta
EXPOSE 8000

# Comando padrão
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
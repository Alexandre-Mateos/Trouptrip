FROM php:8.4-fpm as php

# Install git
RUN apt-get update && \
    apt-get install -y --no-install-recommends git && \
    rm -rf /var/lib/apt/lists/* && \
    git config --system user.name "user" && \
    git config --system user.email "user@dev.com"

# Install needed packages
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions zip sockets pdo_pgsql intl gd opcache && \
    rm -f /usr/local/bin/install-php-extensions

# Set PHP limits
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory_limit.ini
RUN echo "post_max_size=64M" > /usr/local/etc/php/conf.d/post_max_size.ini
RUN echo "upload_max_filesize=64M" > /usr/local/etc/php/conf.d/upload_max_filesize.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

#user and group that will be used
ARG USER_ID=1001
ARG GROUP_ID=1001

## --- set the right user and group inside the container ---
COPY user_entry_point.sh /user_entry_point.sh
RUN chmod +x /user_entry_point.sh
RUN /user_entry_point.sh ${USER_ID} ${GROUP_ID}

## défninir le dossier de destination puis copier le code app_backend dans le dossier du container
WORKDIR /var/www/html
COPY ./app_backend /var/www/html

# installer les dépendances pour la PROD
RUN composer install --no-dev --optimize-autoloader --no-interaction

#switch to the good user
USER ${USER_ID}:${GROUP_ID}

FROM node:22 as node-front

#user and group that will be used
ARG USER_ID=1001
ARG GROUP_ID=1001

## --- set the right user and group inside the container ---
COPY user_entry_point.sh /user_entry_point.sh
RUN chmod +x /user_entry_point.sh
RUN /user_entry_point.sh ${USER_ID} ${GROUP_ID}

## défninir le dossier de destination puis copier le code app_frontend dans le dossier du container
WORKDIR /var/www/front
COPY ./app_frontend /var/www/front

# installe ET build (indispensable pour générer le output)
RUN npm install && npm run build

#switch to the good user
USER ${USER_ID}:${GROUP_ID}

# Commande de prod par défaut (est écrasé par la commande du docker-compose en dev)
CMD ["node", ".output/server/index.mjs"]
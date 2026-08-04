FROM php:8.4-fpm as php-base

# Installation des dépendances et extensions
RUN apt-get update && \
    apt-get install -y --no-install-recommends git unzip && \
    rm -rf /var/lib/apt/lists/*

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions zip sockets pdo_pgsql intl gd opcache

# Config PHP
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory_limit.ini && \
    echo "post_max_size=64M" > /usr/local/etc/php/conf.d/post_max_size.ini && \
    echo "upload_max_filesize=64M" > /usr/local/etc/php/conf.d/upload_max_filesize.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

FROM php-base as php-prod

ENV APP_ENV=prod

ARG USER_ID=1001
ARG GROUP_ID=1001

COPY user_entry_point.sh /user_entry_point.sh
RUN chmod +x /user_entry_point.sh && /user_entry_point.sh ${USER_ID} ${GROUP_ID}

COPY ./app_backend /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
RUN composer dump-env prod --empty
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

RUN php bin/console assets:install

RUN chown -R ${USER_ID}:${GROUP_ID} /var/www/html
USER ${USER_ID}:${GROUP_ID}


CMD ["php-fpm", "-F"]

FROM php-base as php-dev

RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

ARG USER_ID=1001
ARG GROUP_ID=1001

COPY user_entry_point.sh /user_entry_point.sh
RUN chmod +x /user_entry_point.sh && /user_entry_point.sh ${USER_ID} ${GROUP_ID}

USER ${USER_ID}:${GROUP_ID}
CMD ["php-fpm", "-F"]

FROM node:22 as node-base

ARG USER_ID=1001
ARG GROUP_ID=1001

COPY user_entry_point.sh /user_entry_point.sh
RUN chmod +x /user_entry_point.sh
RUN /user_entry_point.sh ${USER_ID} ${GROUP_ID}

WORKDIR /var/www/front
USER ${USER_ID}:${GROUP_ID}

FROM node-base as node-front

USER root
COPY ./app_frontend /var/www/front
RUN npm install && npm run build

ARG USER_ID=1001
ARG GROUP_ID=1001
USER ${USER_ID}:${GROUP_ID}

CMD ["node", ".output/server/index.mjs"]

FROM nginx:alpine as nginx-service

WORKDIR /var/www/html

COPY --from=php-prod /var/www/html/public /var/www/html/public

COPY nginx.conf /etc/nginx/conf.d/default.conf
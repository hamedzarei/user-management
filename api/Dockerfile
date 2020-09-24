# build environment
FROM composer as build
WORKDIR /app
COPY . /app
RUN cp .env.deploy .env
RUN composer install

# production environment
FROM webdevops/php-nginx

WORKDIR /var/www/application


COPY --from=build /app /var/www/application

ENV WEB_DOCUMENT_ROOT /var/www/application/public

#RUN mkdir -p /var/log/nginx/

RUN cd /var/www/application && chmod -R go+w storage/logs

RUN apt update && apt install -y php-pgsql

ENV TZ=Asia/Tehran
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone






FROM php:8.0-apache

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
ADD --chmod=0755 https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v7.0.2.zip /tmp/PHPMailer.zip

RUN apt-get update && apt-get install -y libpq-dev unzip \
    && docker-php-ext-install pdo pdo_pgsql
RUN install-php-extensions gd
RUN install-php-extensions mysqli
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN unzip /tmp/PHPMailer.zip -d /tmp/ 
RUN mv /tmp/PHPMailer-7.0.2 /usr/local/lib/php/PHPMailer

COPY src/ /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]
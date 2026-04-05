FROM php:8.5-cli-alpine

WORKDIR /var/www/html/

RUN \
# Install dependencies \
apk add --no-cache \
curl \
git \
nano \
unzip \
wget \
zip \
# Install Composer \
&& curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
&& git config --global --add safe.directory /var/www/html/

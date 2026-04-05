FROM alpine:3.24

ARG GID=1000
ARG UID=1000
ARG USERNAME=developer

ENV COMPOSER_CACHE_DIR=/home/${USERNAME}/.cache/composer

# Composer binary from the official image (no installer script, no curl needed)
COPY --from=composer/composer:2-bin /composer /usr/local/bin/composer

# PHP from the Alpine repository: much smaller than the official php image,
# which also ships php-cgi, phpdbg and the PHP sources.
# Add the php85-* packages for the extensions the project needs.
RUN \
# Install dependencies \
apk add --no-cache \
git \
php85 \
php85-ctype \
php85-dom \
php85-iconv \
php85-mbstring \
php85-openssl \
php85-phar \
php85-tokenizer \
php85-xml \
php85-xmlwriter \
unzip \
&& ln -s /usr/bin/php85 /usr/local/bin/php \
# Add user \
&& addgroup -g ${GID} ${USERNAME} \
&& adduser -D -u ${UID} -G ${USERNAME} ${USERNAME} \
# Create the Composer cache directory owned by the user (it is mounted as a volume in compose.yaml) \
&& install -d -o ${USERNAME} -g ${USERNAME} ${COMPOSER_CACHE_DIR}

COPY custom-php.ini /etc/php85/conf.d/99-custom.ini

WORKDIR /var/www/html/

USER ${USERNAME}

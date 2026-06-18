FROM php:8.5-cli-alpine

ARG UID=1000
ARG GID=1000
ARG USERNAME=developer

WORKDIR /var/www/html/

RUN \
# Install dependencies \
apk add --no-cache \
curl \
git \
nano \
sudo \
unzip \
wget \
zip \
# Install Composer \
&& curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
&& git config --global --add safe.directory /var/www/html/ \
# Add user \
&& addgroup -g ${GID} ${USERNAME} \
&& adduser -D -u ${UID} -G ${USERNAME} ${USERNAME} \
&& echo "${USERNAME} ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers \
&& mkdir -p /home/${USERNAME} \
&& chown -R ${USERNAME}:${USERNAME} /home/${USERNAME}

USER ${USERNAME}

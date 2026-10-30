# The hub's development image: PHP 8.2 and the web server, with no code in
# it. compose.yaml mounts the checkout over /var/www/hub.
FROM dunglas/frankenphp:1-php8.2-bookworm

# The extensions the hub needs that the image does not already have, Xdebug,
# the tools Composer uses, and the MariaDB client muse dumps and loads with
RUN install-php-extensions pdo_mysql gd zip sockets opcache xdebug \
    && apt-get update \
    && apt-get install -y --no-install-recommends git unzip mariadb-client \
    && rm -rf /var/lib/apt/lists/*

# PHP's development settings, and a place for sessions outside the document
# root: the image has neither, and the installer keeps its secrets there
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
    && mkdir -p /var/lib/php/sessions \
    && echo 'session.save_path = "/var/lib/php/sessions"' > "$PHP_INI_DIR/conf.d/hub.ini"

# Mail the hub sends with PHP's mail() goes to the mail catcher
COPY --from=axllent/mailpit:v1.32 /mailpit /usr/local/bin/mailpit
RUN echo 'sendmail_path = "/usr/local/bin/mailpit sendmail -S mail:1025"' >> "$PHP_INI_DIR/conf.d/hub.ini"

# Xdebug stays off unless XDEBUG_MODE says otherwise, and calls back to the
# editor on the machine running Docker
RUN echo 'xdebug.client_host = host.docker.internal' >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini"

# Able to listen on the hub's port as whichever account owns the checkout
RUN setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp

COPY Caddyfile /etc/frankenphp/Caddyfile
COPY hub-dev /usr/local/bin/hub-dev

WORKDIR /var/www/hub
CMD ["hub-dev"]

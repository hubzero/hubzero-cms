#!/usr/bin/env bash
#
# Stand up a demo hub: its own document root, database and virtual host, all
# sharing this checkout's core. Everything the installer would ask for is
# written to an answer file first, so a run needs no input and repeats.
#
#   tools/hubs/newsite.sh welcome 7500 kimera
#   tools/hubs/newsite.sh mesozoic 7600 lucent --reset
#
# --reset drops and recreates the database, so a capture run always starts
# from the same state.

set -euo pipefail

REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"

HUBS_DIR="${HUBS_DIR:-$HOME/hubs}"
CADDYFILE="${CADDYFILE:-$HOME/frankenphp/Caddyfile}"
FRANKENPHP_DIR="${FRANKENPHP_DIR:-$HOME/frankenphp}"
DOMAIN="${HUB_DOMAIN:-example.com}"
DB_PASSWORD="${HUB_DB_PASSWORD:-ClaudeDev2026}"
# Reach the server over TCP: the socket PHP is built against is not always
# the one the server listens on, and a hub is served by more than one PHP.
DB_HOST="${HUB_DB_HOST:-127.0.0.1}"
DB_PORT="${HUB_DB_PORT:-3306}"
ADMIN_PASSWORD="${HUB_ADMIN_PASSWORD:-ClaudeDev2026}"
SAMPLE="${HUB_SAMPLE:-starter}"

die() { echo "error: $*" >&2; exit 1; }
say() { echo -e "\033[36m==>\033[0m $*"; }

usage() {
    cat >&2 <<'USAGE'
usage: newsite.sh <name> <port> [template] [--reset] [--no-caddy]

  name        short name of the hub; also the database name (hub_<name>)
              and the host name (<name>.$HUB_DOMAIN)
  port        https port to serve it on
  template    site template to make the default (default: kimera)

  --reset     drop and recreate the database and the app directory first
  --no-caddy  do not touch the Caddyfile

environment:
  HUBS_DIR            where document roots live (default: $HOME/hubs)
  CADDYFILE           the Caddyfile to add a block to
  HUB_DOMAIN          host name suffix (default: example.com)
  HUB_SAMPLE          none, or the name of a data set to load (default: starter)
  HUB_DB_PASSWORD     database password for the hub's own user
  HUB_DB_HOST         database host (default: 127.0.0.1)
  HUB_DB_PORT         database port (default: 3306)
  HUB_ADMIN_PASSWORD  password for the hub's admin account
USAGE
    exit 2
}

NAME=""
PORT=""
TEMPLATE="kimera"
RESET=0
WRITE_CADDY=1
positional=0

for arg in "$@"; do
    case "$arg" in
        --reset)    RESET=1 ;;
        --no-caddy) WRITE_CADDY=0 ;;
        -h|--help)  usage ;;
        -*)         die "unknown option: $arg" ;;
        *)
            positional=$((positional + 1))
            case $positional in
                1) NAME="$arg" ;;
                2) PORT="$arg" ;;
                3) TEMPLATE="$arg" ;;
                *) die "unexpected argument: $arg" ;;
            esac
            ;;
    esac
done

[ -n "$NAME" ] && [ -n "$PORT" ] || usage
[[ "$NAME" =~ ^[a-z][a-z0-9_-]*$ ]] || die "name must be lowercase letters, digits, dash or underscore"
[[ "$PORT" =~ ^[0-9]+$ ]] || die "port must be a number"
[ -d "$REPO/core/templates/$TEMPLATE" ] || [ -d "$HUBS_DIR/$NAME/app/templates/$TEMPLATE" ] \
    || die "no such template: $TEMPLATE"

SITE_HOST="$NAME.$DOMAIN"
DOCROOT="$HUBS_DIR/$NAME"
DATABASE="hub_$NAME"
DBUSER="hub_$NAME"
URL="https://$SITE_HOST:$PORT"
ANSWERS="$DOCROOT/install.json"

say "Building $NAME at $DOCROOT, served on $URL"

# ---------------------------------------------------------------- database --
if ! command -v mysql >/dev/null; then
    die "mysql client not found"
fi

MYSQL_ADMIN=(sudo -n mysql)
if ! "${MYSQL_ADMIN[@]}" -e "SELECT 1" >/dev/null 2>&1; then
    die "cannot reach mysql as an administrator (tried: sudo mysql)"
fi

if [ "$RESET" = "1" ]; then
    say "Dropping $DATABASE and ${DATABASE}_metrics"
    "${MYSQL_ADMIN[@]}" -e "DROP DATABASE IF EXISTS \`$DATABASE\`;"
    "${MYSQL_ADMIN[@]}" -e "DROP DATABASE IF EXISTS \`${DATABASE}_metrics\`;"
    rm -rf "$DOCROOT/app"
fi

# A hub keeps its usage statistics in a second database named after the first,
# which the migrations create tables in.
say "Creating $DATABASE, ${DATABASE}_metrics and their user"
"${MYSQL_ADMIN[@]}" <<SQL
CREATE DATABASE IF NOT EXISTS \`$DATABASE\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS \`${DATABASE}_metrics\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DBUSER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';
ALTER USER '$DBUSER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON \`$DATABASE\`.* TO '$DBUSER'@'localhost';
GRANT ALL PRIVILEGES ON \`${DATABASE}_metrics\`.* TO '$DBUSER'@'localhost';
FLUSH PRIVILEGES;
SQL

# ---------------------------------------------------------------- docroot ---
say "Laying out the document root"
mkdir -p "$DOCROOT"
cp "$REPO/index.php" "$DOCROOT/index.php"

for dist in favicon.ico robots.txt; do
    if [ -f "$REPO/$dist.dist" ] && [ ! -f "$DOCROOT/$dist" ]; then
        cp "$REPO/$dist.dist" "$DOCROOT/$dist"
    fi
done

ln -sfn "$REPO/core" "$DOCROOT/core"

# A muse of its own. The console works out its paths from where core sits,
# which for a shared core would name the checkout rather than this hub, so
# the wrapper says outright which root and app directory it is running for.
cat > "$DOCROOT/muse" <<MUSEEOF
#!/usr/bin/env php
<?php

/**
 * muse for the $NAME hub, running against the core in $REPO
 */

define('PATH_ROOT', __DIR__);
define('PATH_CORE', '$REPO/core');
define('PATH_APP', __DIR__ . '/app');

require PATH_CORE . '/bin/muse';
MUSEEOF
chmod +x "$DOCROOT/muse"

say "Creating the app directory"
"$DOCROOT/muse" install appdir --no-ansi

# ----------------------------------------------------------------- answers --
say "Writing $ANSWERS"
cat > "$ANSWERS" <<JSONEOF
{
    "site": {
        "sitename": "$NAME",
        "mailfrom": "admin@$SITE_HOST",
        "live_site": "$URL",
        "offset": "America/Indiana/Indianapolis",
        "MetaDesc": "A HUBzero demonstration hub.",
        "site_template": "$TEMPLATE",
        "administrator_template": "kimera",
        "application_env": "development",
        "debug": "1"
    },
    "database": {
        "host": "$DB_HOST",
        "port": $DB_PORT,
        "database": "$DATABASE",
        "username": "$DBUSER",
        "password": "$DB_PASSWORD",
        "prefix": "jos_"
    },
    "admin": {
        "name": "Site Administrator",
        "username": "admin",
        "email": "admin@$SITE_HOST",
        "password": "$ADMIN_PASSWORD"
    },
    "sample": "$SAMPLE"
}
JSONEOF
chmod 600 "$ANSWERS"

# ----------------------------------------------------------------- install --
say "Installing"
"$DOCROOT/muse" install --no-ansi --config="$ANSWERS"

# ---------------------------------------------------------------- template --
# The default template is a row in the database, and the base data names one
# already, so say plainly which one this hub uses rather than leaving the
# config file to be a fallback nobody reaches.
say "Making $TEMPLATE the site template"
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DBUSER" -p"$DB_PASSWORD" "$DATABASE" <<SQL
UPDATE \`jos_template_styles\` SET \`home\` = '0' WHERE \`client_id\` = 0;
INSERT INTO \`jos_template_styles\` (\`template\`, \`client_id\`, \`home\`, \`title\`, \`params\`)
SELECT '$TEMPLATE', 0, '1', '$TEMPLATE', '{}' FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM (SELECT * FROM \`jos_template_styles\`) AS s
    WHERE s.\`template\` = '$TEMPLATE' AND s.\`client_id\` = 0
);
UPDATE \`jos_template_styles\` SET \`home\` = '1'
WHERE \`template\` = '$TEMPLATE' AND \`client_id\` = 0;
SQL

# ------------------------------------------------------------------- caddy --
if [ "$WRITE_CADDY" = "1" ] && [ -f "$CADDYFILE" ]; then
    if grep -q "^# hub: $NAME\$" "$CADDYFILE"; then
        say "Caddyfile already has a block for $NAME"
    else
        say "Adding a Caddy block for $SITE_HOST:$PORT"
        cat >> "$CADDYFILE" <<CADDYEOF

# hub: $NAME
https://$SITE_HOST:$PORT {
	tls internal

	root * $DOCROOT

	php_server {
		env PATH_CORE $REPO/core
	}

	file_server

	log {
		output file $FRANKENPHP_DIR/access-$NAME.log
		format console
	}
}
CADDYEOF
    fi

    if curl -s --max-time 3 http://localhost:2019/config/ -o /dev/null; then
        say "Reloading the server"
        (cd "$FRANKENPHP_DIR" && ./frankenphp reload --config "$CADDYFILE" --adapter caddyfile)
    else
        say "Server is not running; start it to pick up the new block"
    fi
fi

# ------------------------------------------------------------------- done ---
cat <<DONEEOF

  $NAME is ready.

    URL       $URL
    Admin     $URL/administrator
    Username  admin
    Password  $ADMIN_PASSWORD
    Database  $DATABASE
    Docroot   $DOCROOT
    Template  $TEMPLATE

DONEEOF

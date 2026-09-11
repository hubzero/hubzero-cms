#!/usr/bin/env bash
#
# Build a hub from nothing and fail loudly if it does not reach the end.
#
#   tools/hubs/check-install.sh                 the empty hub
#   tools/hubs/check-install.sh starter         with the starter content
#   tools/hubs/check-install.sh --keep          leave the hub standing
#
# A hub that loads no starting content has no record of a migration ever
# having run, so it runs every one of them. That is the path that finds
# defects the hubs built from a data set hide, and nothing else exercises it.
#
# Exits non-zero, saying why, if the install stops, if a migration is left
# pending, or if the hub does not serve its own front page.

set -uo pipefail

REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"

NAME="${CHECK_HUB_NAME:-checkinstall}"
PORT="${CHECK_HUB_PORT:-7999}"
TEMPLATE="${CHECK_HUB_TEMPLATE:-lucent}"
SAMPLE="none"
KEEP=0

for arg in "$@"; do
    case "$arg" in
        --keep) KEEP=1 ;;
        -h|--help) sed -n '2,16p' "${BASH_SOURCE[0]}" | sed 's/^# \{0,1\}//'; exit 0 ;;
        -*) echo "unknown option: $arg" >&2; exit 2 ;;
        *)  SAMPLE="$arg" ;;
    esac
done

HUBS_DIR="${HUBS_DIR:-$HOME/hubs}"
DOCROOT="$HUBS_DIR/$NAME"
LOG="$(mktemp)"

say()  { printf '\033[36m==>\033[0m %s\n' "$*"; }
bad()  { printf '\033[31mFAILED\033[0m %s\n' "$*"; }
good() { printf '\033[32mok\033[0m     %s\n' "$*"; }

cleanup() {
    if [ "$KEEP" = "0" ]; then
        sudo -n mysql -e "DROP DATABASE IF EXISTS \`hub_$NAME\`; DROP DATABASE IF EXISTS \`hub_${NAME}_metrics\`;" 2>/dev/null
        rm -rf "$DOCROOT"
    fi
    rm -f "$LOG"
}
trap cleanup EXIT

say "Building $NAME with the $SAMPLE data set"

HUB_SAMPLE="$SAMPLE" "$REPO/tools/hubs/newsite.sh" "$NAME" "$PORT" "$TEMPLATE" --reset --no-caddy > "$LOG" 2>&1
code=$?

clean="$(sed -E 's/\x1b\[[0-9;]*m//g' "$LOG" | grep -vE 'Progress:')"
ran=$(printf '%s\n' "$clean" | grep -c 'Completed up')

status=0

if [ "$code" != "0" ]; then
    bad "the install stopped (exit $code) after $ran migrations"
    printf '%s\n' "$clean" | grep -m1 -A6 -E 'PHP Fatal error|Error: running|Migration error' | sed 's/^/       /'
    status=1
else
    good "the install finished, $ran migrations"
fi

if printf '%s\n' "$clean" | grep -qE 'PHP Fatal error|Error: running|Migration error'; then
    bad "something went wrong along the way"
    printf '%s\n' "$clean" | grep -m1 -A6 -E 'PHP Fatal error|Error: running|Migration error' | sed 's/^/       /'
    status=1
fi

if [ -x "$DOCROOT/muse" ]; then
    pending=$("$DOCROOT/muse" migration --no-ansi 2>/dev/null | grep -c 'Would run')

    if [ "$pending" != "0" ]; then
        bad "$pending migrations were left pending"
        "$DOCROOT/muse" migration --no-ansi 2>/dev/null | grep -m3 'Would run' | sed 's/^/       /'
        status=1
    else
        good "no migrations left pending"
    fi

    # The front page, served the way a browser would ask for it
    server_port=$((PORT + 1))
    PATH_CORE="$REPO/core" php -S "127.0.0.1:$server_port" -t "$DOCROOT" "$DOCROOT/index.php" \
        > /dev/null 2>&1 &
    server=$!

    for _ in 1 2 3 4 5 6 7 8 9 10; do
        curl -s -o /dev/null "http://127.0.0.1:$server_port/" && break
        sleep 1
    done

    body="$(curl -s --max-time 30 "http://127.0.0.1:$server_port/" 2>/dev/null)"
    code="$(curl -s -o /dev/null --max-time 30 -w '%{http_code}' "http://127.0.0.1:$server_port/" 2>/dev/null)"

    kill "$server" 2>/dev/null
    wait "$server" 2>/dev/null

    error="$(printf '%s' "$body" | grep -oP '<p class="error">.*?</p>' | head -1 | sed 's/<[^>]*>//g')"

    if [ "$code" != "200" ] || [ -n "$error" ]; then
        bad "the front page answered $code${error:+: $error}"
        status=1
    elif ! printf '%s' "$body" | grep -qi '<html'; then
        bad "the front page answered $code but sent no page"
        status=1
    else
        good "the front page renders ($code, $(printf '%s' "$body" | wc -c) bytes)"
    fi
fi

[ "$KEEP" = "1" ] && say "Left standing at $DOCROOT"

exit $status

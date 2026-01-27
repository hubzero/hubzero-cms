#!/bin/bash
# PHP linting and code style checker for CI
# Usage: php_tests.sh [file1.php file2.php ...]

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CORE_DIR="$(dirname "$SCRIPT_DIR")"
VENDOR_BIN="$CORE_DIR/vendor/bin"

update_status () { if [ $1 -ne 0 ]; then status=$1; fi }
status=0

# Filter to only .php files
php_args=$(echo "$@" | tr ' ' '\n' | grep '\.php$' | tr '\n' ' ')
if [ -z "$php_args" ]; then exit 0; fi

# Run code style checks (PSR-12)
"$VENDOR_BIN/phpcs" -np --standard=PSR12 --ignore=*/tests/*,*/bin/* $php_args
update_status $?

# Run PHP syntax linting
"$VENDOR_BIN/parallel-lint" --no-progress $php_args
update_status $?

exit $status

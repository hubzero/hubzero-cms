#!/usr/bin/env bash
#
# Compile every template's LESS with the compiler this repository ships.
#
#   tools/templates/build-css.sh              every template
#   tools/templates/build-css.sh lucent       one of them
#   tools/templates/build-css.sh --check      compile, but only report drift
#   tools/templates/build-css.sh --compress   minify everything it writes
#
# Each entry point in a template's less/ directory that is not a partial (the
# ones whose names begin with an underscore) becomes a stylesheet. Where it
# lands follows what the template already does: lucent keeps main.css beside
# its source, the others write into css/.

set -euo pipefail

REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
LESSC="$REPO/core/bin/lessc"
TEMPLATES="$REPO/core/templates"

CHECK=0
COMPRESS=0
WANTED=()

for arg in "$@"; do
    case "$arg" in
        --check) CHECK=1 ;;
        --compress) COMPRESS=1 ;;
        -h|--help)
            sed -n '2,14p' "${BASH_SOURCE[0]}" | sed 's/^# \{0,1\}//'
            exit 0
            ;;
        -*) echo "unknown option: $arg" >&2; exit 2 ;;
        *)  WANTED+=("$arg") ;;
    esac
done

[ -x "$LESSC" ] || { echo "no compiler at $LESSC" >&2; exit 1; }

# Whether the stylesheet already there is minified
#
# Templates disagree about that too, and a rebuild that changes the shape of a
# file it did not need to change is a diff nobody can read. Match what is
# there: a compiled file averaging more than this many bytes to the line was
# compressed when it was made.
minified() {
    local file=$1 bytes lines

    [ -f "$file" ] || return 1

    bytes=$(wc -c < "$file")
    lines=$(wc -l < "$file")

    [ "$lines" -gt 0 ] || return 0
    [ $((bytes / lines)) -gt 200 ]
}

# Where a template's compiled stylesheet goes
#
# Wherever the template already keeps it. Templates disagree: most write into
# css/, lucent keeps main.css beside its source, and whichever a template chose
# is the path its index.php asks for.
destination() {
    local template=$1 entry=$2 name beside incss
    name="$(basename "$entry" .less)"
    beside="$(dirname "$entry")/$name.css"
    incss="$TEMPLATES/$template/css/$name.css"

    if [ -f "$beside" ]; then
        echo "$beside"
    elif [ -f "$incss" ] || [ -d "$TEMPLATES/$template/css" ]; then
        echo "$incss"
    else
        echo "$beside"
    fi
}

status=0
built=0
drifted=0

for dir in "$TEMPLATES"/*/; do
    template="$(basename "$dir")"

    if [ ${#WANTED[@]} -gt 0 ] && [[ ! " ${WANTED[*]} " =~ " $template " ]]; then
        continue
    fi

    [ -d "$dir/less" ] || continue

    for entry in "$dir"less/*.less; do
        [ -f "$entry" ] || continue
        case "$(basename "$entry")" in _*) continue ;; esac

        out="$(destination "$template" "$entry")"
        tmp="$(mktemp)"

        args=()
        if [ "$COMPRESS" = "1" ] || minified "$out"; then
            args+=(--compress)
        fi

        if ! "$LESSC" "${args[@]}" "$entry" "$tmp" >/dev/null 2>"$tmp.err"; then
            echo "FAILED  $template/$(basename "$entry")"
            sed 's/^/        /' "$tmp.err" | head -3
            rm -f "$tmp" "$tmp.err"
            status=1
            continue
        fi

        rm -f "$tmp.err"

        if [ "$CHECK" = "1" ]; then
            if [ ! -f "$out" ] || ! cmp -s "$tmp" "$out"; then
                echo "DRIFT   ${out#$REPO/}"
                drifted=$((drifted + 1))
                status=1
            fi
            rm -f "$tmp"
        else
            mv "$tmp" "$out"
            chmod 644 "$out"
            echo "built   ${out#$REPO/}"
            built=$((built + 1))
        fi
    done
done

if [ "$CHECK" = "1" ]; then
    [ "$drifted" = "0" ] && echo "Every stylesheet matches its source."
else
    echo "$built stylesheets built."
fi

exit $status

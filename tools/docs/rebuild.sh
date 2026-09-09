#!/bin/sh
# Rebuild the documentation site, in the order the pieces depend on each other.
#
# The generated references are read by the builder, so they have to be
# regenerated first: run the builder before them and the committed site is
# stale, which the Pages workflow rejects. Editing anything under core/ can
# move a line number the API reference links to, so this is worth running
# after code changes too, not only after writing a chapter.
#
# Usage: sh tools/docs/rebuild.sh

set -e
cd "$(dirname "$0")/../.."

echo "==> regenerating the references"
for generator in config muse events api; do
    python3 "tools/docs/gen_${generator}_reference.py"
done

echo "==> building the site"
python3 gh-pages/build_site.py

echo "==> checking links"
python3 gh-pages/check_links.py gh-pages/public

echo "==> running the tests"
python3 -m pytest gh-pages/test_build_site.py tools/docs/test_import_help.py -q

echo
echo "Commit docs/, gh-pages/public/ and any regenerated reference together."

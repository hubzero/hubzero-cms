#!/usr/bin/env bash
#
# Run a sample data pack again and check that nothing doubled.
#
# A step is meant to find what it built last time and leave it alone. When it
# does not, the page still renders — there are simply more rows behind it than
# the step reports — so nothing else here catches it.
#
#   tools/hubs/check-idempotent.sh mesozoic
#
set -uo pipefail

HUB="${1:-mesozoic}"
PACK="${2:-Mesozoic}"
DIR="${HUBS_DIR:-$HOME/hubs}/$HUB"
DB="hub_$HUB"
USER="${HUB_DB_USER:-hub_$HUB}"
PASS="${HUB_DB_PASSWORD:-ClaudeDev2026}"
HOST="${HUB_DB_HOST:-127.0.0.1}"
SAMPLEDATA="${HUBZERO_SAMPLEDATA:-$HOME/hubzero-mesozoic}"

say()  { echo -e "\033[36m==>\033[0m $*"; }
ok()   { echo -e "\033[32mok\033[0m     $*"; }
bad()  { echo -e "\033[31mFAIL\033[0m   $*"; }

counts() {
    mysql -h "$HOST" -u "$USER" -p"$PASS" "$DB" -sN -e "
        SELECT table_name, table_rows FROM information_schema.tables
        WHERE table_schema = '$DB' ORDER BY table_name;" 2>/dev/null
}

# information_schema row counts are estimates for InnoDB, so count for real on
# the tables a pack actually writes to.
TABLES="users xprofiles xgroups xgroups_members wiki_pages wiki_versions
        resources author_assoc answers_questions answers_responses
        blog_entries blog_comments kb_articles forum_categories forum_posts
        events support_tickets support_comments collections collections_items
        collections_posts courses courses_units courses_assets citations
        citations_authors citations_assoc projects project_owners project_todo
        wishlist_item wishlist_vote vote_log tags tags_object modules"

snapshot() {
    for t in $TABLES; do
        n=$(mysql -h "$HOST" -u "$USER" -p"$PASS" "$DB" -sN \
            -e "SELECT COUNT(*) FROM jos_$t;" 2>/dev/null)
        [ -n "$n" ] && echo "$t $n"
    done
}

say "Counting what is there now"
before="$(snapshot)"

say "Running the $PACK pack again"
( cd "$DIR" && HUBZERO_SAMPLEDATA="$SAMPLEDATA" ./muse sampledata run --pack="$PACK" --again ) \
    > /dev/null 2>&1 || { bad "the pack did not finish"; exit 1; }

after="$(snapshot)"

fail=0
while read -r t n; do
    m=$(echo "$after" | awk -v k="$t" '$1 == k { print $2 }')
    if [ "$n" != "$m" ]; then
        bad "jos_$t went from $n to $m"
        fail=1
    fi
done <<< "$before"

echo
if [ "$fail" -eq 0 ]; then
    say "Running the pack again changed nothing."
else
    echo -e "\033[31mA step is adding rows instead of finding its own.\033[0m"
fi

exit "$fail"

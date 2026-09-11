#!/usr/bin/env bash
#
# Ask a hub's listing pages whether the sample content is actually on them.
#
# A sample data pack writes rows. Whether those rows reach a page depends on
# column conventions the pack has to get right by hand — a publish window, an
# access level, a scope — and getting one wrong produces a page that renders
# perfectly and lists nothing. This turns that into a failure.
#
#   tools/hubs/check-content.sh mesozoic 7600
#
set -uo pipefail

HUB="${1:-mesozoic}"
PORT="${2:-7600}"
DOMAIN="${HUB_DOMAIN:-example.com}"
BASE="https://${HUB}.${DOMAIN}:${PORT}"

fail=0

say()  { echo -e "\033[36m==>\033[0m $*"; }
ok()   { echo -e "\033[32mok\033[0m     $*"; }
bad()  { echo -e "\033[31mFAIL\033[0m   $*"; fail=1; }

# expect <name> <path> <pattern> <minimum>
#
# Counts the distinct matches of an extended regular expression in the page and
# checks there are at least as many as the pack should have put there.
expect() {
    local name="$1" path="$2" pattern="$3" want="$4"
    local body code n

    body="$(mktemp)"
    code="$(curl -sk -o "$body" -w '%{http_code}' --max-time 30 "${BASE}${path}")"

    if [ "$code" != "200" ]; then
        bad "$name: ${path} answered ${code}"
        rm -f "$body"
        return
    fi

    n="$(grep -oE "$pattern" "$body" | sort -u | wc -l)"
    rm -f "$body"

    if [ "$n" -lt "$want" ]; then
        bad "$name: ${path} shows ${n}, expected at least ${want}"
    else
        ok "$name: ${n} on ${path}"
    fi
}

# downloads <name> <page> <type> <minimum bytes> [extension]
#
# Finds the download link on a page, follows it, and looks at what comes back.
# The link carries an id that changes between builds, so it is read off the
# page rather than written down here; and only the bytes tell a download that
# works from one that answers 200 with an error page in it.
downloads() {
    local name="$1" page="$2" want="$3" least="$4" ext="${5:-}"
    local link code type size

    link="$(curl -sk --max-time 30 "${BASE}${page}" \
        | grep -oE "/resources/[0-9]+/download/[^\"]*${ext}" | head -1)"

    if [ -z "$link" ]; then
        bad "$name: ${page} offers nothing to download"
        return
    fi

    IFS='|' read -r code type size <<< "$(curl -sk -o /dev/null \
        -w '%{http_code}|%{content_type}|%{size_download}' --max-time 30 "${BASE}${link}")"

    if [ "$code" != "200" ]; then
        bad "$name: ${link} answered ${code}"
    elif [ "${type%%;*}" != "$want" ]; then
        bad "$name: ${link} came back as ${type%%;*}, wanted ${want}"
    elif [ "$size" -lt "$least" ]; then
        bad "$name: ${link} is ${size} bytes, wanted at least ${least}"
    else
        ok "$name: ${size} bytes of ${want}"
    fi
}

# serves <name> <path> <type> <minimum bytes>
#
# Follows a path and looks at what comes back rather than at the page that
# offered it.
serves() {
    local name="$1" path="$2" want="$3" least="$4"
    local code type size

    IFS='|' read -r code type size <<< "$(curl -skL -o /dev/null \
        -w '%{http_code}|%{content_type}|%{size_download}' --max-time 60 "${BASE}${path}")"

    if [ "$code" != "200" ]; then
        bad "$name: ${path} answered ${code}"
    elif [ "${type%%;*}" != "$want" ]; then
        bad "$name: ${path} came back as ${type%%;*}, wanted ${want}"
    elif [ "$size" -lt "$least" ]; then
        bad "$name: ${path} is ${size} bytes, wanted at least ${least}"
    else
        ok "$name: ${size} bytes of ${want}"
    fi
}

say "Checking the content on ${BASE}"

expect "front page"  "/"                    'home-[0-9]'                        2
# The browse listing links by id and the type listings link by alias
expect "resources"   "/resources/browse"    'href="/resources/([0-9]+|[a-z0-9-]{6,})"' 20
expect "wiki"        "/wiki/Special:AllPages" 'href="/wiki/[A-Za-z]{4,}"'       30
expect "groups"      "/groups/browse"       'href="/groups/[a-z-]{5,}"'          8
expect "questions"   "/answers"             'href="/answers/question/[0-9]+'    20
expect "blog"        "/blog"                'href="/blog/[0-9]{4}/[0-9]{2}/[a-z0-9-]+"' 10
expect "knowledge base" "/kb"               'href="/kb/[a-z]+/[a-z0-9-]+"'      20
expect "forum"       "/forum"               'href="/forum/[a-z]+/[a-z-]{5,}"'    5
# The calendar only shows the month it is asked for, so this checks the year
expect "events"      "/events/2026"         'href="/events/details/[0-9]+"'      6
# Counted from the posts stream, which names the collection each item is in
expect "collections" "/collections/posts"   'href="/members/[0-9]+/collections/[a-z-]+"' 5
expect "courses"     "/courses/browse"      'href="/courses/[a-z-]{6,}"'         3
expect "citations"   "/citations/browse"    'citations/download/[0-9]+'         12
expect "projects"    "/projects/browse"     'href="/projects/[a-z0-9-]{6,}"'     4
expect "wish list"   "/wishlist"            'wishlist/general/1/wish/[0-9]+'    10
expect "publications" "/publications"       'href="/publications/[0-9]+"'        8
expect "polls"       "/poll"                'name="id" value="[0-9]+"'           3
expect "jobs"        "/jobs"                'href="/jobs/job/[0-9]+"'            5
expect "newsletters" "/newsletter"          'href="/newsletter/[0-9]{4}-[a-z]+"' 4

# Pictures, which are files rather than rows: a hub whose members and groups
# all fall back to the same grey placeholder looks unbuilt, and the way that
# happens is a picture written to the wrong directory, where nothing errors
expect "group logos" "/groups/browse"     'src="/files/[A-Za-z0-9+/=]{40,}"'   8
# No listing of people is public, so this asks one member's own page. The id is
# the first account the pack makes on an otherwise empty hub. The profile finds
# the picture on disk rather than through the column, so this fails when the
# file is written somewhere the hub does not look
expect "member picture" "/members/1001"   'src="/files/[A-Za-z0-9+/=]{40,}"'   1

# A wiki nobody has edited twice has an empty history screen and a diff screen
# with nothing on it, which are two of the more distinctive pages it has
expect "wiki history"  "/wiki/CalderBasin?task=history"      'id="oldid-[0-9]+"'  3
expect "wiki comments" "/wiki/FieldNumbering?task=comments" 'id="c[0-9]+"'       3
# The diff of the first revision against the last, which is only worth looking
# at if the revisions differ, and which names who made each of them
expect "wiki diff"     "/wiki/CalderBasin?task=compare&oldid=1&diff=4" 'by [A-Z][a-z]+ [A-Z][a-z]+' 2

# A group whose every tab is empty is a worse advertisement for groups than no
# group at all, and a page written to the wrong place still answers 200
expect "group pages"    "/groups/fossil-ct"          'class="page" href="[^"]+"'          2
expect "group wiki"     "/groups/fossil-ct/wiki"     'href="[^"]*/wiki/[A-Za-z]{6,}"'     2
expect "group forum"    "/groups/fossil-ct/forum"    'forum/discussion/[a-z-]{5,}'        2
expect "group calendar" "/groups/fossil-ct/calendar" 'calendar/details/[0-9]+'            2

# A hub is a repository, so something has to come back when the button is
# pressed. A file written to the wrong directory leaves a page that renders
# perfectly and a download that answers 404
downloads "a data table" "/resources/calder-basin-measured-sections" text/csv         1200
# The figure and the note are behind the supporting documents tab rather than
# on the record itself, which is where the hub puts everything but the primary
downloads "a figure"     "/resources/calder-quarry-map-2025/supportingdocs" image/png   4000  '\.png'
downloads "a note"       "/resources/calder-quarry-map-2025/supportingdocs" text/plain   300  'README.txt'
downloads "a document"   "/resources/preparation-standards"            application/pdf  4000

# A course offering is reached by its alias, and the hub reads a numeric alias
# as a row id, so an offering named for its year is looked for at that id and
# never found. Two live offerings, because an offering that has ended is
# deliberately not shown to a visitor
expect "course offering" "/courses/field-stratigraphy/summer-2026" 'summer-2026/enroll' 1
expect "open offering"   "/courses/fossil-imaging/open"            'open/enroll'        1

# A course with only an instructor in it, a collection of nobody else's finds,
# and an event that cannot be registered for are each a screen that renders
# correctly and says nothing
expect "reposts"      "/collections/posts"     'members/[0-9]+/collections/[a-z-]+' 6
expect "registration" "/events/details/2"      'details/[0-9]+/register'            1

# A published record that cannot be downloaded is the emptiest record a hub
# has, and the bundle is built on demand from what is attached
serves "a publication bundle" "/publications/1/serve/1?render=archive" application/zip 2000

if [ "$fail" -eq 0 ]; then
    echo
    say "Everything the pack builds is on a page."
else
    echo
    echo -e "\033[31mSome of the content the pack builds is not reaching a page.\033[0m"
fi

exit "$fail"

#!/usr/bin/env bash
#
# Ask a hub whether the content only its members can see is actually there.
#
# check-content.sh looks at the hub as a visitor, which is most of it but not
# all of it: a project's files, a course gradebook and a group area closed to
# non-members are invisible to a guest by design. Those are exactly the places
# where a page can render perfectly for the person who built it and be empty
# for everybody else, so they need asking too.
#
#   tools/hubs/check-private.sh mesozoic 7600
#
set -uo pipefail

HUB="${1:-mesozoic}"
PORT="${2:-7600}"
DOMAIN="${HUB_DOMAIN:-example.com}"
BASE="https://${HUB}.${DOMAIN}:${PORT}"
PASS="${HUB_MEMBER_PASSWORD:-MesozoicDemo2026}"

fail=0
jar="$(mktemp)"
body="$(mktemp)"
trap 'rm -f "$jar" "$body"' EXIT

say()  { echo -e "\033[36m==>\033[0m $*"; }
ok()   { echo -e "\033[32mok\033[0m     $*"; }
bad()  { echo -e "\033[31mFAIL\033[0m   $*"; fail=1; }

# signin <username>
#
# Signs in and keeps the session, so everything after it is asked as that
# person. The form carries a one-time token that has to be read off the page.
signin() {
    local who="$1" token

    rm -f "$jar"
    curl -sk -c "$jar" -o "$body" --max-time 30 "${BASE}/login"

    token="$(grep -oE 'name="[a-f0-9]{32}" value="1"' "$body" \
        | head -1 | sed 's/name="//; s/" value="1"//')"

    if [ -z "$token" ]; then
        bad "could not find the login form's token"
        return 1
    fi

    curl -sk -b "$jar" -c "$jar" -o /dev/null -L --max-time 30 \
        -d option=com_users -d authenticator=hubzero -d task=login \
        -d "username=${who}" -d "passwd=${PASS}" -d "${token}=1" \
        "${BASE}/login"

    # To a file rather than down a pipe: grep -q stops at its first match and
    # closes the pipe, curl dies of SIGPIPE, and pipefail then reports the
    # whole pipeline as failed even though the match was found.
    curl -sk -b "$jar" -o "$body" --max-time 30 "${BASE}/members/myaccount"

    if ! grep -q "$who" "$body"; then
        bad "could not sign in as ${who}"
        return 1
    fi

    ok "signed in as ${who}"
}

# expect <name> <path> <pattern> <minimum>
#
# As check-content.sh, but asked while signed in.
expect() {
    local name="$1" path="$2" pattern="$3" want="$4" code n

    code="$(curl -sk -b "$jar" -o "$body" -w '%{http_code}' --max-time 30 "${BASE}${path}")"

    if [ "$code" != "200" ]; then
        bad "$name: ${path} answered ${code}"
        return
    fi

    n="$(grep -oE "$pattern" "$body" | sort -u | wc -l)"

    if [ "$n" -lt "$want" ]; then
        bad "$name: ${path} shows ${n}, expected at least ${want}"
    else
        ok "$name: ${n} on ${path}"
    fi
}

# refused <name> <path>
#
# The other half of a members-only area: that somebody who is not a member is
# told so rather than shown it.
refused() {
    local name="$1" path="$2" code

    code="$(curl -sk -b "$jar" -o "$body" -w '%{http_code}' --max-time 30 "${BASE}${path}")"

    # Not "restricted": the group menu marks every area a non-member cannot
    # reach, so that word is on the page whether or not this area is one of
    # them. Being told to join is what only a refusal says.
    if [ "$code" = "302" ] || grep -qi 'must be a member' "$body"; then
        ok "$name: kept out of ${path}"
    else
        bad "$name: ${path} let a non-member in"
    fi
}

say "Checking what only members see on ${BASE}"

# A project's files live outside the document root and are listed through the
# file store adapter rather than read from a table, so nothing about them shows
# up in a row count
if signin sberglund; then
    # The listing names each entry in the checkbox beside it. Matching only
    # the value, because the attributes after it are on the following line and
    # grep reads a line at a time
    expect "project files"  "/projects/calder-2026/files" \
                            'value="(sections|quarry|README\.txt)"'  3
    expect "a course roster" "/courses/field-stratigraphy/summer-2026/progress?action=getgradebookdata" \
                             '"name":"[A-Z][a-z]+ [A-Z][a-z]+"'                                 8
    expect "a gradebook"    "/courses/field-stratigraphy/summer-2026/progress?action=getgradebookdata" \
                             '"score":"[0-9]+\.[0-9]{2}"'                                       6
    expect "a closed forum" "/groups/calder-basin/forum"   'forum/discussion/[a-z-]{5,}'         2
fi

# And that the same area is closed to somebody who is not in the group
if signin mokonkwo; then
    refused "a non-member"  "/groups/calder-basin/forum"
fi

echo
if [ "$fail" -eq 0 ]; then
    say "Everything a member should see is there, and what they should not is not."
else
    echo -e "\033[31mSomething a member should see is missing.\033[0m"
fi

exit "$fail"

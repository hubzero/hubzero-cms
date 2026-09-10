<!--
status: rewritten
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
-->
# Redirect

The Redirect component does two unrelated jobs. It keeps the hub's list of
managed redirects — the addresses that used to work and where they go now —
and it collects the 404s the site serves so you can see which broken
addresses people are actually asking for. Separately, it provides the
interstitial page that warns a member before an external link takes them off
the hub.

The redirect list is the part a manager works in. [URLs](../08-content/urls.md)
covers it in the context of how the hub addresses a page; this chapter is the
component's own reference and goes further into the screens, the collection
mechanism, and the options.

## Where it lives

**Site → Maintenance → Routes**. It is deliberately not in the
**Components** menu — the admin menu excludes it.

> **Note:** The **Maintenance** submenu that holds **Routes** is only drawn
> for an administrator who has `core.admin` on `com_checkin` or `core.manage`
> on `com_cache`. Someone granted `core.manage` on `com_redirect` alone can
> reach the screen at `index.php?option=com_redirect` but will not find a
> menu entry for it.

Opening the component needs `core.manage` on `com_redirect`.

## The two lists

The screen has two sub-navigation entries over the same table:

- **Redirects** — links that have a destination.
- **404s** — links that do not. These are the recorded misses.

**Search** matches the source URL, the destination, the comment, and the
referring page. The status filter offers **All**, **Enabled**,
**Disabled**, **Archived**, and **Trashed**. Every column heading sorts.

The list shows the **Expired URL**, then either the **New URL** (on
**Redirects**) or the **Referring Page** (on **404s**), followed by the
**Created Date**, the status, the **404 Hits** count, and the ID. Source and
destination are shown relative to the hub root, with a `(site)` prefix
standing in for it.

At the foot of the table the component reports whether the plugin it depends
on is running: *The Redirect Plug-in is enabled.* or *The Redirect Plug-in
is disabled. Enable it in the Plug-in Manager.*

## How 404s are collected

They are collected by the **System - Redirect** plugin, not by the component.
The plugin installs itself as the site's exception handler; when the hub
raises a 404 it:

1. Looks for an enabled link whose **Source URL** matches the full request
   URL. Failing that, it tries the server-relative path, with and without a
   leading slash.
2. If it finds one, it sends the redirect and stops.
3. Otherwise it records the address — creating a link with an empty
   destination, the referring page, and one hit, or incrementing the hit
   count on the one that already exists.

So the **404s** list fills itself, and nothing at all is recorded while the
plugin is disabled. Two kinds of request are skipped on purpose: URLs
containing `mosConfig_` or `=http://`, which are probe traffic.

Enable the plugin under **Extensions → Plugins**, filtering on `system`. See
[Plugins](../10-extensions/03-plugins.md).

## Editing a link

**New**, or select one and **Edit**.

| Field | Meaning |
|---|---|
| **Source URL** | The address to catch. Required, and must be unique across the whole list. A value that does not start with `http` is stored as a root-relative path. |
| **Destination URL** | Where to send the visitor. Also normalised to a root-relative path unless it starts with `http`. |
| **Response Code** | **404 Not Found**, **301 Moved Permanently**, or **302 Found**. |
| **Comment** | A free-text note. Worth using: it is the only place to record why a redirect exists. |
| **Status** | **Enabled**, **Disabled**, **Archived**, or **Trashed**. Only an enabled link redirects. |

The right-hand panel shows the ID, created and last-updated dates, and the
hit count; none of them are editable.

> **Note:** The response code is corrected on save. Give a link a destination
> and pick **404 Not Found**, and it is stored as **301 Moved Permanently** —
> a redirect must be a 301 or a 302. The edit form preselects **302 Found**
> for a new link that has a destination, so a link created through the form
> is a 302 unless you say otherwise.

## Working through the 404 list

Below the list, once it has rows, is **Update selected links to the following
new URL**. Tick several recorded 404s, type one **Destination URL** and
optionally a **Comment**, and press **Update Links**: all of them get that
destination, the same comment, and are enabled in one go. This is the fast
way to retire a whole directory of moved pages.

Links updated this way are stored as **301 Moved Permanently**, because the
batch sets no response code and the correction above supplies one.

The rest of the toolbar is **Enable**, **Disable**, **Archive**, and
**Trash**. Permanent deletion appears as **Empty trash** only when the status
filter is set to **Trashed**. **Options** appears for `core.admin`.

Archiving is the way to retire a recorded 404 you have decided not to
redirect, without losing the record that it was asked for.

## The external-link interstitial

The second half of the component is unrelated to the redirect list. It
rewrites links that point off the hub so that they go through
`/redirect/{base64 of the target}` instead, and that address optionally shows
a countdown page — *Redirecting Soon…* — before forwarding.

Four places pass member-supplied text through the rewriter: the body of an
[Answers](01-answers.md) question and of its comments, the **About** text on
a project's public page and in the project info panel, and the handoff when a
[tool](25-tools.md) session opens at an external proxy URL.

The rewrite happens whether or not the delay is switched on; what the options
control is what the `/redirect/` address then does. In every case the
rewritten link also gains `rel="noreferrer nofollow noopener"`, so the
external site is not told where the visitor came from.

| Setting | What it does |
|---|---|
| **Delay is enabled** | `DISABLED` — the default — forwards immediately. `ENABLED` shows the countdown page for external destinations. |
| **Delay in seconds** | How long the countdown runs. Default 10. |
| **Whitelisted hosts** | Comma-separated hostnames that are never delayed and never rewritten. |
| **Blacklisted hosts** | Comma-separated hostnames that are not followed at all: the link is rewritten to the hub's front page. |

Links to the hub's own host are left alone, as are anchors and
`javascript:` URLs. Hostnames containing `proxy` are always treated as
external.

The parameters are in the
[generated reference](../../reference/configuration/components/redirect.md).

> **Warning:** The host lists are matched exactly, against the hostname
> only. `example.org` does not cover `www.example.org`; list both.

## See also

- [URLs](../08-content/urls.md) — how the hub decides an address in the first
  place, and why a redirect is a better answer than a menu item.

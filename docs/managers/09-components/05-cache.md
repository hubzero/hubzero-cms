<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
-->
# Cache

The Cache Manager empties the hub's cache. It is two screens — one that lists
what is cached and deletes it, one that deletes only the entries that have
expired — and it has no settings of its own. You open it when a change you
made in the administrator interface is not showing on the site, and almost
never otherwise.

Both screens are under **Site → Maintenance** in the administrator menu:
**Clear Cache** and **Purge Expired Cache**. Each carries the same
sub-navigation across the top — **Checkin**, **Clear Cache**, **Purge Expired
Cache** — so [Global Check-in](07-checkin.md) is one click away from either.

Clearing the cache is the safest button in the administrator interface.
Everything in the cache is a copy of something the hub can work out again, so
the worst outcome is that the next few page loads are slower than usual. It
is not confirmed and not undoable, and neither of those matters. Do not
hesitate over it.

> **Note:** This is not where caching is turned on or off, and it is not the
> place to tune it. What gets cached, for how long, and by which backend is
> set on the **System** tab of
> [Global configuration](../05-configuring/01-hub.md), covered under
> [Settings](#settings) below. These two screens only empty what is already
> there.

Do reach for it when a change you made in the administrator interface does
not appear on the site — a module you moved, a menu item you renamed, an
article you published. Do not reach for it when content is missing, when a
member cannot log in, or when a permission is not taking effect. Those are
not cache problems, and clearing the cache to see whether it helps costs you
the time it takes to convince yourself it did not.

## What the hub caches

Caching is off in a default installation. The installer writes
`app/config/cache.php` with `caching` set to `0`, and while it is off nothing
new is written: the cache service resolves to the `none` storage, which
accepts writes and forgets them.

With caching on, four kinds of thing land in the cache:

| Cached by | Group | What it holds |
|---|---|---|
| `plgSystemCache` | `page` | Whole rendered pages, for guests only, on GET requests. Off unless the **System - Cache** plugin is enabled and its **Use Page Caching** option is on. |
| `Hubzero\Module\Loader` | `com_modules` | Rendered module output, for guests only, per module and menu item. |
| Individual components | `wiki`, `groups`, `tags`, `members`, `resources`, … | Expensive fragments a component decided to keep: rendered wiki revisions, a group's compiled stylesheets, the tag clouds, the member statistics, resource usage charts. |
| The system itself | `_system` | Extension and menu data that several components clear by hand after a save. |

A group is just a name prefix on the cache key, so the list on the screen is
a list of the features currently holding cached data, not a fixed catalogue.

## Where the files are

With the default `file` handler, each cache group is a directory under
`app/cache/<client>/`, and each entry a file inside it. The client directory
is `site` for the front end and `admin` for the administrator interface.

> **Note:** Two files sit in the client directory itself rather than in a
> group: `site.css` and `site.less.cache`, the compiled and combined system
> stylesheet. The Clear Cache screen lists directories only, so those two are
> never listed and the **Delete** button never removes them. When a template
> change refuses to appear, that is usually what is holding it.

## Clear Cache

Open **Site → Maintenance → Clear Cache**.

The table lists one row per cache group for the selected location:

| Column | Meaning |
|---|---|
| **Cache Group** | The group name, which is the directory name under `app/cache/<client>/`. |
| **Number of Files** | How many cache entries the group holds. |
| **Size** | The total size of those entries. |

All three columns sort. The drop-down at the top left is the location filter.
It offers every client the framework defines — Site, Administrator, Files,
Install, Api, Testing and Cli — although only the first two ever hold
anything; picking one of the others creates an empty directory under
`app/cache/` and shows an empty list.

Tick the groups you want gone and press **Delete**. That removes every entry
in the group, expired or not, and the group's directory with it. Nothing is
confirmed and nothing is recoverable, but nothing is lost either: cached data
is a copy of something the hub can compute again.

The toolbar also carries **Options**, for someone with `core.admin`, and
**Help**. There is no button that clears everything; select all the rows
instead.

### Worked example: a template change that will not appear

Say you have edited the site template's stylesheet and the site still serves
the old one, on a hub where caching is on.

1. Open **Site → Maintenance → Clear Cache**, tick every row, and press
   **Delete**. If the change now appears, you are finished.
2. It usually will not. The compiled stylesheet is `site.css` in
   `app/cache/site/`, which sits outside any group, and this screen lists
   groups only — so the button you just pressed could not have removed it.
3. On the server, run `core/bin/muse cache clear`. That empties
   `app/cache/` completely, compiled stylesheets included.
4. Reload the site with a forced refresh, to rule out the browser's own copy.

If it still will not appear after step 3, the hub's cache is not what is
holding it: look at whatever sits in front of the hub — a web server cache,
a proxy, a CDN — none of which these screens can touch.

## Purge Expired Cache

Open **Site → Maintenance → Purge Expired Cache**. The screen is a paragraph
of instructions and one toolbar button, **Purge expired**, which walks the
cache directory for the current location and deletes only the entries whose
lifetime has run out. Entries that are still current survive. On a hub with a
large cache this reads every cache file, which is what the warning on the
screen is about.

> **Note:** Only the `file`, `apc`, `wincache` and `memory` handlers collect
> expired entries. The `memcache`, `memcached` and `xcache` handlers inherit a
> no-op, so with those the button reports *Expired items have been purged* and
> does nothing. Nothing is lost — those servers expire their own entries — but
> the message is not evidence that anything happened.

## Settings

The component has no options. The **Options** button opens the permissions
grid and nothing else, and the grid it opens is the global one: the `rules`
field in
[`config/config.xml`](../../../core/components/com_cache/config/config.xml)
carries `component="com_config"` rather than `com_cache`, so what you set
there is the site-wide rule, not a rule for the Cache Manager. That is
deliberate — the component ships no `access.xml` of its own and is
administered as part of the global configuration — but it means the two
actions the field lists, **Configure** and **Access Administration
Interface**, are the site-wide ones you would also find on the **Permissions** tab of
[Global configuration](../05-configuring/01-hub.md).

Everything that decides *what* gets cached lives there too, on the **System**
tab under **Cache Settings**:

- **Caching** — off, conservative, or progressive. Either of the last two
  turns on module caching; progressive additionally lets the whole rendered
  document be cached, and only for visitors who are not signed in.
- **Cache Handler** — which storage backend to use. `file` unless the hub has
  a memcache server.
- **Cache Time** — the default lifetime in minutes.
- **Memcache Settings** — host, port, persistence and compression, used only
  when the handler is a memcache one.

The Cache Manager reads the handler setting but not the on/off switch, so the
two screens still list and clear whatever is on disk from a period when
caching was on.

## From the command line

`muse cache clear` empties `app/cache/` completely — every client directory,
every group, and the compiled stylesheets the screens leave behind:

```bash
core/bin/muse cache clear
```

It reports each path it removes. Use it after a template or LESS change, and
when a hub's cache has grown large enough that walking it in the browser times
out. The [muse reference](../../reference/muse.md#muse-cache) lists the command's
tasks.

Purging can also be scheduled. The **Cron - Cache Handler** plugin offers
**Trash expired cache data**, which is the Purge Expired button as a cron job;
schedule it from [Cron](12-cron.md) on a hub whose cache needs regular
trimming. Its sibling job, **Remove old system CSS files**, looks for files
named `system-*.css` directly in `app/cache/` — a name the hub no longer
writes — so it finds nothing to delete.

Both screens also have REST equivalents — `GET /api/cache/list`,
`DELETE /api/cache/clean`, `DELETE /api/cache/purge` — documented in the
[cache API reference](../../reference/api/cache.md).

## What it does not clear

- Compiled stylesheets, as above; use `muse cache clear`.
- Anything under `app/cache/` that is not inside a client directory — the
  generated API documentation, a module's own scratch directory. Those are
  siblings of `site/` and `admin/`, and only `muse cache clear` reaches them.
- The language string cache behind the override editor's search panel, which
  is a database table refreshed from
  [Language Manager](20-languages.md).
- The Solr index, which has its own rebuild in
  [Search](31-search/README.md).
- Anything the web server or a CDN is caching in front of the hub.

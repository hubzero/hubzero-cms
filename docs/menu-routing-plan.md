# Menu routing: what is wrong and what to do about it

Written 14 September 2026, at the end of a long session. Nothing here is built.
The parts of it that *were* built that day are marked as such; everything else
is a proposal, and the last section lists what was considered and dropped, so
that a decision does not get made twice.

---

## The problem

A hub ships two site menus. `mainmenu` is the one a visitor sees. `default` is
one nobody can explain, and it is the source of the complaint.

`default` is doing three unrelated jobs under one name.

### 1. It gives every component a route and an Itemid

It holds one flat item per component — `resources`, `wiki`, `forum` — each
linking to `index.php?option=com_x` and sitting at level 1, so its `path` is a
single segment. That path is what the router matches an incoming URL against.

This is load-bearing, and the measurements say so:

```
/resources     200  active-markers=2     in default -> gets an Itemid
/publications  200  active-markers=2     in default -> gets an Itemid
/wiki          200  active-markers=2     in default -> gets an Itemid
/oaipmh        200  active-markers=0     not in it  -> routes by name, no Itemid
/activity      404                       not in it  -> does not route at all
```

Two parse rules in `core/bootstrap/Site/routes.php` decide this. The `menu`
rule (line 307) matches the URL against `#__menu.path` and, on a hit, sets
`Itemid` and calls `setActive()`. The `component` rule (line 569) only runs if
that missed: it takes the first segment as a component name and sets `option`
with no Itemid at all.

No Itemid means no per-page modules, no per-page template style, and no menu
highlighting — `#__modules_menu` keys on `menuid`, so there is nowhere to hang
them.

### 2. It is the alias target for the visible menu

`mainmenu` nests its items for presentation — Resources under Discover — and
`#__menu.path` is built from that nesting, so such an item would own
`/discover/resources`. To keep the URL at `/resources`, `mainmenu`'s twenty
component entries are `alias` items pointing into `default` by id, through
`params.aliasoptions`.

This is why `default` cannot simply be deleted, and why the duplication exists.

### 3. It holds hand-chosen friendly URLs

`/login`, `/logout` and `/register` are in there. These are not derivable from
anything: `/register` is `com_members&view=register&layout=create`. The natural
routes already work — `/users/login` and `/members/register` both return 200
with no menu item — so these three rows exist only to provide a nicer spelling.

### And it has drifted, because it is maintained by hand

```
enabled components:            55
with a site/ directory:        40
admin-only (no site/):         15
site components with no route: 14
```

Of those fourteen, most should not have a route: `com_content` routes through
articles, `com_cron` / `com_system` / `com_mailto` / `com_media` /
`com_redirect` / `com_help` / `com_messages` are infrastructure, and
`com_oaipmh` / `com_oauth` / `com_saml` are machine-facing.

Hitting each of them directly, on a hub with `debug` off, narrows it further:

```
/search       200   routes and renders, no Itemid   <- the genuine gap
/dataviewer   404   has a site/ directory but does not answer at its own name
/oaipmh       200   routes, no Itemid               machine-facing, fine
/oauth        403   machine-facing, fine
/saml         404   machine-facing, fine
/redirect     500   reachable by name and errors    <- its own bug, unrelated
```

So the genuine gap is **`com_search`**, one component. `com_dataviewer` needs
looking at on its own before assuming a menu entry would fix it, and the 500 on
`/redirect` is a separate defect found while checking this.

---

## The proposed solution

Four pieces. They are separable and can land in this order.

### A. Let a menu item declare its own route

The root cause of job 2 is that `path` is derived from nesting. It is written
in exactly two places in `core/components/com_menus/models/item.php` — line 408
on insert (`parent.path . '/' . alias`) and `rebuildPath()` at line 509. The
column comment says as much: *"The computed path of the menu item based on the
alias field."*

Nothing depends on it matching the tree:

- `idx_path` is a plain index, **not unique**
- the unique key is `(client_id, parent_id, alias, language)`, about siblings
- the tree itself is `lft` / `rgt` / `parent_id` / `level`, entirely separate
- nothing in `Hubzero/Menu/` reads a path as a hierarchy; `Type/Site.php`
  selects it as `route` for the router to match

So: a per-item route, a guard in those two functions, and a field in the item
form. Resources then nests under Discover for display *and* answers at
`/resources`. `mainmenu`'s twenty alias items stop having a reason to exist.

**Risk: highest of the four.** Every URL on every hub comes out of that column,
and the migration rewrites paths on live sites.

### B. Generate the component routes

Rename what is left of `default` to say what it is — a Component menu, never
displayed, one item per site component, giving each a route and an Itemid.

Generate it in `AddComponentEntry`
(`core/libraries/Hubzero/Content/Migration/Macros/AddComponentEntry.php`),
which **already does exactly this for the admin menu** at line 125: it takes
`$alias = substr($option, 4)`, sets `path` to the alias, links to
`index.php?option=com_x`, handles the nested-set rebuild, and is idempotent on
`(client_id, parent_id, alias)`. The site version is the same block with
`client_id` 0 and its own menutype.

97 migrations call this macro, so every component installed from here on is
covered. Note that `substr($option, 4)` is the same rule the router's fallback
uses at `routes.php:164`, so the registry and the fallback would agree by
construction rather than by coincidence.

Decisions inside this:

- **Which components qualify.** `com_cron` and `com_system` go through the same
  macro. The honest test is whether the component has a `site/` directory, either
  checked by the macro or passed by the caller the way `$createMenuItem` is now.
- **Existing hubs** need a one-time backfill. On the evidence that is one
  component, `com_search` - not a sweep. See the note on `com_dataviewer` below.
- **`DisableComponent`** should unpublish the site item to match, or a
  switched-off component keeps answering.
- **A `muse` reconcile command**, for drift the macro cannot see: a
  site-specific component dropped in without a migration, where neither the
  install hook nor a backfill knows it exists. This is also the escape hatch for
  the protection below.

#### The items are protected, not hidden

The menu is generated, so hand-editing it is how `default` drifted in the first
place. But it must not be hidden from the admin, and the reason is the reason
the entries exist at all: an Itemid is only worth having because two things hang
off it, and both are set against the item.

- `#__modules_menu.menuid` - assigning a module to that page
- `#__menu.template_style_id` - giving that page its own template style

Hide the item and a hub cannot put a module on Search or give it its own style,
at which point the entry buys nothing over the no-Itemid fallback and there is
no point generating it.

So: identity locked, assignment open.

| locked | open |
|---|---|
| `alias`, `path`, `link` - derived from the component name; editing them breaks the agreement with `routes.php:164` | `template_style_id` |
| `type`, `component_id` | `params`, including `menu_show` |
| deleting the item | `access` |
| deleting or renaming the menutype | |

`published` follows the component, set by `EnableComponent` and
`DisableComponent` rather than toggled by hand. Otherwise a hub can switch off a
route while leaving the component on, and have no way to work out why the page
stopped answering.

**Mechanism.** A `protected` column on `#__menu` rather than keying off the
menutype's name. There is precedent in the codebase: `#__extensions.protected`
exists already, and `Migration20150626141512Core.php` sets it on the core
templates. A column generalises; a hard-coded menutype name in `com_menus` does
not.

**Escape hatch.** A protected item with no override is a trap the first time a
route is wrong or two components collide. Protected means the admin UI refuses
and the `muse` command can do it - which is the same command that reconciles.

### C. Lazy generation, in development only

When the `component` parse rule resolves a component that has no entry, create
one — but only when `debug` is on in the hub's `app/config/app.php`.

This is for building a component, where writing a migration just to see the
thing route is friction. It needs no `site/` heuristic, because reachability is
the proof: if a request got as far as resolving the component, it has a site
entry point.

- Wrap it in `Driver::transaction(callable, $attempts)`, which already exists,
  so the nested-set shift and the insert land together.
- The race between two first-visits is arbitrated by the existing unique key on
  `(client_id, parent_id, alias, language)` — the loser's transaction rolls back
  and re-reads. No locking.
- Set the Itemid and `setActive()` in the same request, so the page you are
  looking at is already the page you will get next time.
- Log what it did. A silent insert during a GET is surprising even in dev.
- The row it generates is the row the migration should declare, which makes it
  a discovery tool as well as a convenience.

Kept behind `debug` not because it would be incorrect otherwise — with the
transaction and the unique key it would not be — but because a write during a
GET rules out a read-only web tier, and because in production B should have
already done it. If it has not, that is worth noticing rather than papering
over.

### D. Move the friendly URLs out

`/login`, `/logout` and `/register` were once a requirement. **That requirement
no longer holds** (stated 14 September 2026), so they can fall back to their
natural routes, which already work.

The cost is ten hard-coded links, which must change in the same commit:

| link | where |
|---|---|
| `/register` | `modules/mod_login/tmpl/default.php:233`, a `mod_mysubmissions` language string, and twice in `data.sql` (the hero button and "Create an account") |
| `/login/remind` | `starter.sql` ×2, in the footer |
| `/login/reset` | `starter.sql` ×4, in the footer |

Worth knowing: `/login/remind` currently 301s to
`/login/remind?Itemid=165&option=com_members&view=remind&task=remind`. There is
no `remind` item on a hub built from the migration — the URL matches `login` by
prefix, the component router takes `remind`, and the menu rule's
"route does not match link" branch bounces it. It works, via a round trip
nobody intended.

If the friendly spellings are wanted after all, they belong in `mainmenu` as
hidden items — a hub's own choice of URL, in the hub's own menu — not in a
generated registry.

### Where that leaves things

Nothing in a menutype nobody can explain. Everything is either **derived** (the
Component menu) or **deliberately chosen** (`mainmenu`, including hidden items
for URLs a hub wants to keep).

---

## Built on the day, and relevant here

- **`none` menu item type** — an item that names no component, for a front page
  the template draws itself. Commit `170bb5a85d`.
- **`menu_show`** — any item can be kept out of the menu while keeping its
  address, Itemid, modules and template style. Commits `efa9376181` and
  `5be2988f38`. This is what makes hidden routing items possible at all, and it
  is what B and D lean on.
- Hiding an item hides what is under it, matching Joomla's own `mod_menu`.

---

## Ideas raised and dropped

Listed so nobody re-opens them without knowing why they closed.

### Hide most of `mainmenu`'s items and show a curated subset

**Dropped.** The shipped menu has 25 component links under 5 groups, and the
instinct to show fewer is sound. But doing it with `menu_show` while `default`
still exists adds a *third* layer of bookkeeping — routes in `default`, aliases
in `mainmenu`, and now a visibility flag on the aliases — on top of a two-layer
workaround. Revisit after A and B, when there is one menutype to curate.

The one piece of it worth doing regardless: **stop advertising Tools**, for the
same reason it came off the front page — the middleware that runs a tool is not
distributed, so a stock hub cannot run one.

### Lazy generation as the primary mechanism

**Dropped as the primary; kept as the dev backup (C).** Three objections, two
of which the transaction answered:

- *It fires on the wrong event.* The entry is needed when `Route::url()` builds
  a link, which happens before anyone visits. Create-on-visit means the first
  link is still built without an Itemid. **This objection stands.**
- *Concurrency on the nested set.* Answered — transaction plus the existing
  unique key.
- *Coverage is incidental* — you get entries for whatever someone happened to
  hit, including `/oaipmh` and `/saml`. Stands, and is why B is the primary.

### Generate nothing at all

**Considered, not chosen.** The argument: an entry only buys an Itemid, and an
Itemid only matters for per-page modules and a per-page template style, which a
new hub has configured for neither. So let the fallback route without one and
let an admin add an item when they want either.

It lost to B once it became clear the macro already creates the admin item — at
which point generating the site one is a near-copy in a place that already
exists, rather than new machinery.

### A router rule for the contractual URLs

**Moot.** Proposed when `/login`, `/logout` and `/register` had to keep working:
put them in `routes.php` so no one could delete them. The requirement was then
withdrawn, so D applies instead.

### A module-position check in the install test

**Written, then removed.** It flagged the main menu module sitting in
`position-7`, which no template has drawn in years — correctly, but pointlessly:
`Migration20260913090000MainMenu.php` moves it to `user3`. Installing is two
phases, these files and then every migration in order, and a static test over
the files cannot see the second. Noted in the test's class docblock so it is
not re-added.

---

## Corrections to things said during the discussion

Recorded because they were stated confidently and were wrong.

- **"27 components are missing routes."** It is 14, and only **2** matter —
  `com_search` and `com_dataviewer`. The rest are admin-only, infrastructure, or
  machine-facing. `default` has drifted far less than claimed.
- **"A fallback-routed page has no Itemid, so no modules."** True only for
  components with no entry at all. The 28 in `default` do get an Itemid; that is
  the whole point of them.
- **"`default` is a hack."** It is three jobs under one name, two of which are
  legitimate and one of which (the aliases) is a workaround for path being
  derived from nesting. The name is the worst part.
- **"Joomla has solved this."** It has not. `NomenuRules` and `RouterView`
  (3.4+, standard in 4/5) route a component with no menu item, but Joomla's own
  manual calls the result — `/component/contact/contact/me?Itemid=1` — "a bad
  situation and to be avoided at all costs", for exactly the reason above: the
  page falls back to the home item's modules and template style. Joomla never
  decoupled path from nesting either, and its answer to "routable but not shown"
  is still a menu row, in a hidden menu or with `menu_show` off.

---

## Suggested order

1. **D** — smallest, independently useful, no routing change. Ten links.
2. **B** — contained, lands in a macro that already does the same thing.
3. **C** — small, and only affects development.
4. **A** — last, largest, and the only one that rewrites live URLs.

A and B are independent: B is worth doing whether or not A ever happens.

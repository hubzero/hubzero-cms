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

### B. Give a menu a type, and generate the component routes

A menu has no idea what it is for. `#__menu_types` holds `id`, `menutype`,
`title` and `description`, and on a real hub that reads:

```
id=1  menutype=mainmenu  title=Main Menu  description=The main menu for the site
id=2  menutype=default   title=Default    description=(empty)
```

That empty description is the complaint in one line. A menu called Default,
holding twenty-eight rows that turn out to be the routing table, explained
nowhere. And nothing marks it as not-for-display: it is invisible only because
no module happens to name it. Edit the menu module, pick Default, save, and the
whole routing table lands in the masthead.

So `#__menu_types` gains a **`type`**, and a menu says what it is:

| type | what it holds | shown |
|---|---|---|
| `display` | what a visitor sees - `mainmenu`, `about`, `legal` | yes, by a menu module |
| `component` | one generated entry per site component | never |
| `routing` | addresses a hub wants working but not shown | never |

Splitting generated from hand-added at the *menu* level rather than the row
level means no per-row protected flag is needed: a `component` menu is
generated and therefore locked, a `routing` menu is the hub's own and therefore
editable. One field decides both.

This is more work in the code than leaving it as convention, and that is the
trade being made deliberately: the difficulty belongs to whoever maintains this,
not to whoever has to understand a menu called Default.

#### The conditionals

There are fewer than expected, because the enumeration happens in two places
and they want opposite answers.

- **`com_menus/models/fields/menu.php:35`** - `SELECT menutype, title FROM
  #__menu_types ORDER BY title`. This is the dropdown `mod_menu` uses to choose
  which menu it renders (`mod_menu.xml:27`). **Filter to `type = 'display'`.**
  One `WHERE` clause, and a routing menu can no longer be pointed at by a menu
  module - which is what makes "never displayed" true rather than merely
  intended.
- **`com_menus/helpers/menus.php:121`, `getMenuLinks()`** - used by module
  assignment. **Leave unfiltered, on purpose.** A module must be assignable to a
  component or routing item, because that is the entire reason those items carry
  an Itemid. Same for template style assignment.
- **The Menu Manager's Menus tab**
  (`com_menus/admin/views/menus/tmpl/display.php`) - the three menus stay in the
  one list rather than gaining tabs of their own. They are menus; a tab implies
  a different kind of object, and then "where do I look up a URL" has three
  answers instead of one. Menus and Menu Items is two tabs; this would make
  four, and five the day another type is added.

  Two changes to that list:

  - **A Type column**, and the `description` finally populated. That is what the
    type field is for, and it tells you what a row is on the row you are about
    to click, which a tab cannot do because you have to pick the tab first.
  - **Suppress the Linked Modules actions on non-display menus.** That column
    currently offers *Add a menu module* and *Edit module settings* for every
    menu, so on a component menu it is a button that puts the routing table in a
    page. It should read "Not displayed" instead. Paired with the
    `fields/menu.php:35` filter this closes both ways in: a routing menu cannot
    be chosen in a module's settings, and such a module cannot be made from here
    either.

  The honest cost: a read-only menu sitting among editable ones invites clicking
  Edit and being refused. The Type column answers that better than a tab would.

- **The admin navigation** (`mod_adminmenu.php:62`, rendered at
  `tmpl/default_enabled.php:243`) lists every row of `#__menu_types` under
  Menus. Leave the non-display ones out of those shortcuts: that list is for
  menus somebody curates, and there are already 42 top-level admin items
  competing for the space. They stay reachable from the Menu Manager, which is
  the one place that answers what menus a hub has.
- **Item editing** - on a `component` menu, `alias`, `path`, `link`, `type` and
  `component_id` are read-only and the item cannot be deleted; `template_style_id`,
  `params` and `access` stay editable, because those are what the entry is for.
  A `routing` menu is fully editable.
- **`published`** on a component item follows the component, set by
  `EnableComponent` and `DisableComponent`. Otherwise a hub can switch off a
  route while leaving the component on, and have no way to work out why the page
  stopped answering.
- **Home and default** - only a `display` menu may hold the site default.
- **Deleting a menu** - `display` and `routing` yes, `component` no.
- **Escape hatch** - a locked item with no override is a trap the first time two
  components collide. The admin UI refuses; the `muse` command below can do it.

#### Generating the component menu

`AddComponentEntry`
(`core/libraries/Hubzero/Content/Migration/Macros/AddComponentEntry.php`)
**already does exactly this for the admin menu** at line 125: it takes
`$alias = substr($option, 4)`, sets `path` to the alias, links to
`index.php?option=com_x`, handles the nested-set rebuild, and is idempotent on
`(client_id, parent_id, alias)`. The site version is the same block with
`client_id` 0 and the component menutype.

97 migrations call this macro, so every component installed from here on is
covered. `substr($option, 4)` is also the rule the router fallback uses at
`routes.php:164`, so the registry and the fallback agree by construction rather
than by coincidence.

- **Which components qualify.** `com_cron` and `com_system` go through the same
  macro. The test is whether the component has a `site/` directory, either
  checked by the macro or passed by the caller the way `$createMenuItem` is now.
- **Existing hubs** need a one-time backfill, and on the evidence that is one
  component, `com_search`.
- **A `muse` reconcile command** for drift the macro cannot see - a
  site-specific component dropped in without a migration - which doubles as the
  override for locked items.

#### Migration

`type` defaults to `display`, so every existing menu keeps working. The existing
`default` menutype becomes `component`; once D has removed the friendly URLs it
holds nothing else. A `routing` menu starts empty and exists for a hub that
wants an address without a link to it.

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

Nothing in a menutype nobody can explain. Every menu says what it is for, and a
menu that is never displayed cannot be pointed at by a menu module. Entries are
either **generated** (a `component` menu, locked), **chosen but not shown** (a
`routing` menu), or **shown** (a `display` menu).

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

### Removing the component router's name fallback

**Kept.** Once every component has a generated entry, the fallback at
`routes.php:569` looks redundant. It is not: that rule does two jobs, and only
one of them is the fallback. The other is invoking the component's own router on
whatever segments are left after the menu rule has taken its prefix - which is
how `/resources/123/whatever` is parsed, entry or no entry. The `menu` rule
deliberately falls through when there is a remainder rather than returning.

Removing the name fallback alone would also turn a working-but-degraded page
into a 404 for the case where an operator is least able to diagnose it: a
site-specific component installed without a migration, which the macro never saw
and a backfill does not know about. And it would remove the hook C hangs on.

What it should do instead is **say so**. It currently succeeds silently, which is
why `com_search` has had no Itemid on every hub for years without anyone
noticing. One log line naming the component and pointing at the reconcile
command turns future drift into something that surfaces on its own.

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

---

## What is built, as of 2026-09-15

**D is done.** The friendly URLs that were in the old `default` menu were
separated from the component routes in the migration: an item whose alias is its
own component's name is a route and stays; anything else is a page and was moved
to the display menu with `menu_show=0`, which is how the front page and the
congratulations page stopped being filed under "Generated; not edited here".

**B is done.**

- `#__menu_types` has a `type` of `display`, `component` or `routing`, in
  `schema.sql` and in `Migration20260914180000MenuTypes.php`.
- The old `default` menu became `components`, typed `component`, with a
  description that says what it is.
- `Migration20260914190000ComponentRoutes.php` backfills a route for every
  enabled site component, less an exclusion list of the ones that are machinery
  rather than a page.
- `AddComponentEntry` takes a sixth argument and makes the route at install
  time; `EnableComponent` and `DisableComponent` publish and unpublish it, so a
  component that is switched off does not leave an address answering.
- `muse routes check` and `muse routes fix` report and repair. All three hubs
  read "28 site components should have an address. None is missing one."
- The menu picker in `com_menus` only offers `display` menus, so a menu module
  can no longer be pointed at the routing table.
- The Menus list shows each menu's type and description, and offers no "add a
  menu module" link for a menu that is never displayed.
- Item editing is locked on a `component` menu — `alias`, `path`, `link`,
  `type`, `component_id`, `menutype`, `parent_id`, `home`, `language` and
  ordering — in the form and again in `save()`, because a disabled field still
  posts. `destroy()` refuses. Title, note, access, template style, params and
  published stay editable.
- The component menu itself cannot be renamed, retyped or deleted, and no other
  menu can be turned into one.
- The menu form offers Display or Routing when creating a menu, so the third
  type has somewhere to come from. A routing item was confirmed to resolve, to
  render its component, and to stay out of the rendered menu.

**C is done.** A `lazyroute` parse rule, appended after `component` and wrapped
in `if (Config::get('debug'))`, gives a brand-new component its address on the
spot so a developer is not chasing a missing Itemid while the migration is still
being written. The check and the insert are one transaction with one retry. The
address lands after this request has already read the menu, so it is the next
request that gets the Itemid.

Verified with debug off, a deleted route stays deleted. With it on: the route
comes back on the next request, and `/notacomponent`, `/wp-admin`, `/etc/passwd`,
`/com_evil`, a deep URL under a real component, and two components that are
installed but switched off all create nothing.

All three callers - the install macro, `muse routes fix` and the router - now go
through one class, `Hubzero\Menu\ComponentRoute`. They had three copies of the
same insert and two different rebuilds, one of which did not derive `path`.

**A is done, and it rewrites nothing.** The fear in the plan was a migration
that changed every hub's URLs. It turned out not to need one: `#__menu` gains a
nullable `route`, empty on every existing row, and an item only moves when
somebody fills it in.

- `path` is still what the router matches on, so the router is untouched. It
  means "the address this item answers at" now rather than "computed from the
  alias field", and it is computed in one place rather than the three it was
  spread across.
- An item with a declared route answers there wherever it sits in the menu, and
  what is nested under it follows unless it declares its own.
- A save that would put two items at one address is refused: `idx_path` is not
  unique, so the database would accept it and the router would resolve it
  arbitrarily.
- The shared rebuild asks before overwriting a declared address, or installing
  anything at all would quietly undo it.
- A generated entry cannot declare one.
- The value is cleaned to lowercase path segments - no slashes at the ends, no
  empty or dot segments, nothing that means something else in a URL. Twenty-four
  tests in `core/components/com_menus/tests/RouteTest.php`, including that
  cleaning twice is cleaning once, because it runs on every save.

Demonstrated on lucent: a page two levels under About, with a child, declared
`/handbook`. Both moved, the old addresses 404, and the menu still draws the
item nested under About linking to `/handbook`.

Two fixes came with it. `rebuildPath()` stopped at direct children, so renaming
an alias two levels up left the bottom of the tree at an address that no longer
existed; it walks down now. And `starter.sql` inserted 105 menu rows
positionally, so adding a column shifted every value in every one - the install
test caught that, and they name their columns now.

### What A does not yet do: collapse the alias items

lucent's main menu holds twenty-nine alias items, and the plan said they "stop
having a reason to exist". They have not stopped yet, because of how A and B
meet.

An alias item at `nav-discover/resources` points at the generated entry at
`/resources`. To replace the pair with one item, that item must be nested under
Discover and declare `route = resources` - which is the address the generated
entry already holds, so the collision check refuses it. Deleting the generated
entry first is refused too. There is no order that works.

Making it work needs one more rule, and it is a real decision rather than a
detail: **a declared route displaces a generated entry**, on the grounds that
the generated one exists to guarantee an address and there now is one. The cost
is that the Itemid changes, and an Itemid is what per-page module assignments
and template styles are keyed to - so anything attached to the old entry is
silently attached to nothing.

Three ways to go:

1. The rule above, plus moving the module assignments and template style across
   when it fires. Most work, best result, and the only one that actually
   collapses the alias items.
2. Leave it. A is for pages - articles, headings, hand-made URLs - and
   components keep the alias-item arrangement they have. Nothing breaks and the
   main menu stays as it is.
3. Let a display item and a generated entry share an address, with the display
   one winning. Cheapest, but two rows claiming one URL is the thing the
   collision check exists to prevent, and `muse routes check` would have to
   learn to expect it.

Not chosen here.

### Defects found on the way, not fixed here

- `com_menus`' items controller calls `$model->batch($vars, $pks, $contexts)`,
  which resolves to `Hubzero\Database\Relational::batch(int $size)` and fatals.
  The model's implementation is `batchTask()`. Batch has never worked. The
  guard that keeps generated entries out of a batch is in `batchTask()`, ready
  for whenever that is corrected.
- `/redirect` returns 500.
- Admin pages throw `jQuery is not defined`.
- The first `content` build rule looks like it inherits the current page's
  Itemid when a link is built without one: `$item = $menu->getItem($uri->
  getUriVar('Itemid'))` inside `if (is_null($itemid))`. It passes null, and
  `getItem()` is `isset($this->_items[$id])`, which is always false for null.
  That branch has never done anything; it plainly means `getActive()`. Leaving
  it is what makes component links deterministic today, so this is a note, not
  a fix - see the section below on what wins.
- Deleting a component route leaves any alias menu item that pointed at it
  dangling, building URLs like `/content/?Itemid=142`. `muse routes fix` remakes
  the route under a new id and cannot mend the alias. Deletion is refused in the
  admin now, so the remaining way in is a direct database change.


---

## Which address wins, measured

Asked while reviewing A: with a main menu item pointing at a component and a
generated route for the same component, which one answers? Traced and measured
on lucent with both present - the generated `/resources` (Itemid 140) and a
`component` item nested in the main menu at `/nav-discover/resources-direct`
(Itemid 219).

**Parsing an incoming URL: nothing contends.** Each item answers only at its own
`path`. The parse rule walks the menu deepest-first and takes the longest
prefix of the request path that matches an item's `path` - and skips
`type = 'alias'` items entirely, which is why `/nav-discover/resources` is a 404
on a hub that has that alias in its menu. So `/resources` resolved to 140 and
`/nav-discover/resources-direct` to 219. Both 200. There is no choice to make
because they are different URLs.

**Building a URL: the generated route wins nearly everywhere.** The build rule
uses the Itemid it is given, and falls back to the component's own name when it
has none. Almost nothing passes one, so almost everything builds `/resources`.
Measured from *inside* `/nav-discover/resources-direct`, the component's own
links still came out `/resources/browse` and `/resources/new`. The only place
the main menu item's address appears is the main menu.

**Highlighting depends on the door.** With the alias arrangement, visiting
`/resources` lights up `item-166`, the alias in the main menu - the target's
Itemid is what mod_menu matches on, so the nav highlights correctly. With a
direct component item as well, visiting `/nav-discover/resources-direct` lights
up `item-219 current active`, but visiting `/resources` still lights up the
alias. Two doors, two highlights.

**So the alias items are not redundant with B.** Putting a component directly in
the main menu gives it a second address that the component itself never uses.
An alias item gives the nav an entry without creating a second address, which is
exactly the split this avoids. That is worth weighing against collapsing them:
option 1 in the section above would have to make the main menu item *be* the
component's entry rather than a second one beside it, and option 2 - leave the
alias items alone - now looks better than it did.

Nothing in A or B changes any of this. A only lets an item state its own `path`;
B only guarantees every component has one. They meet in exactly one place, which
is a display item wanting the same address as a generated entry, and that is the
case the collision check refuses.

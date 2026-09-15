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
- ~~Deleting a component route leaves any alias menu item that pointed at it
  dangling, building URLs like `/content/?Itemid=142`.~~ Fixed: the menu now
  drops an alias whose target is not loaded. `muse routes fix` still remakes a
  deleted route under a new id and cannot mend an alias pointing at the old one,
  but the alias no longer builds a broken URL - it simply is not drawn.


---

## Which address wins, measured

**This section records how it behaved before the precedence rule below. Kept
because the measurement is what justified changing it.**

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

---

# Part two: precedence, and tying it to the extensions table

Everything above was about giving each component an address. This part is about
what happens when two things want the same one, and about an address that should
stop working. Each item below was measured before and after against the same
probe — seventy-eight URLs across the three hubs, status and body size.

## E. A precedence rule

**The problem.** Two menu items can answer at one address. Which one won was
decided by `lft` — whichever sat earlier in the menu tree — and nothing else.
Proved by building two menus with the same path and swapping only their `lft`
values: the winner flipped.

Worse than "menu order", because every menu's top-level items hang off one root
interleaved by `ordering`. On lucent `/resources` (components, ordering 1) sits
next to `/nav-discover` (mainmenu, ordering 1), so reordering the main menu in
the admin can hand an address to a menu the admin is never shown alongside it.
`lft` is also reassigned wholesale by any rebuild, and the two rebuilds in the
tree disagree — `com_menus` orders children by `lft`, `ComponentRoute` by
`ordering` then `lft` — so installing a component could change the answer.

**The rule.** A menu the hub shows beats one it does not, and both beat the
entry generated for a component:

    display  >  routing  >  component

A longer address still wins first. Where the kind ties, the old behaviour still
decides, so nothing previously unambiguous moves.

The point of that order: **a generated entry is a floor, not a claim.** It
exists so every component has some address, and it steps aside the moment the
hub says otherwise. Take the hub's item away and the generated one answers
again, with no cleanup step.

Verified on lucent: a main menu item declaring `/resources` wins at `lft` 960
against the generated entry's 119, where the lower `lft` used to take it.

The save-time check follows the same order — an item may take an address off a
weaker claim and not off an equal one — which is how a hub puts a component in
its own menu at the component's own address.

`muse routes check` reports addresses claimed twice, saying which answers and
which is shadowed, because a rule that is deterministic is still invisible.

## F. The same question backwards

There was no reverse lookup at all. Building a URL with no Itemid used
`substr($option, 4)`, the component's name, full stop. So a hub whose Resources
page sat at `/library` still emitted `/resources/browse` from inside `/library`,
losing the Itemid and with it that page's modules and template style. The hub's
own page was a dead end.

There is now a lookup for the item that *speaks for* a component, ranked
identically to the parse side.

It counts an item whose link is the component and nothing else — one naming a
particular view is a page about something narrower, and a link that asked for
the component should not land there — **or** a component menu's entry whatever
its link says. That second clause exists because real data needed it: welcome's
entries came from the old `default` menu and carry
`index.php?option=com_resources&view=intro` and `&view=index&layout=display`.
A bare-link-only rule worked on lucent and silently did nothing on welcome.

## G. Non-SEF URLs had no page identity

`/resources` and `index.php?option=com_resources` are the same page. One had an
Itemid and the other had none, so one had a template style, per-page modules and
a breadcrumb and the other had none of the three.

A non-SEF URL that *named* its own Itemid was ignored too:
`index.php?option=com_x&Itemid=12` carried it as a query var and never activated
it. The menu rule returned early for a non-SEF URL before `setActive()` was
reached — and there is a commented-out `|| isset($query['Itemid'])` beside that
return, which looks like somebody noticed and stopped.

Now: honour the Itemid when given, otherwise use the same lookup as F.
`index.php?option=com_content` still activates nothing, because no bare
com_content item exists.

## H. Every component with site code gets an address

The skip list was a judgement about which components are worth an address, and
that kind of list ages badly — a component that is machinery today may grow a
page tomorrow and nobody will remember to take it off.

It is now only the components with no site code to run at all:

| | what is in `site/` |
|---|---|
| com_media | one class and a helper |
| com_messages | a language file, nothing else |
| com_system | a router and a class |

Everything else gets one, endpoints included. That costs nothing: the router
already resolves `/cron` by component name whether or not an entry exists, so
the entry only adds an Itemid, and the day one of them grows a page it has
somewhere to put its modules. Counts went 28 to 36 on welcome and mesozoic, 37
on lucent.

**"No `site/views` directory" is not the test**, though it looked like a tidy
mechanical one. com_dataviewer has none and is a full site component —
`Controller.php`, `View/Gallery.php`, `View/Spreadsheet.php`, its own router, a
tree of assets — written in a namespaced layout rather than the old one. A rule
built on that directory would have taken its address away.

Before settling on this, each old exclusion was measured at its own name rather
than argued about: com_cron answers JSON, com_oaipmh OAI-PMH XML, com_mailto
402, com_oauth and com_media 403, com_saml, com_messages, com_system and
com_dataviewer 404, com_redirect 500. com_content routes through article paths
and has never answered at `/content` — and a `/content` entry was tried on
welcome and changed nothing, because com_content's router rejects the bare view.
com_help was the one that looked wrong, since it serves a real HTML page, but
its controller does `Request::setVar('tmpl', 'help')`, so a template style from
an entry would be ignored.

**Found while doing it:** the dev-only lazy generation knew about none of this.
`routable()` asked only whether a `site/` directory existed, so with debug on,
requesting `/cron`, `/oaipmh`, `/help` or `/media` created an entry for each —
28 entries became 32. The list had been living in two files and being ignored by
a third. It has one home now, in `ComponentRoute`.

## I. The menu's unique key, which sample data was throwing away

`schema.sql` declares `idx_client_id_parent_id_alias_language` UNIQUE. Two lines
in `starter.sql` dropped it and added it back as a plain index, so **every hub
installed with sample data has run without the constraint ever since.** Only a
hub installed without sample data kept it — which is why lucent refused a
duplicate alias with an integrity error while welcome and mesozoic quietly
accepted one.

It was dropped because the sample data breaks it, in two places:

- two separators under Discover, ids 46 and 49, both aliased `n`. They sit at
  different points in the menu, so both are wanted; they only needed different
  names.
- the main menu's Support entry, id 8, sharing the root with the component entry
  it points at, id 84, both aliased `support`. Which is this whole line of work
  in miniature: a display item and a component route wanting one name, and the
  response being to delete the constraint.

Both renamed, neither moving a URL — an alias item and a separator are never
matched against a request, so their alias is a label. A migration does the same
for installed hubs and is careful which of a pair it renames: the cosmetic one,
never the one answering at a path. A group with no cosmetic member is reported
and left, because moving a working address under a hub unasked is worse than
leaving a duplicate.

The install test now reads the `UNIQUE` and `PRIMARY KEY` declarations out of
`schema.sql` and holds the data files to them, listing every clash rather than
the first. It would have caught this the day it was written. Making it work
needed the reader to model `REPLACE` properly — it appended where a database
replaces the row with the same key, and `starter.sql` replaces assets `data.sql`
already wrote.

## J. An item is only a page while its component is switched on

A menu item for a component the hub has switched off is not a page. The
dispatcher already refused to run one — `/citations` 404s the moment
com_citations is disabled — but the item stayed in the menu as a dead link, kept
its Itemid, and went on holding its address against anything else that wanted
it.

**A join, not a second copy of the fact.** The menu already joins
`#__extensions` for each item's component name, so it now also leaves out any
item whose component is not enabled. That is right however the component was
switched off — migration, extension manager, or by hand — because there is
nothing to keep in step.

An item with no component says so with `component_id` 0 and stays. Anything else
must find its component enabled, so an item left behind by an uninstalled
component goes too rather than joining to nothing and being kept.

Aliases go with it, which also fixes the dangling-alias URL noted earlier.

**And the publish tie had to go**, because it was worse than redundant.
`EnableComponent` and `DisableComponent` kept an entry's published state matching
the component, so disabling by migration and re-enabling in the extension
manager left the entry unpublished: the component ran, answered through the name
fallback, and had no Itemid, with nothing to say why. `create()` had the same
asymmetry, copying `enabled` into `published`. Neither does now, a migration
publishes the entries that were switched off on a component's behalf, and an
entry's published state goes back to meaning what it means everywhere else —
whether the administrator wants the page.

Round trip on welcome, where Resources has an alias in the main menu:

| | `/resources` | nav "Resources" | dangling `Itemid=` |
|---|---|---|---|
| enabled | 200, 19982B | 2 | 0 |
| disabled | 404 | 1 | 0 |
| re-enabled | 200, 19982B | 2 | 0 |

## What changed on the live hubs, in total

Seventy-eight URLs across three hubs, before Part two and after. **Seventy-two
byte for byte identical.** The six that moved are error pages on lucent, each
eight bytes shorter, and the eight bytes are ` current` disappearing from the
**Home** nav item: a URL with no menu entry was making the menu falsely
highlight Home, and those URLs now have entries.

## Still not done

Four of the five things once listed here are done: the batch processing, the
/redirect 500, the admin control panel's jQuery, and the `getActive()` branch -
which turned out to be the thing worth fixing rather than the thing to leave
alone. What remains:

- **Convert the alias items into nested items.** The substantive one. Twenty
  to twenty-five per hub. No code change is needed - the router, the precedence
  rule and the prefix inheritance all support it - so this is a data migration
  per hub, and one that changes urls, so it wants deciding rather than doing
  quietly.
- **How much of the codebase builds a component url without `Route::`.** The
  prefix inheritance only reaches links that go through the router; anything
  concatenating a url by hand still emits the flat route, and a section's
  prefix leaks away there. Not measured.
- **Canonical urls.** One component at several addresses is now a hub's choice
  to make, and nothing emits a canonical link tag. Raised, not addressed.
- **The tie-break between two items of equal kind** claiming one address, on
  both the parse and the build side: the one earlier in the tree wins. Both
  halves agree, which is the most that can be said for it. A hub that cares has
  to decide by ordering.

Nothing in this work has been pushed.

## A note on how this was checked

Two measurement mistakes worth recording, because both produced confident wrong
statements before they were caught.

**Wrong port.** The three hubs are on separate ports — welcome 7500, mesozoic
7600, lucent 7700. Requests to `welcome.example.com:7700` reach lucent's
listener with a Host it does not serve and come back as an empty `200`. Every
HTTP check reported for welcome and mesozoic across a long stretch of this work
was that empty response. Status codes alone hid it.

**Status without size.** `200` on its own says almost nothing. Every probe here
records the body size as well, which is what made the eight-byte ` current`
difference visible and what would have caught the wrong port immediately.

---

## Correction: the alias items should become nested items, not flat ones

The section above treats an alias item as something to collapse *away*, and
weighs whether a display item should take the flat address `/resources` off the
generated entry. That framing is backwards, and the corrected version is this.

**A nested menu item is supposed to answer at its section's address.** Resources
under Discover should be `/discover/resources`. That is what nesting it means,
and it was never the problem.

**The problem was that the prefix did not stick.** Once a visitor was inside a
section, the next link to the same component dropped back to the flat route. The
build rule has always meant to prevent that - given a link with no Itemid it
looks up the current page and carries its Itemid through if the page belongs to
the same component - but it asked `getItem()` for the Itemid it had just
established was null, so it fetched nothing every time. `getActive()` is the
page you are on. Fixed.

**Which makes the alias items the thing standing in the way.** The router never
matches `type = 'alias'`, so `/nav-discover/resources` is a 404 for as long as
Resources is an alias; the alias exists only to be drawn, and it draws a link to
the target's flat route. A prefix cannot stick to an address that does not
exist.

So the direction is to **convert each alias into a real nested item**, not to
flatten it:

- it answers at `/discover/resources`, which is what the nesting says
- links inside the section keep the prefix - `/discover/resources/browse`
- the generated `/resources` stays underneath as the floor, and is not removed
- the row count does not change, and nothing is deleted
- the item an administrator sees and edits is the one that governs the page,
  so a template style or per-page module assigned to it finally applies

Measured on lucent with Resources converted: `/nav-discover/resources` and
`/nav-discover/resources/browse` both answer, links inside the section keep the
prefix, a link built from an unrelated page goes to the nested item, and
`/resources` still answers underneath.

**Generated component entries are not to be removed.** They are the floor. The
earlier option of deleting a shadowed entry is withdrawn.

### One component, several addresses

A consequence of the above, and an intended one: once menu items are nested
rather than aliased, a component can be reached by more than one address, and
what differs between them is the Itemid.

Measured on lucent with com_resources in two sections and the generated entry
underneath:

| address | Itemid | active item |
|---|---|---|
| `/resources` | 246 | the generated entry, nothing highlighted in the menu |
| `/nav-discover/resources` | 166 | `item-166 current`, under Discover |
| `/nav-learn/resources` | 249 | `item-249 current`, under Learn |

All three answer, with the same component rendering the same content. What the
Itemid carries is the page identity: the per-page modules, the template style,
the breadcrumb, and which entry the menu highlights. So the three are not
duplicates by accident - they are three presentations of one component, which is
what a menu item has always been for.

Each keeps its own context. From inside `/nav-discover/resources` the next link
is `/nav-discover/resources/browse`; from `/nav-learn/resources` it is
`/nav-learn/resources/browse`; from `/resources` it is `/resources/browse`. The
three worlds do not leak into each other.

**What this asks of a hub.** The same content now sits at several urls, so a hub
that cares about that has to decide which is canonical and say so - the router
does not, and never did. Nothing here emits a canonical link tag. That is worth
knowing before a hub puts one component in three sections.

**How a link from outside picks.** A link built for a component from a page that
is not that component has no context to inherit, so it asks which item speaks
for the component: highest ranked wins - display over routing over generated -
and where the rank ties, the item earlier in the tree. Which is the same
fallback the parse side uses when two items of equal kind claim an address, so
both halves settle it the same way. It is still a tie-break rather than an
answer: a hub with a component in two display menus gets the one that happens to
sit earlier. If which one is canonical matters, the hub should say so by
ordering, or keep a single non-nested item for that component.

**`muse routes check` does not report this**, and should not: several addresses
for one component is a hub's choice, not a fault. What it reports is two items
claiming *the same* address, which is the case where one of them cannot be
reached.

### What is still open

The inheritance only helps a link that goes through `Route::`. Anything building
a component url by string concatenation bypasses the router and will still emit
the flat route, so a section's prefix will leak away at those links. How much of
the codebase does that has not been measured.

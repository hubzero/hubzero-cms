<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
-->
# How a hub fits together

This chapter explains how the hub builds a page, how its features fit
together, and where it stores your content. That helps you decide which
administrator screen to use when you want to change something.

It is for the person who has just been given a hub to run and has no
background in the software. If you write code for a hub, read
[Application structure](../../developers/03-foundation/01-structure.md)
instead; it covers the same ground with the detail a developer needs.

## The two halves

A hub is one application with two faces.

The **front end** is the site people visit: the home page, the groups, the
resources, the search box. The **administrator interface**, or back end, is
at `/administrator` and is where you configure all of that.

They are not two programs. They are the same installation, running the same
components against the same database, with one difference: the first path
segment of the URL. `/administrator/…` selects the administrator *client*,
`/api/…` selects the REST API, and anything else is the site. The choice is
made by
[`ClientDetector`](../../../core/libraries/Hubzero/Base/ClientDetector.php)
on every request, and it decides which set of controllers, views, template
and configuration the request runs against — `admin/` instead of `site/`
inside each component.

That is why a component you disable vanishes from both the **Components**
menu and the public site at once, and why there is no separate back-end
address, host or port to remember.

[The administrator interface](01-administrator-interface.md) covers reaching it, who is
allowed in, and what each of its menus holds. Read that chapter for the back
end itself; this one is about what the back end is editing.

## How a page gets built

Four extension types build the pages you manage:

| Extension | Its job | Example |
|---|---|---|
| Component | Produces the page's main content | The article displayed on an About page |
| Module | Adds a small block in a named position | A navigation menu beside the article |
| Template | Supplies the page layout and appearance | The header, columns, footer, and styles |
| Plugin | Adds behavior to another feature | A Forum tab inside a group |

You configure them from the **Components** and **Extensions** menus.
The same module type can have several instances: two menu modules can show
different menus in different positions.

A page is assembled in four stages:

1. **The request arrives.** The web server sends a page request to
   `index.php`, the CMS entry point. Static files such as images and
   stylesheets can be served directly.
2. **The router picks a component.** It looks at the URL and decides which
   component owns it, and it sets two things: `option`, the component's name,
   and `Itemid`, the id of the menu item that matched. Remember `Itemid`; it
   matters in a moment.
3. **The component produces the main content.** Exactly one component runs
   per page. It reads the database, applies its own permissions, and returns
   the block of HTML that is the actual subject of the page — the article,
   the group's forum, the list of resources.
4. **The template wraps it, and the modules fill in around it.** The
   template is a page skeleton with named holes in it. One hole takes the
   component's output. The rest are **positions**, and each position renders
   whichever modules are published there.

Here is the middle of the shipped site template, which is the whole idea in
one screenful — an optional column of modules, the component, another
optional column of modules:

<!--include: core/templates/kimera/index.php:232-252-->

Two consequences follow from that order, and between them they explain most
of the surprises.

**The component does not know about the modules, and the modules do not know
about the component.** They are chosen independently: the component by the
router, the modules by their position and their page assignment. A page can
therefore have perfect content and no navigation, which is exactly the
failure described in step 7 of
[The first week with a new hub](02-first-week.md) — a menu that exists, in a
position no template renders, on a site whose pages are otherwise fine.

**Modules are assigned to pages by menu item id.** A module is shown if it is
published, if its position is in the template, if the viewer's access level
allows it, and if its page assignment includes the current `Itemid`. Modules
set to appear on all pages carry no id at all and always render. So:

- A page reached without a menu item — a component URL a visitor typed, a
  deep link into a group — has no `Itemid`, and shows only the all-pages
  modules.
- Deleting a menu item and creating a replacement gives you a *new* id.
  Modules assigned to the old one are now assigned to a page that does not
  exist, and quietly stop appearing. If a sidebar disappears after a menu
  tidy-up, this is why. Edit menu items rather than replacing them.

[Menus](../07-menus.md) covers the menu side and
[Modules](../10-extensions/01-modules.md) the module side; the point here is
only that they are two halves of one decision.

> **Note:** Of everything the template asks for, the modules are rendered
> first — before even the page's `<head>` is written. That is deliberate: it
> lets a module add a stylesheet or a script and still have it appear in the
> head of the finished page. The component's output was produced a step
> earlier still, before the template was opened at all.

### A worked page

Take the front page a new hub gets in step 7 of
[The first week with a new hub](02-first-week.md): an article, shown by a menu
item of type **Articles > Single Article**, marked **Home**.

A visitor asks for `https://example.com/`. The path is empty, so the router
takes the menu item marked **Home**, and from it sets `option=com_content`
and the article's id, plus that item's `Itemid`. The Content component loads
the article, checks its state and its access level, and renders it. The
template then puts that HTML in the component hole, renders the **Main Menu**
module into `user3`, the breadcrumb module into `breadcrumbs`, and whatever
else is published into `footer` and the rest, and sends the finished page.

Change the article's text and only stage 3 changes. Change which menu item is
**Home** and stage 2 changes, which changes the `Itemid`, which changes which
modules stage 4 renders. That is the whole mechanism.

## Where things live

Two trees on disk, and one database.

| Tree | What is in it |
|---|---|
| `core/` | The platform: the framework, and every component, module, plugin and template the release ships. An upgrade replaces this tree wholesale. |
| `app/` | This one hub: its configuration, its uploads, its cache and logs, and any extension it adds or replaces. |

`app/` wins. When the hub looks for a component, a module, a plugin or a
template, it checks `app/` first and uses `core/` only if it finds nothing —
so a hub customises itself by adding files under `app/`, never by editing
`core/`. A change made in `core/` works until the next upgrade and then
disappears.

The database holds everything the administrator interface edits as records:
articles, menu items, modules and their positions, members, access groups and
levels, categories, component options, cron jobs, forum posts, group
memberships. Almost every screen in this book writes there.

Files on disk hold everything else: uploaded and generated content under
`app/site/` — group files, media, member files, publications — plus the
configuration in `app/config/`, and the cache and logs. The Media Manager
writes into `app/site/media`. Global Configuration writes plain PHP files
into `app/config/`.

Which of the two your actions touch is not academic, because it decides what
a backup has to include:

- **The database alone is not a backup.** Restore it without `app/site/` and
  every record survives pointing at a file that is gone.
- **`app/` alone is not a backup either.** It has the uploads and the
  configuration but none of the content.
- **`core/` need not be backed up at all.** It is the release, and you can
  fetch it again. It should also not have been modified; if it has, that is
  the thing to find out about before an upgrade, not after.

So: back up the database and `app/`, together, at the same moment.
[Daily maintenance](../03-maintenance/index.md) covers the rest of the
running-hub routine.

## The four kinds of extension

Everything the hub puts on a page comes from an extension, and there are four
kinds. [Extensions](../10-extensions/index.md) explains each one properly,
including where they live and which screen manages them; one line each here
so the words mean something in the meantime:

- A **component** is an application. It owns the main body of the page, and
  exactly one runs per request.
- A **module** is a small block placed around the component, in a named
  position the template offers.
- A **plugin** answers events — a login, a save, a page about to render — and
  also supplies the tabbed sections of groups, member profiles and projects.
- A **template** is the page skeleton: the layout, the stylesheets, and the
  list of positions modules can go in.

## The table prefix

Sooner or later something technical — a migration, a support answer, a
configuration field, this documentation — shows you a table name written
like this:

```text
#__resources
```

`#__` is not part of the name. It is a placeholder that the database layer
rewrites into the hub's real table prefix on the way to the server, every
time a query runs. On this tree the default prefix is `jos_`, so `#__users`
becomes `jos_users`.

The prefix exists so that several hubs can share one database server, or one
database, without their tables colliding. Because it is chosen per
installation, no code may assume it. Extensions that need a table name build
it from the configured value rather than typing it — the **My Points** module
does exactly that, joining `Config::get('dbprefix')` to `users_points`. Code
that hardcodes a literal prefix works on the hub it was written for and
breaks on every hub that chose a different one, which is why you will see
`#__` and never a real prefix in anything shipped.

There is a **Database Tables Prefix** field in Global Configuration, on the
**Server** tab. It is not a rename tool.

> **Warning:** Changing **Database Tables Prefix** does not rename anything.
> It only changes which tables the hub looks for, so the next request finds
> none of them and the hub stops working. The field exists for the case where
> the database itself has been moved or restored under different table names.
> Leave it alone otherwise. See
> [Global configuration](../05-configuring/01-hub.md).

## What you can break, and what you cannot

Most of what a new manager is nervous about is reversible, and a few things
that look harmless are not. Nothing in the administrator interface tells you
which is which, so this is the list, and it is the most useful page in the
book to have read before you start clicking.

Nearly everything here follows from three questions. Ask them at any screen,
including screens this book does not cover.

1. **Does it change a value, or remove a row?** Changing a value — a state, a
   position, an option, an access level — writes over one field and leaves the
   record where it is. Removing a row takes the record and, often, the files
   and child records hanging off it. The first kind is undone by doing it
   again. The second is undone only from a backup.
2. **Has anything left the building?** A change to the hub stays on the hub
   until you undo it. Mail does not. Neither does anything a member has
   already seen, downloaded or cited. There is no unsend.
3. **Can the same screen put it back?** If the screen that made the change
   offers the opposite of it, the change is cheap. If undoing it means editing
   a file, restoring a table, or asking somebody to re-upload their work, slow
   down and take the backup first.

### Hard to break

- **Disabling and unpublishing.** Disabling a component or unpublishing a
  module deletes nothing. **Enable** and **Publish** put it back exactly as
  it was, options and all.
- **Uninstalling a shipped extension.** You cannot. There is no **Uninstall**
  button in the Extension Manager — it is commented out of the view. See
  [Extension Manager](../10-extensions/04-extension-manager.md).
- **Changing an item's state.** Publishing, unpublishing and the rest write a
  number into a column; the row stays where it is. Read
  [States, deleting and check-out](../08-content/states.md) before assuming
  which number a given label means, because the labels and the stored values
  do not line up as neatly as the screens suggest.
- **A record that will not open because it is "checked out".** Nothing is
  wrong with it. **Site → Maintenance → Global Check-in** releases every
  locked record on the hub.
- **Moving modules between positions.** Free, and the fastest way to learn
  what a template's positions actually are: put a module somewhere and look.
- **Categories.** Creating them costs nothing, and they are scoped per
  component, so a category made for articles cannot disturb the knowledge
  base. See [Categories](../08-content/categories.md).
- **Clearing the cache.** **Site → Maintenance → Clear Cache** removes stored
  cache groups and **Purge Expired Cache** drops the expired ones. Both throw
  away derived data the hub regenerates on demand. Neither touches content,
  and the worst outcome is a slow first page.
- **Global Configuration itself.** It writes plain PHP files under
  `app/config/`, one per group of settings. Whatever you save there can be
  read and corrected in a text editor, so a bad save is never a locked door —
  you do not need the interface to get back into the interface.
- **Deleting an access group you belong to.** The screen refuses, skips that
  row and says so; it also refuses to let an account without `core.admin`
  delete a group that holds it. You cannot remove your own way in with that
  button.

### Easy to break

- **Editing anything under `core/`.** It works until the next upgrade, then
  vanishes without warning. Put the change under `app/` instead.
- **Changing the table prefix**, as above.
- **Deleting the shipped access groups.** **Public** and **Registered** are
  referenced by the default viewing levels and the registration options.
  Delete your own groups freely; leave the eight shipped ones in place. See
  [Access groups](../06-users/06-accessgroups.md).
- **Deleting menu items.** Cheap to do, and it silently strips the module
  assignments that pointed at them, as above.
- **Leaving the hub with no item marked Home.** There must be exactly one,
  because it is what the router falls back to for the bare address.
- **Real deletions.** Removing a member, a group or a file is not a state
  change. It is gone, and only the backup you took in the section above will
  bring it back.
- **Deleting a hub group.** The worst of those, because the button is on an
  ordinary list screen and gives no hint of its reach. Deleting a group fires
  `groups.onGroupDelete` at every group plugin, so the forum, the wiki, the
  blog, the collections, the resources and the wishlist each remove their own
  material before the group row goes. What is left afterwards is a log entry
  recording the members and the description. Unpublish the group instead if
  what you want is for it to stop being visible. See
  [Groups](../06-users/05-groups.md).
- **Saving a component's Options without changing anything.** The save writes
  every field on the form, not only the ones you touched: values that were
  merely defaults become stored values, and any stored parameter the form does
  not declare is dropped. Usually harmless, occasionally not — Media's **Path
  to Files Folder** and **Path to Images Folder** default to `images` in the
  manifest while the code reads `site/media`, so opening that screen and
  saving it untouched points the Media Manager at a directory that does not
  exist. This is a known defect.
- **Mass Mail Users.** **Users > Mass Mail Users** sends to every account in
  the access group you pick, and the hub reports how many it reached. Nothing
  reverses that. Send to a group of one first.

The honest summary: you can explore the administrator interface freely as
long as you are toggling states and positions, and you should slow down at
three kinds of button — one that deletes something, one that sends something,
and one that changes where the hub looks for its data. Everything else on
these screens is a stored value that the same screen will change back.

None of that is a reason to be timid with the rest. A manager who is afraid
of the interface leaves the hub misconfigured, which is a slower and more
certain kind of damage than any of the accidents above. Take the backup, then
go and look.

## Where to go next

- [The administrator interface](01-administrator-interface.md) — the back end itself, menu
  by menu.
- [The first week with a new hub](02-first-week.md) — the ordered path through the
  decisions a new hub needs.
- [Extensions](../10-extensions/index.md) — the four kinds, in full.
- [Application structure](../../developers/03-foundation/01-structure.md) —
  the same picture with the code paths, for readers who want the detail.

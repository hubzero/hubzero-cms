<!--
status: rewritten
reviewed-against: 2.4-main @ 754ab96b09
reviewed: 2026-09-10
screenshots: none
-->
# How a hub fits together

The mental model the rest of this book assumes. Every other chapter tells you
which screen to open and which field to set; none of them tells you what the
hub is doing underneath, so the first time a change you made in one screen
appears — or fails to appear — somewhere else entirely, there is nothing to
reason from. This chapter supplies that. It is short, and it is worth reading
once before the screen-by-screen chapters rather than after.

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

[The administrator interface](administrator.md) covers reaching it, who is
allowed in, and what each of its menus holds. Read that chapter for the back
end itself; this one is about what the back end is editing.

## How a page gets built

This is the single most useful thing to understand, because almost every
"why did that happen?" question on a hub is answered by it.

A page is assembled in four stages:

1. **The request arrives.** One file, `index.php`, handles every URL on the
   hub. Nothing else is ever reached directly.
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

<!--include: core/templates/kimera/index.php:172-192-->

Two consequences follow from that order, and between them they explain most
of the surprises.

**The component does not know about the modules, and the modules do not know
about the component.** They are chosen independently: the component by the
router, the modules by their position and their page assignment. A page can
therefore have perfect content and no navigation, which is exactly the
failure described in step 7 of
[The first week with a new hub](02-newhub.md) — a menu that exists, in a
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
[The first week with a new hub](02-newhub.md): an article, shown by a menu
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
[Daily maintenance](../03-maintenance/README.md) covers the rest of the
running-hub routine.

## The four kinds of extension

Everything the hub puts on a page comes from an extension, and there are four
kinds. [Extensions](../10-extensions/README.md) explains each one properly,
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
that look harmless are not. Worth knowing which is which before you start
clicking.

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

The honest summary: you can explore the administrator interface freely as
long as you are toggling states and positions, and you should slow down the
moment a screen offers to delete something or to change where the hub looks
for its data.

## Where to go next

- [The administrator interface](administrator.md) — the back end itself, menu
  by menu.
- [The first week with a new hub](02-newhub.md) — the ordered path through the
  decisions a new hub needs.
- [Extensions](../10-extensions/README.md) — the four kinds, in full.
- [Application structure](../../developers/03-foundation/01-structure.md) —
  the same picture with the code paths, for readers who want the detail.

<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/index
source-id: 3336
imported: 2026-09-09
-->
# Getting started

This book is for the person who runs a hub. It covers the administrator
interface — the back end at `/administrator` — and the settings, screens and
extensions you reach from it. It starts from a hub that is already installed
and answering; it does not cover installing the software.

## What a hub manager does

A hub is a Hubzero site: a public front end that members browse, and a private
back end where you configure it. Almost everything in this book happens in the
back end. The recurring jobs are:

- Configure the site: its name, its template, its menus, and the components
  that provide its features.
- Manage members: their accounts, the access groups they belong to, and what
  each group is allowed to do.
- Curate content: articles, resources, publications, and whatever the hub's
  members submit for approval.
- Install and configure extensions: components, modules, plugins, and
  templates.
- Keep the site healthy: support tickets, cron jobs, site notices, and the
  spam filters.

## Read this first

[The administrator interface](#the-administrator-interface) explains how to reach the back
end, who is allowed in, and what each of its menus contains. Read it before
anything else in this book; every other chapter assumes you can find your way
around those screens.

Then, if the hub was installed this week, work through
[The first week with a new hub](#the-first-week-with-a-new-hub). It puts the decisions a new hub
needs — the site name, mail, registration, access, which components to keep,
the front page and menu, and the scheduled jobs — into the order that works,
and links to the chapter covering each one.

[How a hub fits together](#how-a-hub-fits-together) is the short chapter that explains
what the hub is doing underneath those screens: the two halves of the
application, how a page is assembled out of a component, a template and its
modules, where the platform ends and your hub's own material begins, and what
you can and cannot break. Every other chapter in this book assumes it. Read it
whenever a change made in one screen turns up somewhere you did not expect.

## Then

- If the hub is new, go on to [Advanced setup](02-advancedsetup.md) and
  [Configuring](05-configuring/README.md). Between them they cover the global
  configuration, the site template, the menus, and registration — the choices
  that are hardest to change once members have started using the hub.
- If you are taking over a hub that is already running, go to
  [Daily maintenance](03-maintenance/README.md) and
  [Users](06-users/README.md).
- If you are looking for one particular screen, the
  [Components](09-components/README.md) and
  [Extensions](10-extensions/README.md) chapters are organised the same way
  the **Components** and **Extensions** menus are.

Every setting the interface offers is also listed, component by component, in
the generated
[configuration reference](../reference/configuration/README.md).
## The first week with a new hub

What to do first with a hub that has just been installed, in the order that
works. The software is running, the site answers, and you can sign in to the
back end — and now several dozen screens are asking for decisions, some of them
painful to change once members have accounts and content. This chapter puts
those decisions in order, links to the chapter that covers each one in full,
and says which shipped defaults surprise people.

It is for the person who has just been handed a new hub. It does not repeat
what the linked chapters say; read it beside them. If you are taking over a
hub that already has members, this is the wrong chapter — go to
[Daily maintenance](03-maintenance/README.md) and
[Users](06-users/README.md) instead.

Before anything else, read [The administrator interface](#the-administrator-interface).
Everything below happens in the back end, and every step names a menu path
through it.

### The order

1. [Take the site offline](#1-take-the-site-offline) while you work.
2. [Set the site identity](#2-set-the-site-identity) — name, address, time
   zone, environment.
3. [Make mail work](#3-make-mail-work), before anything that sends any.
4. [Decide how people register](#4-decide-how-people-register).
5. [Decide who can do what](#5-decide-who-can-do-what).
6. [Turn off what the hub does not need](#6-turn-off-what-the-hub-does-not-need).
7. [Give the site a front page and a menu](#7-give-the-site-a-front-page-and-a-menu).
8. [Schedule the recurring jobs](#8-schedule-the-recurring-jobs).
9. [Bring the site back online](#9-bring-the-site-back-online).

Steps 3, 4 and 5 are the ones that are hard to undo. A hub that opens
registration before its mail works collects accounts that can never confirm
themselves.

### 1. Take the site offline

**Site > Global Configuration**, **Site** tab, **Offline Settings**.

| Field | Default |
|---|---|
| **Site Offline** | No |
| **Offline Message** | Use Custom Message |
| **Custom Message** | *This site is down for maintenance. Please check back again soon.* |
| **Offline Image** | empty |

A new hub is online. Set **Site Offline** to **Yes** and the public front end
returns the template's offline page with a `503 Service Temporarily
Unavailable` status:

<!--include: core/bootstrap/Site/Providers/DocumentServiceProvider.php:81-86-->

Anyone holding `core.login.offline` still sees the real site. On a new hub
that permission is granted to the **Manager** access group and inherited by
**Administrator** below it; **Super Users** hold `core.admin` and are allowed
everything. So you keep browsing while visitors get the notice. The
administrator interface is not affected either way.

This step is optional, and it is worth the two clicks only if the hub is
already reachable from outside. Undo it in step 9.

### 2. Set the site identity

**Site > Global Configuration**, **Site** and **Server** tabs. See
[Global configuration](05-configuring/01-hub.md) for the whole screen.

The installer already asked for a site name, a time zone and an address, so
this is a check rather than a first entry. Four fields matter now:

- **Site Name** — used in page titles, in mail subjects, and by any component
  that asks the hub what it is called.
- **FQDN** — the hub's fully qualified domain name.
- **Server Time Zone**, on the **Server** tab. Cron recurrences and every
  displayed date are read against it.
- **Application Environment**, which decides how much detail an error page
  shows. Set it to **Production** before anyone outside sees the site.

Global Configuration writes plain PHP files under `app/config/`. If the save
reports that it could not write, fix the directory's permissions rather than
hunting for the setting elsewhere.

### 3. Make mail work

**Site > Global Configuration**, **Server** tab, **Mail Settings**.

Almost nothing on a hub notifies anyone until this is right: account
confirmation, password resets, group announcements, support tickets, every
digest. There is no test-mail button anywhere in the interface, so the only
way to know is to trigger something that sends — registering a throwaway
account is the usual test, which is why this comes before step 4.

**Mailer** offers three choices: **Sendmail**, **SMTP** and **Mandrill
(SMTP)**. Set the **Sendmail Path**, or the SMTP host, port, security and
credentials, to match the machine.

> **Warning:** Do not choose **Mandrill (SMTP)**. The option stores the value
> `mandrill`, but the transport builder only recognises `mandrill+smtp`, so
> the hub builds no transport and every send throws. Recorded in
> It is recorded with the project.
<!--include: core/libraries/Hubzero/Mail/Message.php:136-142-->

The value the installer writes into `app/config/mail.php` is `mail`, which is
not one of the three options the drop-down offers. It behaves as **Sendmail**,
as the code above shows, so the hub does send mail — but the field renders
without a matching selection, and the first time you save Global
Configuration it becomes a real **Sendmail**. Nothing breaks; do not be
alarmed by the change.

**From email** does more than name the sender. It is also the address the hub
notifies when an account is created — the new-account mail is addressed to the
same address it is sent from. Point it at a mailbox somebody reads, not at a
no-reply address.

> **Tip:** **DSN (overrides other mail settings)** on the same panel takes a
> single connection string and ignores the fields above it. Use it when the
> hub's mail relay needs something the individual fields cannot express.

### 4. Decide how people register

Two screens, both in the Members component. Read
[Registration](05-configuring/02-registration.md) for both in full.

**Users > Members**, then **Options** in the toolbar, holds the decisions:

| Option | Default | What the default means |
|---|---|---|
| **Allow User Registration** | Yes | Anyone can create an account. |
| **New User Account Activation** | Self | The member gets a link by mail and activates the account themselves. Nobody reviews it. |
| **New User Registration Group** | Registered | New accounts land in the Registered access group. |
| **Guest Access Group** | Public | Visitors who are not signed in count as Public. |
| **Notification Mail to Administrators** | Yes | Nothing here. See the note below. |
| **Send Password** | Yes | Nothing. No code reads it. |
| **Simple Registration** | No | Nothing. No code reads it. |

Three of those options do not do what the screen implies, and all three are
Recorded with the project:

- **Send Password** and **Simple Registration** are declared on the form and
  described in the help text, but nothing in the tree reads either value.
  Setting them changes nothing.
- **Notification Mail to Administrators** is read from the **User - Hubzero**
  plugin, not from the Members component, so the copy on this screen has no
  effect. The one that works is **Notification Mail to Administrator** under
  **Extensions > Plug-in Manager**, on the **User - Hubzero** plugin, and it
  also defaults to Yes. The mail it sends goes to the global **From email** —
  the same address it is sent from.

The default that surprises people is **New User Account Activation**. A new
hub is open, and confirming an email address is all it takes to get an
account — there is no approval queue. If your hub is for a named group of
people, set it to **Admin**, which adds a review step before the account
works, and set **Email On Account Activation** to **Yes** so the member is
told when you approve them. If the hub is closed entirely, set **Allow User
Registration** to **No** and create accounts yourself under **Users >
Members**.

**Users > Members > Registration** is the second screen: a grid of fields
against the four moments a member's details are collected, each cell
**Required**, **Optional**, **Hide** or **Read only**. Decide here whether you
are asking for an organisation, a phone number, a reason for joining. It is
easier to relax a required field later than to collect one you did not ask for
at the start — the **Update on Next Login** column exists for exactly that
repair, but it costs every existing member an interruption.

> **Note:** Nothing in the shipped site template links to the registration
> form. The template's **Register Link** option is read only by the older
> layout, and the current one renders no such link at all. Visitors reach the
> form through a menu item, so add one in step 7 if the hub takes public
> registrations.

### 5. Decide who can do what

The single most useful thing to know about this section is that *group* means
two unrelated things, and the screens for them sit next to each other on the
**Users** menu. [Users](06-users/README.md) explains the split; read that
before touching either screen.

A **hub group** is a community with pages, a forum, a wiki and an address at
`/groups/<alias>`. Members join it. See [Groups](06-users/05-groups.md).

An **access group** is a permission bucket with no pages and no address.
Nobody joins one; you put accounts in it. See
[Access Groups](06-users/06-accessgroups.md).

Eight access groups ship, nested:

| Group | Parent |
|---|---|
| **Public** | — |
| **Manager** | Public |
| **Administrator** | Manager |
| **Registered** | Public |
| **Author** | Registered |
| **Editor** | Author |
| **Publisher** | Editor |
| **Super Users** | Public |

The names carry no meaning of their own; what each may do is decided by the
permission rules set against it in Global Configuration's **Permissions** tab
and on each component's **Options**. The exceptions are **Super Users**, which
holds `core.admin` and is therefore allowed everything, and **Manager**, which
is where `core.login.admin` — the permission that lets an account into the
back end at all — is granted on a new hub.

Three viewing levels ship as well: **Public**, **Registered** and **Special**.
They answer "who may see this", and every content item carries one. See
[Access Levels](06-users/07-accesslevels.md).

For the first week:

1. Know where your own account sits. The one the installer created is in
   **Super Users**.
2. Put the people who will run the hub with you in **Manager** or
   **Administrator**, not in **Super Users**. Both reach the back end, and
   neither holds `core.admin`, so neither can open Global Configuration and
   rewrite the permission rules.
3. Leave the eight shipped groups in place. Deleting **Public** or
   **Registered** breaks the default viewing levels and the registration
   options. Add your own groups alongside them.

> **Warning:** The permission check honours a `root_user` key in
> `app/config/app.php`: an account whose username, or numeric id, matches it
> is treated as a Super User whatever its access groups say. It is a lock-out
> failsafe, and the installer does not write it — but installations carry it
> from older configurations. Look for it, and make sure the account it names
> is one you control.

### 6. Turn off what the hub does not need

**Extensions > Extension Manager**, **Hubzero Core** tab. See
[Extension Manager](10-extensions/04-extension-manager.md).

A hub ships with nearly every component enabled — around sixty of them, from
Answers to Wishlist — because the platform cannot know which ones a given hub
is for. Every enabled component adds an entry to the **Components** menu, and
most of them add something a visitor can reach. A hub that will never run
courses, sell anything, or publish a newsletter is easier to administer, and
easier to explain to its members, with those switched off.

To see the real state, filter the list by **Type** = Component and sort or
filter on **Status**; do not go by what a fresh database happens to contain,
since installing with the optional sample content switches several extensions
off that a plain install leaves on.

1. Filter to **Component**.
2. Tick the ones this hub will not use.
3. Select **Disable**.

Disabling a component removes it from the administrator menu as well as from
the site. Nothing is deleted, and **Enable** puts it back.

Then set the options of the ones you kept:
[Components](05-configuring/03-components.md) explains the **Options**
pop-up and its **Permissions** tab, and every parameter of every component is
listed in the generated
[configuration reference](../reference/configuration/README.md). Some
components do nothing useful until their options are filled in.

### 7. Give the site a front page and a menu

Two separate jobs, and the second one catches everybody.

#### The front page

The site's home page is whichever menu item is marked **Home**. On a plain
install that is a single item in **Main Menu**, of type **Featured Articles**.

> **Warning:** Nothing in the administrator interface can put an article on
> that page. The **Featured** field on an article writes to the article's own
> column, while the Featured Articles view reads a separate table that no
> administrator screen writes to. A hub left on the shipped home page has an
> empty front page for ever. See
> [Article Manager](08-content/articlemanager.md).

So the first-week job is to replace it:

1. Write the page you want as an article — **Content > Article Manager**,
   **New**. See [Content](08-content/README.md).
2. **Menus > Main Menu**, then **New**.
3. Set **Menu Item Type** to **Articles > Single Article** and pick the
   article.
4. Save it, then tick it in the list and select **Home**.

There must always be exactly one item marked **Home**; giving the flag to the
new item takes it from the old one.

#### The menu

A menu is not displayed anywhere until a **Menu** module is pointed at it and
given a template position. Creating the menu and creating the module that
shows it are two separate steps, and the shipped module is in the wrong place:

- The site templates in this tree render the main navigation from the
  `user3` position.
- The **Main Menu** module a plain install creates is assigned to
  `position-7`, which no template here declares or renders.

The result is a hub with a menu that appears nowhere. Installing the optional
sample content moves the module to `user3`; a plain install does not, so check
it yourself:

1. **Extensions > Module Manager**, open **Main Menu**.
2. Set **Position** to `user3`.
3. Confirm **Status** is **Published**, and **Save & Close**.

Then build the menu out. [Menus](07-menus.md) covers the Menu Manager, the
item types, and how to group items into sections; [Modules](10-extensions/01-modules.md)
covers the module side. The positions the site template offers are `footer`,
`banner`, `welcome`, `left`, `right`, `helppane`, `user3`, `notices`,
`search`, `breadcrumbs` and `endpage`.

### 8. Schedule the recurring jobs

Several features do nothing at all until a job runs: digests, group
announcements, membership expiry, search indexing, DOI registration, cache
cleanup. Read [Scheduled tasks](03-maintenance/05-cron.md) first — it
explains the tick, the `muse cron:jobs` command and the IP whitelist — and use
[Cron](09-components/12-cron.md) as the catalogue of what each job does.

Two things have to be true.

**The tick has to be installed.** Unix cron does not run the hub's jobs; it
calls the hub once a minute and the hub runs whatever is due. Until that entry
exists in the system crontab, every job in the list sits there with a next-run
time in the past.

**The jobs have to exist.** A new hub has three, and only one of them runs:

| Job | Event | State |
|---|---|---|
| **Group Announcements** | `groups` / `sendGroupAnnouncements`, every 5 minutes | Published |
| **Process Newsletter Mailings** | `newsletter` / `processMailings` | Unpublished |
| **Process Newsletter Opens & Click IP Addresses** | `newsletter` / `processIps` | Unpublished |

Everything else you create yourself, at **Components > Cron > New**. The
**Event** drop-down is built from the enabled cron plugins, so an event you
expect and cannot find usually means its plugin is disabled under
**Extensions > Plug-in Manager**. Add the jobs for the features you kept in
step 6 — there is no point scheduling a publications job on a hub that does
not publish.

> **Caution:** Leave **Run in a detached process** alone unless a job is a
> long archival batch. A detached job cannot build correct absolute URLs, so
> never enable it for anything that sends mail.

### 9. Bring the site back online

**Site > Global Configuration**, **Site** tab, **Offline Settings**. Set
**Site Offline** back to **No**.

Then sign out and look at the front page the way a visitor will: the home page
you built in step 7, the menu in a position the template renders, and — if the
hub takes registrations — a way to reach the registration form. Checking this
signed out matters, because signed in as a Super User you see the site whether
it is offline or not.

### Where to go next

- [Integrations](02-advancedsetup.md) — analytics, Google Drive, the rich
  text editor, and CAPTCHA.
- [Configuring](05-configuring/README.md) — the rest of the global
  configuration, and the per-extension options.
- [Daily maintenance](03-maintenance/README.md) — the work the hub needs
  once people are using it.
- [Spam](11-spam.md) — worth reading before the hub is public, not after.
## How a hub fits together

The mental model the rest of this book assumes. Every other chapter tells you
which screen to open and which field to set; none of them tells you what the
hub is doing underneath, so the first time a change you made in one screen
appears — or fails to appear — somewhere else entirely, there is nothing to
reason from. This chapter supplies that. It is short, and it is worth reading
once before the screen-by-screen chapters rather than after.

It is for the person who has just been given a hub to run and has no
background in the software. If you write code for a hub, read
[Application structure](../developers/03-foundation/01-structure.md)
instead; it covers the same ground with the detail a developer needs.

### The two halves

A hub is one application with two faces.

The **front end** is the site people visit: the home page, the groups, the
resources, the search box. The **administrator interface**, or back end, is
at `/administrator` and is where you configure all of that.

They are not two programs. They are the same installation, running the same
components against the same database, with one difference: the first path
segment of the URL. `/administrator/…` selects the administrator *client*,
`/api/…` selects the REST API, and anything else is the site. The choice is
made by
[`ClientDetector`](../../core/libraries/Hubzero/Base/ClientDetector.php)
on every request, and it decides which set of controllers, views, template
and configuration the request runs against — `admin/` instead of `site/`
inside each component.

That is why a component you disable vanishes from both the **Components**
menu and the public site at once, and why there is no separate back-end
address, host or port to remember.

[The administrator interface](#the-administrator-interface) covers reaching it, who is
allowed in, and what each of its menus holds. Read that chapter for the back
end itself; this one is about what the back end is editing.

### How a page gets built

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
[The first week with a new hub](#the-first-week-with-a-new-hub) — a menu that exists, in a
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

[Menus](07-menus.md) covers the menu side and
[Modules](10-extensions/01-modules.md) the module side; the point here is
only that they are two halves of one decision.

> **Note:** Of everything the template asks for, the modules are rendered
> first — before even the page's `<head>` is written. That is deliberate: it
> lets a module add a stylesheet or a script and still have it appear in the
> head of the finished page. The component's output was produced a step
> earlier still, before the template was opened at all.

#### A worked page

Take the front page a new hub gets in step 7 of
[The first week with a new hub](#the-first-week-with-a-new-hub): an article, shown by a menu
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

### Where things live

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
[Daily maintenance](03-maintenance/README.md) covers the rest of the
running-hub routine.

### The four kinds of extension

Everything the hub puts on a page comes from an extension, and there are four
kinds. [Extensions](10-extensions/README.md) explains each one properly,
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

### The table prefix

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
> [Global configuration](05-configuring/01-hub.md).

### What you can break, and what you cannot

Most of what a new manager is nervous about is reversible, and a few things
that look harmless are not. Worth knowing which is which before you start
clicking.

#### Hard to break

- **Disabling and unpublishing.** Disabling a component or unpublishing a
  module deletes nothing. **Enable** and **Publish** put it back exactly as
  it was, options and all.
- **Uninstalling a shipped extension.** You cannot. There is no **Uninstall**
  button in the Extension Manager — it is commented out of the view. See
  [Extension Manager](10-extensions/04-extension-manager.md).
- **Changing an item's state.** Publishing, unpublishing and the rest write a
  number into a column; the row stays where it is. Read
  [States, deleting and check-out](08-content/states.md) before assuming
  which number a given label means, because the labels and the stored values
  do not line up as neatly as the screens suggest.
- **A record that will not open because it is "checked out".** Nothing is
  wrong with it. **Site → Maintenance → Global Check-in** releases every
  locked record on the hub.
- **Moving modules between positions.** Free, and the fastest way to learn
  what a template's positions actually are: put a module somewhere and look.
- **Categories.** Creating them costs nothing, and they are scoped per
  component, so a category made for articles cannot disturb the knowledge
  base. See [Categories](08-content/categories.md).

#### Easy to break

- **Editing anything under `core/`.** It works until the next upgrade, then
  vanishes without warning. Put the change under `app/` instead.
- **Changing the table prefix**, as above.
- **Deleting the shipped access groups.** **Public** and **Registered** are
  referenced by the default viewing levels and the registration options.
  Delete your own groups freely; leave the eight shipped ones in place. See
  [Access groups](06-users/06-accessgroups.md).
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

### Where to go next

- [The administrator interface](#the-administrator-interface) — the back end itself, menu
  by menu.
- [The first week with a new hub](#the-first-week-with-a-new-hub) — the ordered path through the
  decisions a new hub needs.
- [Extensions](10-extensions/README.md) — the four kinds, in full.
- [Application structure](../developers/03-foundation/01-structure.md) —
  the same picture with the code paths, for readers who want the detail.
## The administrator interface

The administrator interface, or back end, is where you set up, configure and
maintain a hub. It runs on the same site as the public front end but under a
separate URL, uses a different template, and is closed to anyone without an
explicit permission to enter it.

### Reaching it

Add `/administrator` to the hub's address. If the hub is at
`https://example.com`, the back end is at:

```
https://example.com/administrator
```

That first path segment is what selects the administrator application; there
is no separate hostname or port. Everything after it is an ordinary
`index.php?option=com_…` request handled by the same components as the front
end, only with their `admin/` controllers and views instead of their `site/`
ones.

### Logging in

Every administrator URL is rewritten to the login component until you are both
logged in and authorised, so it does not matter which back-end page you ask
for first — you always land on the login screen:

<!--include: core/bootstrap/Administrator/routes.php:88-105-->

The check is the `core.login.admin` permission, not simply "is logged in". A
member who is signed in to the front end and browses to `/administrator` is
still sent to the login screen if their access groups do not carry that
permission. On a fresh install `core.login.admin` is granted to the
**Manager** group and inherited by **Administrator** beneath it; **Super
Users** hold `core.admin`, which grants everything. See
[Access groups](06-users/06-accessgroups.md) for how to change that.

The login screen itself is `com_login`, rendered through the admin template's
`login.php` layout with the **Login Form** module (`mod_adminlogin`) in it.
What you see depends on which authentication plugins the hub has enabled with
their **Admin login** option turned on:

- With no such plugin, you get a plain form: **Username**, **Password**, and a
  **Log in** button.
- With one or more, you get a **Sign in with …** button for each, plus a link
  back to the plain form for hub-local accounts.

A successful login lands you on the **Control Panel**.

> **Note:** Failing to log in here is not always a wrong password. If the
> account is valid but lacks `core.login.admin`, the login is refused in the
> same way. Check the member's access groups before resetting anything.

### The layout

Every back-end page is built from the same pieces, in this order down the
page:

| Region | What is in it |
|---|---|
| Header | The hub's name, linking to the front end, and the **Log out** link on the right |
| Main navigation | The top-level menus, rendered by the admin menu module in the `menu` position |
| Toolbar box | The page title on the left and its action buttons — **New**, **Save**, **Save & Close**, **Close**, **Options**, **Help** — on the right |
| Sub-navigation | The current component's own screens, when it registers any |
| Content | The component itself |

The toolbar is where the verbs live. A list screen puts **New**, **Edit**,
**Delete** and the status buttons there; an edit screen replaces them with
**Save**, **Save & Close** and **Close**. A component's **Options** button
opens its configuration in a modal, and the **Permissions** tab inside that
modal is where you say which access groups may use it.

### The Control Panel

The Control Panel is the component `com_cpanel`, and it is the default screen
for any administrator URL that names no component. It has no content of its
own: it renders every administrator module published in the `cpanel` position,
one collapsible panel per module. What your dashboard shows therefore depends
entirely on which modules the hub publishes there. [Daily
maintenance](03-maintenance/README.md) describes the panels a working hub
usually adds.

### The menus

The main navigation is assembled by the admin menu module. Most of it is
hardcoded, and each entry is shown only if you hold the permission it needs,
so a Manager sees fewer entries than a Super User.

| Menu | Contains |
|---|---|
| **Site** | **Control Panel**, **Global Configuration**, a **Maintenance** submenu (**Global Check-in**, **Clear Cache**, **Purge Expired Cache**, **LDAP**, **Geo DB**, **APC**, **Routes**), **System Information**, and **Logout** |
| **Users** | **Members**, **Groups**, **Access Groups**, **Access Levels**, **User Notes** and their categories, and **Mass Mail Users** |
| **Menus** | **Menu Manager**, then one entry per menu defined on the hub |
| **Content** | **Article Manager**, **Category Manager**, and **Media Manager** |
| **Components** | One entry per installed, enabled component, with a submenu where the component defines one |
| **Extensions** | **Extension Manager**, **Module Manager**, **Plug-in Manager**, **Template Manager**, **Language Manager** |
| **Help** | **Help Articles** and links out to hubzero.org; the menu module's **Help Menu** option, on by default, hides it |

The **Components** menu is the one that grows. Unlike the rest, it is built
from the database — the administrator menu rows installed with each component
— rather than from the module's own code, which is why installing a component
adds it to that menu without any further step. Members, Groups and System are
deliberately left out of it because they already appear under **Site** and
**Users**.

> **Note:** **Global Configuration** and **System Information** appear only
> for accounts holding `core.admin`. The **Maintenance** submenu needs
> `core.admin` on the check-in component or `core.manage` on the cache
> component; without either, the whole submenu is left out.

### Logging out

Click **Log out** in the top-right corner of the header, or use **Site →
Logout**. Either returns you to the login screen.

While you have an item open for editing, the whole main navigation and the
**Log out** link are disabled: the record is checked out to you, and the
interface hides the links so you cannot navigate away and leave it locked.
Finish with **Save & Close** or **Close** to release it, and the links come
back.

> **Tip:** If a record stays locked — a browser crash, a lost session — clear
> it with **Site → Maintenance → Global Check-in**, which releases every
> record checked out across the hub.

### Where to go next

- [Advanced setup](02-advancedsetup.md) — the site template, menus and
  modules.
- [Configuring](05-configuring/README.md) — the global configuration and the
  per-extension options.
- [Users](06-users/README.md) — members, access groups, and access levels.
- [Daily maintenance](03-maintenance/README.md) — the work a running hub
  needs every day.

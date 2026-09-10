<!--
status: rewritten
reviewed-against: 2.4-main @ 9924bea2ec
reviewed: 2026-09-10
screenshots: none
-->
# The first week with a new hub

What to do first with a hub that has just been installed, in the order that
works. The software is running, the site answers, and you can sign in to the
back end — and now several dozen screens are asking for decisions, some of them
painful to change once members have accounts and content. This chapter puts
those decisions in order, links to the chapter that covers each one in full,
and says which shipped defaults surprise people.

It is for the person who has just been handed a new hub. It does not repeat
what the linked chapters say; read it beside them. If you are taking over a
hub that already has members, this is the wrong chapter — go to
[Daily maintenance](../03-maintenance/README.md) and
[Users](../06-users/README.md) instead.

Before anything else, read [The administrator interface](administrator.md).
Everything below happens in the back end, and every step names a menu path
through it.

## The order

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

## 1. Take the site offline

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

## 2. Set the site identity

**Site > Global Configuration**, **Site** and **Server** tabs. See
[Global configuration](../05-configuring/01-hub.md) for the whole screen.

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

## 3. Make mail work

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

## 4. Decide how people register

Two screens, both in the Members component. Read
[Registration](../05-configuring/02-registration.md) for both in full.

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

## 5. Decide who can do what

The single most useful thing to know about this section is that *group* means
two unrelated things, and the screens for them sit next to each other on the
**Users** menu. [Users](../06-users/README.md) explains the split; read that
before touching either screen.

A **hub group** is a community with pages, a forum, a wiki and an address at
`/groups/<alias>`. Members join it. See [Groups](../06-users/05-groups.md).

An **access group** is a permission bucket with no pages and no address.
Nobody joins one; you put accounts in it. See
[Access Groups](../06-users/06-accessgroups.md).

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
[Access Levels](../06-users/07-accesslevels.md).

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

## 6. Turn off what the hub does not need

**Extensions > Extension Manager**, **Hubzero Core** tab. See
[Extension Manager](../10-extensions/04-extension-manager.md).

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
[Components](../05-configuring/03-components.md) explains the **Options**
pop-up and its **Permissions** tab, and every parameter of every component is
listed in the generated
[configuration reference](../../reference/configuration/README.md). Some
components do nothing useful until their options are filled in.

## 7. Give the site a front page and a menu

Two separate jobs, and the second one catches everybody.

### The front page

The site's home page is whichever menu item is marked **Home**. On a plain
install that is a single item in **Main Menu**, of type **Featured Articles**.

> **Warning:** Nothing in the administrator interface can put an article on
> that page. The **Featured** field on an article writes to the article's own
> column, while the Featured Articles view reads a separate table that no
> administrator screen writes to. A hub left on the shipped home page has an
> empty front page for ever. See
> [Article Manager](../08-content/articlemanager.md).

So the first-week job is to replace it:

1. Write the page you want as an article — **Content > Article Manager**,
   **New**. See [Content](../08-content/README.md).
2. **Menus > Main Menu**, then **New**.
3. Set **Menu Item Type** to **Articles > Single Article** and pick the
   article.
4. Save it, then tick it in the list and select **Home**.

There must always be exactly one item marked **Home**; giving the flag to the
new item takes it from the old one.

### The menu

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

Then build the menu out. [Menus](../07-menus.md) covers the Menu Manager, the
item types, and how to group items into sections; [Modules](../10-extensions/01-modules.md)
covers the module side. The positions the site template offers are `footer`,
`banner`, `welcome`, `left`, `right`, `helppane`, `user3`, `notices`,
`search`, `breadcrumbs` and `endpage`.

## 8. Schedule the recurring jobs

Several features do nothing at all until a job runs: digests, group
announcements, membership expiry, search indexing, DOI registration, cache
cleanup. Read [Scheduled tasks](../03-maintenance/05-cron.md) first — it
explains the tick, the `muse cron:jobs` command and the IP whitelist — and use
[Cron](../09-components/12-cron.md) as the catalogue of what each job does.

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

## 9. Bring the site back online

**Site > Global Configuration**, **Site** tab, **Offline Settings**. Set
**Site Offline** back to **No**.

Then sign out and look at the front page the way a visitor will: the home page
you built in step 7, the menu in a position the template renders, and — if the
hub takes registrations — a way to reach the registration form. Checking this
signed out matters, because signed in as a Super User you see the site whether
it is offline or not.

## Where to go next

- [Integrations](../02-advancedsetup.md) — analytics, Google Drive, the rich
  text editor, and CAPTCHA.
- [Configuring](../05-configuring/README.md) — the rest of the global
  configuration, and the per-extension options.
- [Daily maintenance](../03-maintenance/README.md) — the work the hub needs
  once people are using it.
- [Spam](../11-spam.md) — worth reading before the hub is public, not after.

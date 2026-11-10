<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
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
[Daily maintenance](../03-maintenance/index.md) and
[Users](../06-users/index.md) instead.

One example runs through the chapter, because the right answer to most of
these questions depends on what the hub is for. Take a hub set up for a
soil-carbon project shared by three universities: about forty named
collaborators today, working on data they are not ready to show anyone, and a
public data release in a year and a half. That hub wants accounts somebody
approves rather than accounts anyone can create, mail that reaches people at
three different institutions, and — well before the release — a decision
about whether published material is reviewed before it appears. A hub meant
as a public front door for a lab answers several of these the other way, and
each step says which.

Before anything else, read [The administrator interface](01-administrator-interface.md).
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

The order is not arbitrary. Each step either supplies something the next one
needs, or closes a door the next one would leave open.

- **Offline first** (step 1), because every step below changes the site under
  anyone who is already looking at it.
- **Identity before mail** (steps 2 and 3). The confirmation mail signs itself
  `<Site Name> Administrator` and its subject begins with the site name, so
  the name you set in step 2 is how the hub introduces itself to a stranger in
  step 3.
- **Mail before registration** (steps 3 and 4). This is the one that costs
  people a weekend. **New User Account Activation** ships set to **Self**: the
  hub mails the new member a link, and the account does nothing until they
  click it. On a hub whose mail does not work the account is created anyway —
  the row is written before the mail is attempted — and then sits unactivated
  for ever. The member never gets the link, cannot sign in, and cannot tell
  you, because the hub's contact routes are mail too. If the send fails
  outright the form says so; if the machine accepts the mail and quietly drops
  it, nothing says anything at all. Prove mail works before you let anybody
  register.
- **Registration before permissions** (steps 4 and 5), because deciding what
  **New User Registration Group** should be is really a question about what
  that group is allowed to do.
- **Permissions before pruning** (steps 5 and 6), so that when you decide a
  component stays, you already know who will be allowed to use it.
- **Content after the pruning** (steps 7 and 8), because the front page and the
  scheduled jobs depend on which components survived step 6. There is no point
  building a menu item for a component you are about to disable, or scheduling
  a job for one.
- **Online last** (step 9), so the first thing a visitor sees is the finished
  arrangement rather than a half-configured one.

Steps 3, 4 and 5 are the ones that are hard to undo — not because the screens
resist it, but because by the time the mistake shows, other people have
accounts.

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

<!--include: core/bootstrap/Site/Providers/DocumentServiceProvider.php:79-83-->

Anyone holding `core.login.offline` still sees the real site. On a new hub
that permission is granted to the **Manager** access group and inherited by
**Administrator** below it; **Super Users** hold `core.admin` and are allowed
everything. So you keep browsing while visitors get the notice. The
administrator interface is not affected either way.

The `503` is the right status to send, and worth knowing about: it tells a
search engine that the page is temporarily away and to come back, rather than
that it has gone. A maintenance window does not cost the hub its listings.

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
- **Application Environment**, on the **Site** tab. Set it to **Production**
  on the hub people use, and to anything else on a copy of it.

Two of those deserve more than a glance.

**Site Name** is not only a heading. Registration mail is sent from
`<Site Name> Administrator`, and its subject line begins with the same name,
so this is what a stranger sees in their inbox before they see the hub. Use
the name the project uses in public, not the internal one. Changing it later
is one field and breaks nothing, but the mail already sent keeps the old name.

**Application Environment** is not the error-detail switch, whatever the name
suggests. That one is **Debug System**, on the **System** tab, and it ships
**No**. What the environment setting does is gate the things that should only
happen on the hub people use: the **Google Analytics** and **Google Gtag**
modules render nothing unless the value starts with `production`, the
**Application Environment** module shows a banner naming the environment when
it does not, and super group repository provisioning runs only on production
hubs. So the setting matters most on the *copies*. A staging clone left on
**Production** reports its own traffic into the live hub's analytics, and a
live hub left on **Development** records none of its own.

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

<!--include: core/libraries/Hubzero/Mail/Message.php:127-131-->

The value the installer writes into `app/config/mail.php` is `mail`, which is
not one of the three options the drop-down offers. It behaves as **Sendmail**,
as the code above shows, so the hub does send mail — but the field renders
without a matching selection, and the first time you save Global
Configuration it becomes a real **Sendmail**. Nothing breaks; do not be
alarmed by the change.

**From email** does more than name the sender. It is also the address the hub
notifies when an account is created — the new-account mail is addressed to the
same address it is sent from. Point it at a mailbox somebody reads, not at a
no-reply address. On the example hub that is a shared address the staff at all
three institutions can open, not one person's inbox: members see it as
`<Site Name> Administrator` and reply to it whether you meant them to or not,
and a hub whose only administrative address belongs to someone who has moved
on is a slow, expensive problem to unpick.

> **Tip:** **DSN (overrides other mail settings)** on the same panel takes a
> single connection string and ignores the fields above it. Use it when the
> hub's mail relay needs something the individual fields cannot express.

## 4. Decide how people register

Two screens, both in the Members component. Read
[Registration](../05-configuring/02-registration.md) for both in full.

**Users > Members**, then **Options** in the toolbar, **Component** tab, holds
the decisions:

| Option | Default | What the default means |
|---|---|---|
| **Allow User Registration** | Yes | Anyone can create an account. |
| **New User Account Activation** | Self | The member gets a link by mail and activates the account themselves. Nobody reviews it. |
| **New User Registration Group** | Registered | New accounts land in the Registered access group. |
| **Guest Access Group** | Public | Visitors who are not signed in count as Public. |
| **Notification Mail to Administrators** | Yes | Nothing here. See the note below. |
| **Send Password** | Yes | Nothing. No code reads it. |
| **Simple Registration** | No | Nothing. No code reads it. |

Three of those options do not do what the screen implies, and all three are known
defects:

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

The example hub takes **Admin**. Forty named collaborators is not a number
that needs a self-service front door, and an approval step is the only thing
between the hub and whoever finds the form. A hub that wants to be a public
front door leaves it on **Self** and spends the effort on
[Spam](../11-spam.md) instead. Either way, decide it now: switching to **Admin**
later leaves the accounts already created untouched, so the decision only ever
applies going forward.

One more decision sits on a different tab of the same **Options** screen. The
**Defaults** tab holds **Default Privacy**, which sets the viewing level
stored on every new profile: **Public**, meaning anyone who finds the address
can read the member's profile page, or **Private**, meaning nobody but the
member can. It applies at the moment an account is created, so it decides what
your members' pages look like to the outside without their ever choosing it.

> **Warning:** The manifest declares **Default Privacy** as **Private**, but
> the shipped install data stores it as **Public**, and both places in
> `com_members/site/controllers/register.php` that apply it fall back to
> **Public** as well. So on a hub installed from that data, and on a hub whose
> Members options have never been saved, every account is created public
> whatever the manifest says. This is a known defect.
> Open the tab, read the value, and save it deliberately — see
> [Check the default, do not trust it](#check-the-default-do-not-trust-it).

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
**Users** menu. [Users](../06-users/index.md) explains the split; read that
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

This step is housekeeping rather than configuration, and a hub survives
skipping it. Do it anyway, because both halves get more expensive later. Every
component left on is one whose options you are answerable for and whose
screens your members can find and start using; and a component switched off
after they have started takes their content out of reach with it. Decided now,
while nothing has been created, it costs nothing.

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
[configuration reference](../../reference/configuration/index.md). Some
components do nothing useful until their options are filled in.

### Check the default, do not trust it

A component's default comes from two places that do not always agree. The
manifest — the component's own `config/config.xml` — declares a default for
every field, and that is the value the generated
[configuration reference](../../reference/configuration/index.md) prints. The
shipped install data, `core/bootstrap/Install/sql/mysql/data.sql`, separately
seeds a row of stored parameters for each component. **The stored row wins**,
both for what the hub does and for what the **Options** screen shows you: the
form is bound to the stored parameters and falls back to the manifest only for
keys the row does not mention.

Two of those disagreements change what a hub does on its first day.

> **Warning:** Publications ship auto-approving. **Components >
> Publications**, **Options**, **Curation** tab, **Auto-approve** is declared
> `default="0"` — No — in the manifest, and the reference prints No, but the
> shipped row stores `autoapprove=1`. When it is 1 a submitted version goes
> live the moment it is submitted, with no curator involved, unless the author
> ticks the review box themselves — and where the type requires a DOI, that
> DOI is registered as public rather than reserved. A hub that believes it is
> curating its publications is not. See
> [Publications](../09-components/27-publications.md).

That one has a second switch, which is easy to miss. Each publication type
carries its own **Auto Approve?** under **Components > Publications > Master
Types**, and either switch being on is enough to publish without review. If
the example hub's data release is meant to pass a curator, both have to say
no.

The other disagreement is **Default Privacy**, in
[step 4](#4-decide-how-people-register) above.

The habit worth taking from this is small. When a setting matters, open the
screen and read the value rather than reading a default anywhere — including
in this book. A reference tells you what a field means and what its values
are; only the screen tells you what your hub has.

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
   **New**. See [Content](../08-content/index.md).
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
- [Configuring](../05-configuring/index.md) — the rest of the global
  configuration, and the per-extension options.
- [Daily maintenance](../03-maintenance/index.md) — the work the hub needs
  once people are using it.
- [Spam](../11-spam.md) — worth reading before the hub is public, not after.

<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: stale
source: https://help.hubzero.org/documentation/240/managers/components/tools
source-id: 3404
modified: 2016-08-26
imported: 2026-09-09
-->
# Tools

The Tools component has two halves. The half a tool contributor sees lives on
the site, at `/tools/pipeline`, and is where a tool moves from registration to
publication. The half described here lives in the administrator interface and
manages the machinery around it: execution hosts, session zones, running
sessions, per-user session limits, and file handlers.

## Half a system

This is the part of the chapter to read before any of the rest.

The Tools component is the CMS half of a system whose other half is not in
this repository. What lives here is a catalogue of tool contributions, a set
of records describing execution hosts and zones, and the screens that read
and write them. What runs a tool — the middleware that starts a session,
allocates it to a host, keeps a container alive and streams its display back
to a browser — is separate software, installed separately, with its own
database.

That has two consequences for a manager.

**Most of these screens are a view onto a database this repository does not
create.** Hosts, provisions, host status, container statuses and running
sessions are read from the middleware database, configured under **Options →
Middleware** as **Middleware DB Host**, **Middleware Database**, **Middleware
DB Username** and **Middleware DB Password**. The shipped install leaves
every one of those blank. Until a middleware installation exists and those
credentials point at it, the Hosts, Sessions and status screens are empty or
error, and there is nothing you can do about that from inside the CMS.

**Several statements in this chapter could not be verified against this
repository, and are marked where they appear.** The behaviour of the
middleware itself — what a provisions bit means to the scheduler, what
happens on the host when a session is terminated, how a zone's VNC proxy is
used, what provisions an `SSH Key Path` — is decided by code that is not
here. This chapter describes what the CMS screens store and send. It does
not describe what the other half does with it, because that could not be
checked.

> **Note:** **Middleware** on the Options screen is declared **OFF** in the
> component manifest and set to **ON** by the shipped install row, and the
> stored row is what runs. So a hub installed from this release says the
> middleware is on while holding no credentials for it. The setting is not a
> statement of fact about your hub; treat it as a switch you have not
> configured yet.

## The tool pipeline lives elsewhere

For the pipeline itself — the nine tool states, the administrator controls,
the install and publish steps, and the group membership those controls
require — see [Tools](../03-maintenance/02-tools.md) in the maintenance
section. That chapter is the one to read before touching a tool
contribution.

> **Warning:** The pipeline's own controls are granted by membership of the
> group named in the **Admin Group** option, which the shipped install sets
> to `apps`. The install creates no groups at all, so that group does not
> exist until somebody makes it — and while the option names a group, a Super
> User who is *not* in it has no pipeline controls either. The code says so
> in as many words: "if no admin group is defined, allow superadmin to act as
> admin, otherwise superadmins can only act if they are also a member of the
> component admin group"
> ([`pipeline.php:2523`](../../../core/components/com_tools/site/controllers/pipeline.php)).
> Create the `apps` group and put yourself in it, or clear the option, before
> you try to move a contribution along.

## Where to find it

Go to **Components → Tools**. The submenu holds:

| Screen | Shown |
|---|---|
| **Pipeline** | always |
| **Hosts** | always |
| **Host Types** | always |
| **Zones** | only when the **Zones** option is on |
| **Sessions** | always |
| **User Preferences** | always |
| **File Handlers** | always |
| **Windows** | only when the **Access Key ID** option is set |

Every screen needs `core.manage` on `com_tools`. That is separate from the
pipeline's own controls, which are granted by membership of the group named
in the **Admin Group** option.

## Pipeline

A list of every tool contribution: **ID**, **Name**, **Title**, **State**,
**Registered**, **Changed**, and **Versions**. Above it are a **Search** box
and a **State** drop-down. The toolbar carries only **Options** and **Help** —
tools are not created or deleted here.

Clicking a tool's name or title opens a small edit form carrying only **Title**,
**Ticket ID**, and **State**. **State** offers all ten values — Unpublished,
Registered, Created, Uploaded, Installed, Updated, Approved, Published,
Retired, Abandoned. Setting it here writes the state
directly and skips everything the pipeline would otherwise do, so use the
site-side **Flip Status** control instead unless you are repairing a stuck
contribution.

**Options** in the toolbar opens the component's configuration; the full
parameter list is in the
[generated reference](../../reference/configuration/components/tools.md).

### Versions

The number in the **Versions** column links to the tool's version list —
**ID**, **Name**, **Version**, **Revision**, **State**, and, when DOI
registration is configured, **DOI**.

A version's edit form has a **Details** tab with **Command**, **Timeout**,
**Required Host**, **Middleware** and **Parameters**, a DOI panel, and a
**Zones** tab listing the zones the version may run in.

The **DOI** column appears only when the **Enable DOI service?** option is on.

> **Note:** The component has a `batchdoi` task that registers DOIs for tool
> versions that lack one, but no screen links to it. Reach it by hand at
> `index.php?option=com_tools&controller=pipeline&task=batchdoi&limit=25`.
> It processes `limit` records — two by default — and prints its results as
> plain text.

## Hosts

A host is a machine the middleware may start sessions on. You do not add one
here because you have bought a server; you add one here because the
middleware already knows about it and the CMS needs a matching record. Which
way round the two are meant to be kept in step could not be determined from
this repository.

The execution hosts that run tool sessions. Columns: **Name**, **Service
Host**, **Provisions**, **Status**, **Uses**, **Zone**, and **Broken
Containers**.

A host's edit form asks for **Name** (a valid hostname), **Service Host**,
the **Types** it provides, and its **Zone**. The right-hand side shows the
host's live status, its **Tool Sessions**, and its **Container Statuses**.

The **Provisions** cell lists every host type; the ones the host provides are
bold. Each is a link that flips that capability bit on the host, so you can
take one capability away without touching the rest. The **Status** cell links
to a live status page for the host.

> **Note:** Hosts, provisions, status and container information live in the
> middleware database, not the CMS database. When the middleware is
> unreachable these screens are empty or error. What the middleware does
> with a changed provision bit, and how quickly, could not be verified here.

> **Warning:** The provisions toggle is a plain GET link with no CSRF token
> and no permission check beyond `core.manage`. Treat a link to it in an
> email the way you would treat any other unguarded admin action.

## Host Types

A host type is a label two sides agree on: a host says it provides type *n*,
a tool version says it requires type *n*, and the middleware matches them.
The meanings are entirely a local convention — nothing in this repository
defines what any bit means — so a hub inherits whatever its middleware
installation was set up with.

The capability bits a host advertises and a tool requires. Columns:
**Name**, **Bit#**, **Description**, and **References** — the number of hosts
using the type.

An entry is a **Name**, a **Value** (the bit number), and a **Description**.
A tool's **Required Host** field is matched against these.

## Zones

Zones group hosts into pools — a local cluster, a remote site — so a tool
version can be pinned to one. Only a hub whose middleware spans more than
one site needs them.

The screen appears only when the **Zones** option is turned on. It ships
off, so on a stock hub the submenu entry is absent. Turning it on adds the
screen; whether the middleware honours zone assignment, and how, could not
be verified here.

Columns: **Zone**, **Type**, **State**, **Default**, **Master**, **SSH Key
Path**, and **Locations**. Type is **Local** or **Remote**; state is **up**
or **down**; one zone is the **Default** and the toolbar's **Make default**
sets it.

A zone's edit form has a **Details** tab and a **Locations** tab. Details
takes the **Zone** name (letters, numbers, dashes and underscores), a
**Title**, a **Description**, a **Master**, a **Type**, and a **State**, plus
an **Image** panel and a **Parameters** fieldset holding the VNC and
websocket proxy settings:

- **Websocket VNC Proxy Server Enabled**, **Websocket VNC Proxy Server**,
  **Secure Websocket VNC Proxy Server**
- **VNC Proxy Server Configuration Enabled**, **VNC Proxy Server**,
  **Secure VNC Proxy Server**

**Locations** are IP ranges — **IP from**, **IP to**, **Continent**,
**Country**, **Region**, **City** — used to send a visitor to the nearest
zone. A zone must be saved before locations or an image can be added.

> **Note:** **SSH Key Path** is a column on the list but has no field on the
> form. Set it in the database, or through whatever provisions the zone.

## Sessions

A running tool session holds a container on a host, so the two questions a
manager has are *what is running now* and *how many may one person run at
once*. **Active** answers the first, **Session classes** the second.

Two screens, reached from the sub-navigation: **Active** and **Session
classes**.

### Active

Every tool session currently running. Columns: **Session**, **Owner**,
**Viewer**, **Started**, **Last accessed**, **Tool**, **Exec host**, and
**Stop**. Drop-downs filter by **Tool**, **Execution Host**, and **Viewer**.

The **Stop** cell terminates one session. Ticking rows and pressing
**Delete** terminates several. Terminating is immediate and the user is not
warned.

> **Warning:** Stopping a session takes a running program away from someone
> who is using it. Whether their unsaved work survives depends on the tool
> and on the middleware, and could not be determined from this repository —
> assume it does not. Stop a session when a host has to come down or a
> session is stuck, not to tidy the list.

### Session classes

A session class is a named session allowance. Columns: **ID**, **Alias**, and
**Jobs Allowed**.

The install creates one class, `default`, allowing **3** concurrent sessions.
That is the number every user gets unless something overrides it.

A class's edit form has:

- **Alias** — must be unique, and cannot be `custom`, which is reserved.
- **Jobs Allowed** — the number of concurrent sessions.
- **User Access Groups** — the access groups this class applies to. A user
  added to one of these groups is given this class automatically.

Deleting a class moves everyone in it back to the `default` class. The
`default` class itself cannot be deleted.

## User Preferences

This is how you give one person more sessions than everyone else: a course
instructor who needs several tools open at once, or a developer testing a
contribution. For a whole class of people, add a session class and attach an
access group to it instead — that scales, and this does not.

The per-user override of the session allowance. This is the screen the old
version of this page described.

![The User Preferences list](../media/tools-tools-userpreferences.png)

The list shows **User ID**, **Username**, **Name**, **Session class**, and
**Sessions**, all sortable. Above it, a **Search by** drop-down (**Username**
or **Name**) with a search box and **Go**, and a **- Session Class -** filter.

Only users with an explicit entry appear. Everyone else runs on the class
their access groups give them, or on `default`.

To change a user's limit:

1. Go to **Components → Tools → User Preferences**.
2. If the user is listed, tick the row and press **Edit**. If not, press
   **New** and find them with the **User** field — search by name, or type a
   username or user ID.
3. Set **Session class**. Picking a named class fills **Sessions** from that
   class and makes it read-only. Picking **custom** leaves **Sessions**
   editable, so you can give one person a number no class offers.
4. Fill in **Sessions** if you chose **custom**. It must be numeric.
5. **Preferences** takes free-form extra settings for the session.
6. Press **Save & Close**.

Ticking rows and pressing **Default** in the toolbar puts those users back on
the `default` class.

> **Warning:** The **Default** action fails with *Could not find class with
> name 'default'!* if the `default` session class has been renamed or
> deleted. Do not rename it.

> **Note:** The screenshot above predates two changes: the **Search by** and
> **for** labels beside the filter, which were missing strings, and the
> **Zones** and **Windows** submenu entries, which appear when those options
> are on.

## File Handlers

A file handler is the "open with" list: a user looking at a `.dat` file in
their storage is offered the tool you name here. Worth setting up on a hub
whose users work with a handful of well-known file types, and not worth it
otherwise.

A file handler tells the hub which tool opens a given file from a user's
storage. Columns: **Tool alias**, **Prompt**, and **Rules**.

A handler is a **Tool alias** chosen from the installed tools, a **Prompt**
shown to the user, and a list of rules. Each rule is an **Extension** and a
**Quantity** — how many files of that extension the tool accepts. **Add
rule** and **Delete rule** build the list.

If the drop-down says *No tools installed*, no tool has reached the
**Installed** state yet.

## Windows

Present only when the component's **Access Key ID** option holds an Amazon
Web Services key. The AWS side of this — what the instances are, how they
are built and what the CMS expects to find running on them — is outside this
repository and could not be verified. It manages Windows tool instances on AWS: **ID**, **Name**,
**Title**, **UUID**, in-use and available session counts, and **State**, with
per-instance session and usage views and a **Terminate** action.

Leave the option empty on a hub with no Windows tools and the screen never
appears.

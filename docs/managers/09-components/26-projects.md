<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/projects
source-id: 3385
modified: 2016-07-12
imported: 2026-09-09
-->
# Projects

A project is a private workspace for a small team: a shared file area, plus
whatever other features the hub enables for it — notes, a to-do list,
publications, databases, activity. Members create and run their own projects
on the front end. The file area is meant to be versioned, and in this
release it is not: see [Version tracking is
off](#version-tracking-is-off) before you tell anyone their files are safe. In the administrator interface you set the
policy every project follows, and you intervene in individual projects when
something needs an administrator: a quota raised, a project archived, a
member added, an activity entry removed.

## The pipeline

Projects is the first of three components that are meant to be configured
together, or not at all.

| Stage | Component | What it is |
|---|---|---|
| Work | **Projects** | A private team workspace. Files, notes, a to-do list, a team with roles. Nothing here is public. |
| Release | [Publications](27-publications.md) | A frozen, versioned, citable slice of that work, with a DOI and — in principle — a curation step. |
| Catalogue | [Resources](29-resources.md) | The shelf a visitor browses at `/resources`, where a release sits beside tools, seminars and teaching material. |

What you decide at this stage is **who may start a project, how much space
one gets, and which features a project has**. Everything else about a
project is decided inside it by its own manager: you do not create projects,
name them, or add their members as a matter of course.

> **Note:** Nothing moves between the three stages by itself. Publishing a
> publication does not create a resource, and no code in the tree links a
> publication record to a resource record. They are three separate
> catalogues, and a hub that wants a release to appear in `/resources` as
> well has to have someone contribute it there.

A hub that never has private team work — one that is a catalogue and a
seminar calendar and nothing else — can leave this component and the next
one alone. If you enable one, enable all three: a project with the
Publications feature turned off gives a team nowhere to release its work,
and a publication with no project behind it cannot be assembled.

## Where to find it

The component is `com_projects`, reached from **Components → Projects**. Its
sub-navigation has two screens:

<!--include: core/components/com_projects/admin/projects.php:26-35-->

## The Projects list

This is the screen you open when someone writes to you, which is how nearly
all project administration starts: a team is out of space, a project owner
has left the institution, or a project that should have gone live is stuck
part-way through setup.

The default screen lists every project on the hub. The filter bar has a
**Search** box and four drop-downs — **Status**, **Privacy**, **Access**
and **Quotas** — and the columns sort on ID, **Title**, **Owner**,
**Featured**, **Status** and **Privacy**.

**Status** is the project's lifecycle state, and it is the filter you will use
most. **Setting up in progress** is the one to watch: a project in that state
was started and abandoned, and its owner usually does not realise it is not
a real project yet.

| Status | Meaning |
|---|---|
| Setting up in progress | Created but the setup wizard was never finished |
| Pending approval | Waiting on an administrator, when the sensitive-data question is configured to require approval |
| Active | In normal use |
| Archived | Read-only; files kept |
| Rejected | Refused at approval |
| Deleted | Removed by its owner or by an administrator |

> **Note:** The **Quotas** filter cannot tell the two tiers apart on a stock
> hub. It calls a project *premium* when its quota is at or above **Premium
> quota** and *regular* when it is at or below **Default quota**, and the
> shipped install sets both to 1 GB — so every project matches both. Give
> the two options different values before the filter means anything. The
> component manifest declares 5 GB and 30 GB; the shipped row overrides both
> with 1 GB, and the stored row is what runs.

The toolbar carries **Archive** (with `core.edit.state`), **Edit** (with
`core.edit`), and **Options** (with `core.admin`). A **Custom Description**
button appears alongside them only when the component's **Use custom profile
description template?** option is set to `custom`; it opens the form builder
for the project description fields.

## Editing a project

Almost every reason to open this form is a request from a team: more space,
a change of owner, a project put beyond further editing now the work is
finished.

Select a project's title to open it. The screen has two tabs, **Details** and
**Image**, and the Details tab is divided into panels:

- **Basic Info** — title, alias, description, tags, project type, owner or
  lead, and the system group provisioned for the project (named with the
  component's **Project group prefix**, `pr-` by default).
- **Parameters** — access level, whether the team and the publications show
  on the public project page, the page layout, the sensitive-data answer, and
  the grant fields when grant collection is on.
- **Files** — the project's **Files Quota** and **Publications Quota**, both
  in gigabytes, its current disk usage, and its external connections. Raising
  a quota here is the normal way to give one project more space than the
  hub-wide default.
- **Status** — a free-text message plus the administrative actions: **Send
  Message**, **Suspend**, **Activate** or **Reinstate**, **Delete**,
  **Archive**, **Unarchive**. The message you type is included in the mail the
  action sends.
- **Team** — the counts of managers, collaborators, authors and reviewers,
  and an **Add member** box that takes a username.

The Files panel also exposes two repository maintenance links: `git gc
--aggressive`, which repacks the project's git repository and takes minutes to
run, and **download sync log**, which also clears a stalled sync. Neither
does anything useful on a project created by this release — see [Version
tracking is off](#version-tracking-is-off).

> **Warning:** **Delete** and **Archive** act on the project's files as well
> as its record. Neither is reversible from the front end.

### Raising a project's quota

A group writes to say their project is full. They have 1 GB, which is what
every project gets on a stock hub, and their instrument produces 8 GB a
week.

1. Go to **Components → Projects**.
2. Find the project. Search on its title, or set **Quotas** to narrow the
   list.
3. Select the project's title.
4. Open the **Files** panel. **Files Quota** and **Publications Quota** are
   in gigabytes, and the panel shows what the project is using now.
5. Raise **Files Quota** to the figure you have agreed. Raise
   **Publications Quota** as well only if they are publishing; it is a
   separate allowance.
6. Select **Save & Close**.

The change takes effect immediately and is entirely reversible — until the
team fills the new space. Lowering a quota below what a project already
holds does not delete anything; it stops further uploads.

If you find yourself doing this for every project, raise **Default quota**
in [Options](#options) instead. That changes what *new* projects get, not
what existing ones have.

## Team

You edit a team for one reason: the person who owned the project is gone,
and nobody left in it can add anyone. Everything else a team needs is done
by its own managers on the front end.

Opening a project's team from the list gives its own screen, with **New** to
add a member and **Delete** to remove one. The table shows each member's role,
when they joined, when they last visited, and whether they were added
individually or through a group.

## Activity

Somebody's activity entry names a file, a person, or a message that should
not be on the record. This screen is how you take it off.

**Components → Projects → Activity** lists project activity entries across the
hub. Filter by search text, by action, by starred, and by state (published,
unpublished, trashed). The toolbar has **Delete**. This is where you remove an
activity entry that should not have been recorded — it does not undo the thing
that was recorded.

## Options

**Options** opens the component configuration, in five tabs:

| Tab | What it sets |
|---|---|
| Basic | Naming rules, image paths, activity logging, messaging, the default access level and page layout, and the `pr-` group prefix |
| Setup | What the project creation wizard asks for: terms agreement, sensitive-data question and whether it needs approval, grant information, who is made a manager, and group syncing |
| Administrative Groups | Group aliases that gate project creation, administration, sensitive-data review and reporting |
| File Repository and Quotas | **Files Git repo path**, whether it sits outside the web root, **Git path**, and the default and premium quotas for files and publications |
| Permissions | Which access groups hold each `com_projects` action |

Every parameter, with its default, is in the
[configuration reference](../../reference/configuration/components/projects.md).

Most of these are safe to change on a running hub. The two that are not are
**Files Git repo path** and **Project group prefix**: both are used to
build paths and group names for projects that already exist, and changing
either after projects have been created leaves the existing ones pointing
at the old value.

### Do not turn off "Allow project settings editing?"

The option is on the **Setup** tab, and its description — *Enable a screen
to edit project settings after project setup* — describes a screen that is
no longer built. What it actually controls is the **Team** section of the
Edit Project screen, and it controls it by deletion:

```php
$sections = array('info', 'team');
if ($this->config->get('edit_settings', 0) == 0)
{
	array_pop($sections);
}
$this->section = in_array($this->section, $sections) ? $this->section : 'info';
```

— [`setup.php:1092-1098`](../../../core/components/com_projects/site/controllers/setup.php).
Set it to **No** and the route is removed, and both of the links that lead
to the team editor go with it. The **Edit Team** button on a project's Team
tab and the **Invite people** entry in the project's own options menu are
still drawn, but following either now lands silently on the project
description form, with no message and no clue that the screen it named has
been taken away. A project's Team tab still lists the team and still lets a
manager approve a membership request; what it stops doing is letting anyone
add, remove or re-role a member. Nothing about the option's label tells you
any of this.

The component manifest declares it off. The shipped install row sets it on,
and the stored row is what runs, so a hub installed from this release has it
on and both links work. It is worth knowing which option did it if someone
turns it off — and worth knowing that the only remaining way in is a
hand-built `&active=team&action=edit` URL, on which the editor's controls
render outside the form that would submit them.

### Collecting grant information

If the hub runs grant-funded projects, the setup wizard can collect the grant
details, so that quotas can be justified against a budget and the hub can
report on funded work.

1. Go to **Components → Projects**.
2. Select **Options**.
3. Open the **Setup** tab.
4. Set **Collect grant info at setup?** to **Yes**.
5. Select **Save & Close**.

The wizard then asks the creator for a grant title, PI, award number, agency
and budget. Those fields appear afterwards in the Parameters panel of the
project's edit screen.

## Project features

This is the decision that shapes what a project *is* on your hub. A hub for
instrument data wants Files and little else; a hub whose projects lead to
published datasets needs Publications and Links as well; a hub running
human-subjects work needs the HIPAA checkbox. Every feature you enable is a
tab on every project, so enabling all of them gives every team a row of tabs
most of them will never open.

Each feature a project offers is a plugin in the `projects` group, and it is a
tab on the project's front-end page. They ship enabled or disabled according
to the hub's install; check **Extensions → Plug-in Manager** and filter the
type to `projects`.

| Plugin | What it adds |
|---|---|
| Projects - Files | The file repository, the browse view, uploads, and external connections |
| Projects - Publications | The publication pipeline: drafts, versions, and the contribution process |
| Projects - Notes | Wiki-style project notes |
| Projects - Todo | The to-do list |
| Projects - Team | Team membership and roles |
| Projects - Databases | Project data stores, backed by MySQL, browsed with the DataViewer |
| Projects - Links | External content that publications can cite |
| Projects - Feed | The project's activity feed |
| Projects - Info | The project description panel |
| Projects - Watch | Per-member subscriptions to project activity |
| Projects - HIPAA Compliant | A HIPAA compliance checkbox on the project |

Their parameters are in the
[plugin reference](../../reference/configuration/plugins/projects.md).

### Enabling a project feature

1. Go to **Extensions → Plug-in Manager**.
2. Type part of the plugin's name in the search box — for example
   `Projects - Databases` — or set **- Select Type -** to `projects` to list
   them all.
3. Tick the box beside the plugin in the **Plug-in Name** column.
4. Select **Enable**.

> **Warning:** The Databases plugin needs its own MySQL accounts before it
> works. Open the plugin and fill in **Database Host**, the read/write account
> (**Database User [rw]**, **Database Password [rw]**) and the read-only
> account used by the DataViewer (**Database User [ro]**, **Database Password
> [ro]**). Without the read-only account the Databases tab appears but the
> DataViewer cannot read anything.

### Restricting a feature to named projects

Two plugins — **Projects - Databases** and **Projects - Publications** — take
a **Restricted to projects** parameter. Fill it with a comma-separated list of
project *aliases*, not titles, and the feature appears only in those projects.
Leaving it empty means no restriction, so every project gets the feature.

1. Go to **Extensions → Plug-in Manager** and open the plugin.
2. Put the project aliases in **Restricted to projects**.
3. Select **Save & Close**.

No other project plugin has this parameter; the rest are on for every project
once enabled.

## Version tracking is off

Read this before you promise a team anything about file history. It is the
single most important thing to know about the component in this release, and
nothing in the administrator interface says it.

Every project created by this release has version tracking switched off. The
setup controller writes `versionTracking` as `0` when the project record is
first made and again when the project is activated
([`setup.php:103`](../../../core/components/com_projects/site/controllers/setup.php),
line 536), there is no option anywhere — component, plugin or project — that
sets it to `1`, and no screen offers to change it.

The value chooses the repository adapter. With it off, the project gets the
`nogit` adapter instead of the `git` one
([`repo.php:111-120`](../../../core/components/com_projects/models/repo.php)),
and that adapter's `history()`, `diff()`, `restore()` and `getTrash()` are
stubs that return `false`
([`nogit.php:520-562`](../../../core/components/com_projects/models/adapters/nogit.php)).
The project directory is a plain directory; no repository is created in it.

What a member sees, in the project's **Files** tab:

| Missing | Because |
|---|---|
| The timestamp beside a file is plain text, not a link | The per-file history link is drawn only when version tracking is on |
| No **Show trash** link | Deleted files are not kept |
| No **Sync** control and no sync status | Both are drawn only when version tracking is on |
| No link to the connections view from the file browser | The connections panel is drawn only when version tracking is on — see [External file connections](#external-file-connections) |
| The disk usage panel reports 0 for versions | There are no versions to measure |

> **Warning:** Deleting a file in a project is permanent. The `nogit`
> adapter's delete runs `rm` on the file and returns; there is no trash to
> restore it from and no earlier revision to fall back on. Tell your project
> teams this, because a member who has used a versioned workspace before
> will reasonably assume otherwise.

The two repository maintenance links on the admin **Files** panel — `git gc
--aggressive` and **download sync log** — belong to the git adapter and have
nothing to act on. **Git path** in [Options](#options) is likewise unused by
projects created in this release. Set it anyway if you have projects
carried over from a hub that did have version tracking; leaving it wrong
costs nothing today and is one thing less to find later. (The manifest
declares `/opt/local/bin/git`; the shipped row sets `/usr/bin/git`, which is
where the binary usually is.)

## Files on disk

Project files live outside the web root, under the path set by **File
Repository and Quotas → Files Git repo path** — `/srv/projects` by default —
as `/srv/projects/<alias>/files`.

Because the files are a real directory on a real filesystem, a hub can also
offer SFTP into them, which is the practical way to move data too large for a
browser upload. That access is granted through the per-project system group
(`pr-<alias>`) rather than through anything in this component: the hub's
directory service has to publish those groups and the file server has to
honour them. Files arriving that way appear in the project's file listing once the sync
has run. If SFTP is
available on your hub, the connection details and the local password a member
needs are hub-specific; document them for your own users rather than assuming
the defaults here.

## External file connections

A project's Files tab can point at storage the hub does not own — Google
Drive, Dropbox, GitHub, an S3 bucket — instead of, or alongside, the hub's own
storage. Setting that up needs an application registered with the provider
and a plugin configured on the hub; see [Project file
connectors](#project-file-connectors).
## Project file connectors

A project's Files tab normally shows the hub's own storage: a git repository
under `/srv/projects`, listed under a name built from the Projects - Files
plugin's **Default Connection Name** pattern, `%s Master Repository`. A **file
connector** lets a project point at storage the hub does not own — a Google
Drive folder, a Dropbox account, a GitHub repository, an S3 bucket — and
browse it in the same interface. The files stay with the provider. The hub
stores only a name, a provider, and a credential.

Setting one up takes two people. You, as hub manager, register an application
with the provider and put its credentials into the hub. A project manager then
creates the connection inside their own project and authorises it with their
own account.

### What ships

Four external providers ship in Hubzero 2.4, each as a plugin in the
`filesystem` group. A fifth, **Filesystem - Local**, backs the hub's own
repository; it has no parameters and nothing to configure.

| Provider | Plugin | Credentials you set on the hub | Fields the project manager fills in | How it authorises |
|---|---|---|---|---|
| Google Drive | Filesystem - Google Drive | **Client ID**, **Client Secret** | — | Redirects to Google the first time the connection is opened |
| Dropbox | Filesystem - Dropbox | **App key**, **App secret** | — | Redirects to Dropbox the first time the connection is opened |
| GitHub | Filesystem - GitHub | **Client ID**, **Client Secret** | **Repository** (`vendor/repository`) | Public repositories are read anonymously; a private one needs an explicit, manager-initiated OAuth step |
| AWS S3 | Filesystem - AWS S3 | — | **Access Key ID**, **Secret Access Key**, **Endpoint Region**, **Bucket Name**, **Directory to connect** | No OAuth; the IAM key is entered per connection |

All four work: the client libraries they need are installed with the CMS. The
parameters are also in the generated
[filesystem plugin reference](../../reference/configuration/plugins/filesystem.md).

> **Important:** The GitHub connector is read-only, and it deliberately does
> not start an OAuth handshake just to read a public repository. Only a
> private repository triggers one, and only when a project manager asks for it
> — because the token GitHub mints covers the authorising user's whole
> account, including write access.

### The OAuth callback

Google Drive, Dropbox and GitHub each need a redirect URI registered with the
provider. The hub answers them at:

| Provider | Redirect URI |
|---|---|
| Google Drive | `https://yourhub.org/developer/callback/googledriveAuthorize` |
| Dropbox | `https://yourhub.org/developer/callback/dropboxAuthorize` |
| GitHub | `https://yourhub.org/developer/callback/githubAuthorize` |

Substitute the hub's own hostname, and use `https`. The hub builds these from
its own base URL, so a provider that rejects the callback is almost always a
sign that the registered URI and the hub's actual address disagree.

### Setting up a connector

1. **Register an application with the provider.** See the per-provider notes
   below. Register the callback URI from the table above. You end up with a
   pair of values, called a client ID and secret, an app key and secret, or an
   access key and secret depending on the provider.
2. **Configure the plugin.** Go to **Extensions → Plug-in Manager**, search
   for the plugin — for example `Filesystem - Google Drive` — and open it. Set
   **Status** to **Enabled**, put the two values in the credentials panel —
   labelled **Credentials**, or **Google Drive Web Application Credentials** on
   that plugin — and select **Save & Close**. The AWS S3 plugin has nothing to
   set at this level; enabling it is enough.
3. **Make Files open on the connections view.** Open the **Projects -
   Files** plugin and set **Default Action** to **Connections (view
   available connections)**. The Files tab then lists the connections instead
   of opening the local repository straight away.

   This step is not optional on a hub running this release. The file
   browser's own link to the connections panel is drawn only when a project
   has version tracking on, and no project created by this release does (see
   [Version tracking is off](#version-tracking-is-off)), so with **Default
   Action** left on **Browse (browse local files)** project managers have no
   way in and the connectors you configured are invisible to them.
4. **Create the connection in a project.** This part is done by a project
   manager, on the front end, in the project's **Files** tab: pick the
   provider from the **New Connection** drop-down, give the connection a name,
   fill in any per-connection fields, decide whether to share it with the
   project, and save. Opening it for the first time sends them to the provider
   to grant access.

> **Note:** The **New Connection** drop-down lists every provider the hub
> knows about, whether or not that provider's plugin is enabled and
> configured. A connection to a provider you have not set up is created
> happily and then fails when someone opens it. If you do not intend to offer
> a provider, say so to your project managers.

> **Note:** Only a genuine project manager can authorise a connection. A hub
> administrator viewing the project cannot do it for them, by design — the
> handshake attaches the authorising person's own provider account to a
> connection the whole project may use.

Each connection is either private to the member who created it or shared with
everyone in the project; the checkbox is on the connection form, and shared
connections are the ones without the private marker in the connections list.
The connections list also has **Refresh Connection Credentials**, for when a
stored token has expired, and **Refresh Connection Path**.

### Registering the application

The providers change their developer consoles regularly. What follows is
accurate in outline; if a screen has moved, the values you need have not.

#### Dropbox

1. Go to <https://www.dropbox.com/developers> and sign in as the account that
   should own the application — usually one belonging to the group or
   institution, not to a person.
2. Select **My Apps**, then **Create app**.
3. Choose the access level to grant, and give the application a name. That
   name is what users see when they are asked to authorise it.
4. Add `https://yourhub.org/developer/callback/dropboxAuthorize` under
   **Redirect URIs**.
5. Select **Enable additional users**.
6. Copy the **App key**, reveal and copy the **App secret**.
7. Optionally use the branding tab to add your hub's icon and links; they
   appear on the authorisation screen.

> **Note:** Dropbox caps development applications at a small number of linked
> users. Apply for production status with Dropbox before you reach it, or
> members will start being refused.

#### Google Drive

1. Sign in to the [Google Cloud console](https://console.cloud.google.com/)
   and select or create a project.
2. Enable the **Google Drive API** for it.
3. Under **APIs & Services → Credentials**, select **Create credentials →
   OAuth client ID**, and choose **Web application**.
4. Set **Authorized JavaScript origins** to `https://yourhub.org`, and
   **Authorized redirect URIs** to
   `https://yourhub.org/developer/callback/googledriveAuthorize`.
5. Select **Create**, and copy the **Client ID** and **Client Secret**.

#### GitHub

1. In GitHub, go to **Settings → Developer settings → OAuth Apps** for the
   account or organisation that should own the application, and select **New
   OAuth App**.
2. Set the homepage URL to the hub, and the authorization callback URL to
   `https://yourhub.org/developer/callback/githubAuthorize`.
3. Copy the **Client ID**, generate and copy a **Client Secret**.

The hub asks GitHub for the `repo` scope, which is the only classic OAuth
scope that grants read access to private repositories. Tell members that, so
they understand what they are agreeing to. Public repositories never reach
this step.

#### AWS S3

There is no application to register and nothing to configure on the plugin.
Create an IAM user with read access to the bucket, generate an access key for
it, and give the key, the secret, the bucket's region code and the bucket name
to the project manager, who enters them on the connection form. **Directory to
connect** confines the connection to one prefix inside the bucket; leave it
empty for the whole bucket.

> **Warning:** Those S3 credentials are stored with the connection and are
> usable by everyone the connection is shared with. Issue a key that is scoped
> to the one bucket, or the one prefix, and nothing else.

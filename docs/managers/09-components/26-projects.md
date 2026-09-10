<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/projects
source-id: 3385
modified: 2016-07-12
imported: 2026-09-09
-->
# Projects

A project is a private workspace for a small team: a versioned file
repository, plus whatever other features the hub enables for it — notes, a
to-do list, publications, databases, activity. Members create and run their
own projects on the front end. In the administrator interface you set the
policy every project follows, and you intervene in individual projects when
something needs an administrator: a quota raised, a project archived, a
member added, an activity entry removed.

The component is `com_projects`, reached from **Components → Projects**. Its
sub-navigation has two screens:

<!--include: core/components/com_projects/admin/projects.php:26-35-->

## The Projects list

The default screen lists every project on the hub. The filter bar has a
**Search** box and four drop-downs — **Status**, **Privacy**, **Access**
and **Quotas** — and the columns sort on ID, **Title**, **Owner**,
**Featured**, **Status** and **Privacy**.

**Status** is the project's lifecycle state, and it is the filter you will use
most:

| Status | Meaning |
|---|---|
| Setting up in progress | Created but the setup wizard was never finished |
| Pending approval | Waiting on an administrator, when the sensitive-data question is configured to require approval |
| Active | In normal use |
| Archived | Read-only; files kept |
| Rejected | Refused at approval |
| Deleted | Removed by its owner or by an administrator |

The toolbar carries **Archive** (with `core.edit.state`), **Edit** (with
`core.edit`), and **Options** (with `core.admin`). A **Custom Description**
button appears alongside them only when the component's **Use custom profile
description template?** option is set to `custom`; it opens the form builder
for the project description fields.

## Editing a project

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
run, and **download sync log**, which also clears a stalled sync.

> **Warning:** **Delete** and **Archive** act on the project's files as well
> as its record. Neither is reversible from the front end.

## Team

Opening a project's team from the list gives its own screen, with **New** to
add a member and **Delete** to remove one. The table shows each member's role,
when they joined, when they last visited, and whether they were added
individually or through a group.

## Activity

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

## Files on disk

Project files live outside the web root, under the path set by **File
Repository and Quotas → Files Git repo path** — `/srv/projects` by default —
as `/srv/projects/<alias>/files`. Each project is a git repository, which is
what gives the front end its file history and version restore.

Because the files are a real directory on a real filesystem, a hub can also
offer SFTP into them, which is the practical way to move data too large for a
browser upload. That access is granted through the per-project system group
(`pr-<alias>`) rather than through anything in this component: the hub's
directory service has to publish those groups and the file server has to
honour them. Files arriving that way are committed to the project's repository
by the sync and show up in the file history with an `SFTP` origin. If SFTP is
available on your hub, the connection details and the local password a member
needs are hub-specific; document them for your own users rather than assuming
the defaults here.

## External file connections

A project's Files tab can point at storage the hub does not own — Google
Drive, Dropbox, GitHub, an S3 bucket — instead of, or alongside, the hub's own
repository. Setting that up needs an application registered with the provider
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
3. **Optionally make Files open on the connections view.** Open the **Projects
   - Files** plugin and set **Default Action** to **Connections (view
   available connections)**. The Files tab then lists the connections instead
   of opening the local repository straight away. Leave it on **Browse (browse
   local files)** if you would rather members reach connections through the
   file browser.
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

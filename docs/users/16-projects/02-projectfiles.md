<!--
status: rewritten
reviewed-against: 2.4-main @ 1924c22171
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/users/projects/projectfiles
source-id: 3315
modified: 2013-07-10
imported: 2026-09-09
-->
# Project files

The **Files** tab is the project's file area. Everyone on the team sees the
same files; managers and collaborators can change them, reviewers can only
read them. This chapter covers browsing and managing those files, the disk
quota they count against, and connecting a project to storage the hub does
not own.

## Browsing

The Files tab opens a plain file list: **Name**, size, and **Modified**.
Select a folder name to go into it, and use the breadcrumb trail at the top
of the list to come back out. Sort by name, size, or modified date by
selecting a column heading.

Selecting a file name opens it — an image or a PDF previews in the page,
anything else downloads. Where the hub has set up tool handlers for a file
type, extra entries appear beside the name to open the file in that tool.

A file whose modified time shows as `N/A` has no timestamp the hub could
read. A file marked `-- untracked --` is in the project directory but not
recorded in its repository.

## Adding and managing files

The row of controls above the list appears for managers and collaborators:

| Control | What it does |
|---|---|
| **Upload** | Add files to the folder you are in |
| **New Folder** | Create a folder |
| **Download** | Download the ticked files and folders |
| **Move** | Move the ticked items to another folder |
| **Delete** | Delete the ticked items |
| **Rename** | Rename the ticked item |
| **Preview** | Compile a LaTeX document to PDF, if the hub has LaTeX enabled |

Tick the box at the left of each row to choose what **Download**, **Move**,
**Delete**, and **Rename** act on.

### Uploading

1. Open the folder you want the files to land in.
2. Select **Upload**.
3. Drag files onto the drop area, or select it and pick files from your
   computer. Hold Ctrl (Cmd on a Mac) to pick more than one.
4. To have `.zip` and `.tar` archives unpacked as they arrive, tick
   *Uploading archive files (.zip, .tar)? Check the box to expand uploaded
   archive(s)*.
5. Select **Upload now!**.

The maximum size for one file is shown under the drop area; it is a hub-wide
setting and 100 MB by default. If the drag-and-drop uploader gives you
trouble, the **basic upload** link below the button falls back to a plain
form.

> **Tip:** A browser upload is the wrong tool for very large data. Some hubs
> offer SFTP into the project directory instead. Ask the hub's support staff
> whether yours does.

## File history

Where a project's file repository tracks versions, the date in the
**Modified** column is a link. Select it to open **File History** for that
file: every revision with its time and author, a **Change** column, a
**Download** link for each old version, and — for text files — **Diff
Revisions** to compare two of them. A **By** column in the file list shows
who last touched each file.

Version tracking is a per-project setting, and in Hubzero 2.4 new projects
are created with it switched off. In a project without it:

- The **Modified** date is plain text, not a link, and there is no file
  history, no diff, and no restore.
- Deleted files are gone; there is no **Show Deleted Files** view.
- The file list has no **By** column.

Nothing in the front end turns version tracking on. If your project needs
it, ask the hub's support staff.

## Disk usage and quota

Every project has a disk quota. At the bottom left of the file list is a
**Disk usage:** bar and the project's **Project Quota**. The bar turns to a
warning colour as you approach the limit.

Select the bar to open the **Disk Usage** page. It shows the quota, the
percentage used, and a breakdown:

- **Files** — the size of the files currently in the project.
- **Version History** — space taken by earlier revisions, on projects that
  track versions. Revision history counts against the quota.
- **Unused space** — what is left.

The default quota is set hub-wide; an administrator can raise it for one
project. To ask for more space, open a support ticket at
`/support/tickets` (see [Support](../12-support.md)) and say what the
project is, roughly how much space you need, and why. Hubs that run
grant-funded work usually also want the grant title, PI, agency, and award
number.

> **Note:** Project databases do not come out of this quota. They are stored
> in the hub's database server, not in the project's file area. See
> [Databases](05-databases.md).

## Connecting external storage

A project can browse storage the hub does not own — a Google Drive folder,
a Dropbox account, a GitHub repository, an S3 bucket — in the same file
interface. The files stay with the provider; the hub keeps only a name, a
provider, and a credential.

Four providers ship with Hubzero 2.4:

| Provider | What you supply |
|---|---|
| Google Drive | Nothing on the form. You sign in to Google the first time the connection is opened |
| Dropbox | Nothing on the form. You sign in to Dropbox the first time the connection is opened |
| GitHub | **Repository**, as `vendor/repository`. Public repositories are read without signing in |
| AWS S3 | **Access Key ID**, **Secret Access Key**, **Endpoint Region**, **Bucket Name**, and optionally **Directory to connect** |

### Creating a connection

You need to be a manager of the project.

1. Open the project's **Files** tab. Where the hub is set up for it, the tab
   opens on the connections view: the project's own repository, then one
   card for each connection, then a **New Connection** drop-down.
2. Choose the provider from **New Connection**.
3. Give the connection a **Name**.
4. Fill in any per-connection fields from the table above.
5. Tick *Share connection with everyone in the project?* to let the whole
   team use it, or leave it clear to keep it to yourself.
6. Select **Save**.
7. Open the connection. For Google Drive, Dropbox, and private GitHub
   repositories the hub sends you to the provider to grant access, then
   brings you back.

> **Note:** Coming back from Google or Dropbox lands you on a broken address
> in Hubzero 2.4 — the hub builds the return link wrongly. The connection
> itself is saved before the redirect, so go to the project's **Files** tab
> again and the new connection is there.

Each connection card carries **Refresh Connection Credentials**, for when a
stored token has expired, **Refresh Connection Path**, **Edit**, and
**Delete**. Connections that are not shared are marked as private.

Once connected, browse, upload, download, move, rename, and delete work much
as they do in the project's own file area.

> **Note:** The **New Connection** drop-down lists every provider the hub
> knows about, whether or not an administrator has configured it. A
> connection to a provider the hub has not set up saves happily and then
> fails when you open it. If a connection will not authorise, ask the hub's
> support staff whether that provider is configured.

> **Note:** If your Files tab opens straight into the file browser and you
> never see a connections view or a **New Connection** drop-down, the hub has
> not enabled it. An administrator controls that with the Projects - Files
> plugin's **Default Action** setting; see
> [Project file connectors](../../managers/09-components/26-projects/projectfileconnect.md).

Only a project manager can authorise a connection, and only with their own
provider account. A hub administrator cannot do it for you: the handshake
attaches the authorising person's account to a connection the whole project
may use.

> **Warning:** The AWS S3 keys you type into a connection form are stored
> with it and are usable by everyone the connection is shared with. Ask for a
> key scoped to the one bucket, or the one prefix, and nothing else.

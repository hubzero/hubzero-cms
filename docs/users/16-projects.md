<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
screenshots: stale
source: https://help.hubzero.org/documentation/240/users/projects
source-id: 3313
modified: 2013-07-10
imported: 2026-09-09
-->
# Projects

A project is a private workspace on the hub for a piece of work in progress.
You create it, you choose who is on the team, and nobody outside that team
sees anything in it unless you decide otherwise. Inside are a file area,
wiki-style notes, a shared to-do list, an activity feed, and — where the hub
offers them — searchable data tables and a route to publishing.

Projects exist for the months before work is ready to show anyone. A soil
lab measuring nitrate in field samples keeps the raw instrument readings,
the script that cleans them, and the argument about which outliers to drop
in a project, where the four people doing the work can all reach them and
nobody else can. When the cleaned dataset is finally worth citing, the
project is also where they release it.

That release is the other half of the story, and
[Publications](18-publications.md) tells it:

- A **project** is private, editable, and changes every day.
- A **publication** is public, frozen, and carries a DOI so a paper can cite
  it.

You do the work in the first and release it from the second. One project can
produce several publications, each publication can have several versions,
and the project carries on after every release. Nothing you put in a project
is published by putting it there; publishing is a deliberate, separate act.

> **Note:** A project is not a [group](11-groups/README.md). A group is a
> standing community — a lab, a course, a research centre — with its own
> pages, forum and membership, and it outlasts any one piece of work. A
> project is one body of work, a small team, and a file area. A group can own
> a project, but the two are not the same thing.

Projects live at `/projects` on the hub. The **Learn more** button on that
page opens `/projects/features`, the hub's own tour of what a project can do.

Read the two warnings in [The team](#the-team) and
[File history](#file-history) before you start. On a hub left at its shipped
settings, neither the team editor nor file versioning is available to you
after setup, and both are easier to plan around than to discover later.

## Finding your projects

Go to `/projects`. The page has two halves. The top explains what a project
is and offers **Start a project** and **Learn more**; the **My Projects**
section below lists every project you belong to. If you are not logged in,
that section asks you to log in first.

**Browse public projects**, at the top right, lists the projects whose
owners have made them public. You can search the list, sort it by title or
owner, and filter it to show archived projects.

> **Note:** **Start a project** appears only if the hub lets you create one.
> Some hubs restrict project creation to the members of a named group. If
> you do not see the button, ask the hub's support staff.

## Starting a project

Start a project when a piece of work needs a shared home: somewhere the
whole team reaches the same files, and somewhere the record of what was done
survives a laptop being replaced. One project per body of work is the usual
shape — not one per person, and not one per file.

Setup is a short wizard. By default it has two steps — **Describe your
project** and **Add team member(s)** — and a third, **One last thing...**,
appears only on hubs that have turned on the agree-to-terms screen. The
progress bar at the top of each step shows where you are.

![The first step of project setup: title and alias](media/projects-projects-1.png)

### Step 1: describe your project

1. Select **Start a project**.
2. Type the project's full name in **Title**. The soil lab types *Nitrate
   in Field Samples 2026*.
3. Type a short name in **Alias Name (for the project URL)**. Use lowercase
   letters and numbers only: no spaces, punctuation, or special characters.
   The soil lab types `nitrate2026`. The alias becomes the project's
   address, `/projects/nitrate2026`, and cannot be changed afterwards, so
   pick something you will still recognise in two years. The form checks the
   name as you type and suggests an alternative if it is taken.
4. Answer *Would you like to provide more information about your project?*
   with **Yes, I'll do it now** to fill in the rest of the step, or **No,
   later** to skip straight to the team.
5. In **About**, describe the project. This is what visitors see if the
   project is public.
6. Under *Include project in search?* choose one:
   - **Project is hidden from search and listings (private project)**
   - **Anyone can find this project in search & listings and view its basic
     information (public project)**
7. Under *Add a project picture*, upload an image to use as the project's
   thumbnail.
8. Select **Save all and continue**.

### Step 2: add team members

The second step is the team editor. You are already on the team, as the
project's manager; this step is where everyone else gets in.

Add everyone here. On most hubs this is the only screen that opens the team
editor — see the warning in [The team](#the-team) — so the soil lab adds all
four people now rather than planning to come back.

1. Choose the role for the people you are about to add: **manager**,
   **collaborator**, or **reviewers**.
2. Type a name in **Individual**, or an email address to invite someone who
   has no account on the hub yet. Type a group name in **User group** to add
   everyone in a hub group at once. Both boxes suggest matches as you type;
   pick one from the list.
3. Select **add**.
4. Repeat for each person or group. The table below the form lists everyone
   on the team.
5. Select **Save all and continue**.

The three roles are:

| Role | Can |
|---|---|
| Manager | Invite and remove team members, change project information and settings, and everything a collaborator can do |
| Collaborator | Upload and manage project files, edit project publications, use the project's tools such as notes and to-do items |
| Reviewer | View files, publications, notes, to-do items and team members. Read only |

### Step 3: agree to terms

Where the hub has enabled it, a final screen asks you to accept the hub's
privacy terms before the project opens. Depending on how the hub is
configured it may also ask:

- **Are you planning to upload datasets containing any sensitive or
  restricted data?** — either as a single acknowledgement that the project
  will *not* hold sensitive data, or as a set of checkboxes for
  export-controlled data, IRB-governed data, HIPAA-protected health
  information, and FERPA-protected student records. Ticking one of the
  latter two may add an extra acknowledgement you have to confirm.
- **Grant information** — grant title, PI, award number, agency and budget,
  if the hub collects it.

Tick the box next to *Yes, I read, understand and acknowledge* the privacy
terms, then select **Save all and continue**. The project opens.

On hubs that require approval for sensitive-data projects, answering yes to
the export, HIPAA, or FERPA questions puts the project into *pending
approval* instead. An administrator reviews it before it becomes active.

## Inside a project

A project is a set of areas, and you will not use all of them. Files and
Updates carry most of the work on most projects; Notes, To Do and Databases
earn their place on some and sit unused on others. Nothing is lost by
ignoring a tab.

Each area of a project is a tab. Which tabs you see depends on which project
plugins the hub has enabled and configured:

| Tab | What it is |
|---|---|
| Updates | The activity feed: a stream of what everyone on the team has done, with a box to post your own update and comment on others |
| Info | The project description, and the grant information if the hub collects it |
| Team | The member list and, for managers, the team editor |
| Files | The project's file area. See [Project files](#project-files) |
| Databases | Searchable tables built from a spreadsheet. See [Databases](#databases) |
| Notes | Wiki-style pages for anything the team needs to write down |
| To Do | Shared task lists |
| Publications | Drafts and released versions of work published from the project |

Two more plugins ship but add no tab of their own: **Projects - Watch**
provides the feed subscription described below, and **Projects - Links**
supplies external content that publications can cite.

The tabs sit in a menu down the left of the project page. Where the hub uses
the extended page layout instead, they run across the top and **Files** and
**Databases** are grouped under an **Assets** heading.

![The project menu, an activity feed, and the feed subscription box](media/projects-210projects4.png)

> **Note:** The picture above shows the extended layout, where **Files** and
> **Databases** sit under **Assets**. In the standard layout, which is the
> default, every tab is listed on its own.

At the top right of every project page is your role in it — *Project
manager*, *Project collaborator*, or *Project reviewer*. Hover over it for
the menu of project-wide actions: **Edit project**, **Invite people to
join**, **View public profile** (public projects only), and **Leave this
project**.

## Editing project information

1. Open the project.
2. Hover over your role at the top right and select **Edit project**.
3. Change the **Title** and edit the description in **About**.
4. To change the thumbnail, choose a file under *Upload new image* and
   select **Upload**. It replaces the existing one.
5. Select **Save changes**.

Managers can always do this. On hubs that allow it, collaborators can edit
the description too.

The alias cannot be changed. Ask the hub's support staff if a project's
address has to move.

## The team

The **Team** tab lists everyone on the project with their role, when they
joined, when they last visited, and — for members who came in through a hub
group — which group. The list itself is read-only, except that a manager
sees **Approve request** and **Deny request** beside anyone who has asked to
join.

Managers get an **Edit Team** button above the list. It opens the same
editor used during setup: add people, change a member's role by selecting
it, tick the boxes beside members and select **delete** to remove them.
Under *Project owner* in that editor, **Edit** hands the project to someone
else.

Any member of a project with more than one member can leave it: hover over
your role at the top right and select **Leave this project**.

> **Warning:** The **Edit Team** button, and the **Invite people to join**
> entry in the role menu, both open the team editor only if the hub has
> turned on *Allow project settings editing?*, which is off by default.
> Where it is off, both land on the Edit Info screen instead, and there is
> no other way to reach the editor. Add everyone you need during setup, or
> ask the hub's support staff to enable the setting.
>
> What that costs you: once setup is finished you cannot add the student who
> joined in March, cannot remove the one who left in June, cannot promote a
> collaborator to manager, and cannot hand the project to someone else. The
> **Team** tab still lists everyone and still shows a manager **Approve
> request** and **Deny request** beside anyone who asks to join, so requests
> are the one route in that survives. Everything else goes through the hub's
> support staff, one request at a time. Plan the team before you finish
> setup.

### Group-owned projects

A project can be owned by a hub group rather than by a person. In a
group-owned project the team editor offers a choice:

- **Include all group members.** Everyone in the group has project access
  for as long as their group membership lasts, and cannot be removed
  individually.
- **Specify membership.** You choose which group members get access.

Add people from outside the group in the usual way.

## Notes

Notes are for the writing that is not a file: the method as it currently
stands, the meeting decisions, the reasons a parameter was changed. The soil
lab keeps its sampling protocol here, so that when a reviewer asks how the
samples were stored there is one page to point at rather than four people's
memories.

The **Notes** tab is a small wiki. Create a page, write in it, tag it, and
comment on it. Each note keeps its history, so you can see what changed.
Note history is the notes plugin's own, and does not depend on the project's
file version tracking.

A note is private to the team until you share it. At the bottom of a note,
select **Generate public link**. The pop-up gives you an address anyone can
open — paste it into email, chat, or a web page. Select **Close this** to
dismiss the pop-up. In a public project you can also choose whether the note
is *listed* on the project's public page or reachable only by its link.

![A project note, with the Generate public link control below it](media/projects-210projects3.png)

## To Do

The To Do tab is for work that has to be assigned to a person and finished:
*re-run the calibration*, *chase the missing March samples*, *write the
data dictionary before we submit*. If your team already tracks its work
somewhere else, skip this tab.

The **To Do** tab holds the project's task lists. Add an item, assign it to
someone, and check it off when it is done. Two buttons above the list switch
the layout: **Pinboard view** shows items as cards you can drag to reorder,
**List view** stacks them as rows. Both show the newest first.

![The To Do tab and its view buttons](media/projects-210projects2.png)

The sidebar lists the project's to-do lists. **My to do's** is a built-in
list of everything assigned to you; you cannot add to it directly, because
it fills itself from items assigned to you elsewhere. Select **Add** to make
a list of your own, and pick a list when you create an item.

## Following what happens

You do not want to open a project every day to find out whether anyone
uploaded anything. Feed subscription mails you the parts you care about
instead.

Everything the team does shows up on the **Updates** tab. To get it by
email as well, find the **Subscribe to feed updates** box in the sidebar of
a project page and select it.

![The feed subscription options](media/projects-projects-feedmodule2.png)

Tick the categories you want to hear about — project updates and comments,
team changes, new files, publishing activity, new to-do items, new notes —
and select **Save**. Mail goes to the address on your hub profile. Come back
to the same box, now labelled **Manage feed subscription**, to change or
cancel it.

Some hubs subscribe members automatically when they join a project; in that
case the box lets you opt out.

## Deleting a project

Delete a project only when the work is genuinely finished with. A project
that has produced a publication should be left alone: the publication is a
separate, permanent record, but the draft material behind it lives here.

Deleting a project is a manager's job and it cannot be undone from the front
end.

1. Hover over your role at the top right and select **Edit project**.
2. Under *Need to cancel this project?* in the left column, select
   **Delete**.
3. Confirm with **Yes, delete**.

![The delete control on the Edit Project screen](media/projects-210projects.png)

> **Note:** The picture above shows an older Edit Project screen, where
> **delete** was a link at the foot of the page. It is now a **Delete**
> button in the left column, under the same wording.

The project disappears from your list and from search, and the hub revokes
the file-system access that went with it. The files themselves stay on the
hub's disk. If you delete a project by mistake, ask the hub's support staff:
an administrator can still reach it.

Archiving is the gentler option, and only an administrator can do it. An
archived project keeps its files and stays readable, but nobody can change
it.

## What administrators control

Much of what a project offers is set hub-wide, not per project: which tabs
exist, how much disk space a project gets, whether the setup wizard asks
about sensitive data or grants, and which external storage providers a
project can connect to. Those settings are described in
[Projects](../managers/09-components/26-projects.md) and
[Project file connectors](../managers/09-components/26-projects.md#project-file-connectors)
in the managers book.
## Project files

The file area is the reason most teams start a project. It is one folder
tree that everyone on the team reaches from a browser, on the hub's own
disk, with a quota and a backup policy the hub runs — rather than a folder
on somebody's laptop, or an email thread with attachments. The soil lab puts
the instrument exports, the cleaning script and the draft figures here.

Read [File history](#file-history) before you rely on it. On a new project
there is no undo.

The **Files** tab is the project's file area. Everyone on the team sees the
same files; managers and collaborators can change them, reviewers can only
read them. This chapter covers browsing and managing those files, the disk
quota they count against, and connecting a project to storage the hub does
not own.

### Browsing

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

### Adding and managing files

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

#### Uploading

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

### File history

Where a project's file repository tracks versions, the date in the
**Modified** column is a link. Select it to open **File History** for that
file: every revision with its time and author, a **Change** column, a
**Download** link for each old version, and — for text files — **Diff
Revisions** to compare two of them. A **By** column in the file list shows
who last touched each file.

> **Warning:** Version tracking is a per-project setting, and in Hubzero 2.4
> every new project is created with it switched off. Project setup writes
> version tracking off both when the project is created and again when it is
> activated, and the setting chooses which file adapter the project uses. A
> project without it gets an adapter whose history, diff, restore and trash
> are all stubs that return nothing. In such a project:
>
> - The **Modified** date is plain text, not a link. There is no **File
>   History**, no **Diff Revisions**, and no way to fetch an earlier
>   revision.
> - **Delete** is permanent. Deleted files do not go to a trash you can open,
>   and there is no **Show Deleted Files** view to recover them from.
> - Uploading a file over one that is already there replaces it, and the
>   copy you replaced is gone.
> - The file list has no **By** column, so nothing records who last changed
>   a file.
>
> Nothing in the front end turns version tracking on. If your project needs
> it, ask the hub's support staff before you put anything irreplaceable in
> the file area.

What that costs a team in practice: the soil lab's cleaning script has no
history, so when a result stops reproducing there is no way to see what
changed in it or when. Two people editing the same spreadsheet overwrite
each other silently. The usual answer is discipline the hub cannot enforce
for you — date the file names, keep the working copy of code in a repository
of your own, and say on the **Updates** tab what you changed and why.

### Disk usage and quota

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
`/support/tickets` (see [Support](12-support.md)) and say what the
project is, roughly how much space you need, and why. Hubs that run
grant-funded work usually also want the grant title, PI, agency, and award
number.

> **Note:** Project databases do not come out of this quota. They are stored
> in the hub's database server, not in the project's file area. See
> [Databases](#databases).

### Connecting external storage

Some data is already somewhere else and is not going to move: a shared
Google Drive the department pays for, a GitHub repository the code already
lives in, an S3 bucket holding more data than a project quota would ever
hold. A connection lets you browse and work with that storage from the
project's Files tab without copying it onto the hub.

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

#### Creating a connection

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
> [Project file connectors](../managers/09-components/26-projects.md#project-file-connectors).

Only a project manager can authorise a connection, and only with their own
provider account. A hub administrator cannot do it for you: the handshake
attaches the authorising person's account to a connection the whole project
may use.

> **Warning:** The AWS S3 keys you type into a connection form are stored
> with it and are usable by everyone the connection is shared with. Ask for a
> key scoped to the one bucket, or the one prefix, and nothing else.
## Databases

A spreadsheet in the file area is a file: you download it, open it, and
scroll. A project database turns that same spreadsheet into a table your
team can search, sort and filter in the browser, with images and links in
the cells. It is worth doing when a table is big enough that reading it as a
`.csv` is painful, and when several people need to look things up in it.
The soil lab does this with its sample register — a few thousand rows of
site, date, depth and reading — so anyone can find a sample without opening
a spreadsheet.

A project database — DataStore Lite — turns a spreadsheet in your project's
files into a searchable, sortable table anyone on the team can browse. You
upload a `.csv` file, tell the hub what each column holds, and the hub builds
a real database table from it and opens it in the hub's DataViewer.

You need to be a manager or a collaborator on the project. Reviewers can see
the databases a project has but cannot create, update, or delete them.

### If there is no Databases tab

The Databases tab is not a standard part of a project. It comes from the
**Projects - Databases** plugin, which needs two MySQL accounts that only a
hub administrator can set: a read/write account that builds the tables and a
read-only account that the DataViewer uses to read them.

If either account is missing or wrong, the plugin hides the tab entirely.
There is no error message and no explanation — the tab simply is not there:

<!--include: core/plugins/projects/databases/databases.php:79-83-->

So if your project has no **Databases** tab, one of three things is true:
the plugin is disabled, the hub has restricted it to a list of projects that
does not include yours, or its database accounts are not configured. All
three are for an administrator to fix; see
[Projects](../managers/09-components/26-projects.md#enabling-a-project-feature)
in the managers book, and ask the hub's support staff.

A configured tab can still fail at the last step. If the databases list
loads but selecting a database's title gives you an error rather than a
table, the read-only account exists but cannot read what the read/write
account created. That is also one for the administrator.

### Preparing the spreadsheet

The source has to be a `.csv` file — comma-separated values, which every
spreadsheet program can save.

- The first row holds the column labels.
- Every row below it is one record.
- To link a database row to a file in the project, put the file's name and
  extension in a column, spelled exactly as the file is — `projectstep1.png`,
  not `ProjectStep1.PNG`. The file has to be in the same folder as the `.csv`
  or in a folder below it.

### Creating a database

#### Upload the file

1. Open the project and select **Files**.
2. Select **Upload**.
3. Drag the `.csv` file onto the drop area, along with any images or
   documents the database will link to. Select **Upload now!**.

See [Project files](#project-files) for more on uploading.

#### Step 1: select the file

1. Select **Databases**.
2. Select **Create a database**.
3. Pick the `.csv` file from the drop-down. It lists every `.csv` file in the
   project, grouped by folder.
4. Select **Next »**.

If the drop-down is empty, the project has no `.csv` files, or the only ones
it has are already used by a database. The screen offers links back to the
databases list and to the file area.

#### Step 2: verify data

The hub reads the file, guesses a type for each column, and shows a preview.
The heading tells you how many records the file holds and how many are shown
— the preview loads the first hundred rows, but the database is built from
all of them.

Select the edit control in a column heading to open **Column Properties**,
which has three tabs.

**General**

| Field | What it does |
|---|---|
| **Label** | The column heading. Required |
| **Description** | Shown when a reader selects the column title |
| **Width** | A pixel value. The column is held to that width |
| **Truncate text at width** | Cuts the text off at that width |
| **Units** | A unit of measure — inches, meters, liters — shown under the heading |

**Column Type**

Choose one of: **Text [small]**, **Text [large]**, **Link**, **Image**,
**Email**, **Integer**, **Floating Point**, **Numeric [4 decimal places]**,
**Date [yyyy-mm-dd]**, or **Date & Time [yyyy-mm-dd HH:MM:SS]**.

- Use **Text [small]** for a title or a short label, **Text [large]** for
  anything longer than a sentence. Either can be set to **Limit text to a
  single line**, which hides the overflow; a reader sees the whole value by
  hovering over it or selecting it.
- **Image** shows a preview in the cell. **Link** makes the value a
  hyperlink. Both accept a full URL, or the name of a file in the project.
  For a file in the project, tick **Repository Files?** and choose the
  **Repository Path** — only the folder holding the `.csv` and folders below
  it are offered, and the `.csv` must give the bare file name.

**Other**

Set the column's **Alignment**, **Text Color**, and **Background Color**.

Select **Update Column** to keep the changes, or **Cancel** to drop them.
When every column looks right, select **Next »**. **« Back** returns to the
file selection.

#### Step 3: title and description

1. Type a **Title** and a **Description**.
2. Select **Finish**.

The hub creates the table and returns you to the databases list.

> **Important:** Creating a database rewrites the source `.csv` in your
> project files. The hub writes three header rows into it — the column
> labels, the column properties, and a row reading `DATASTART` — followed by
> the data. That is how the database remembers its column setup. Keep those
> three rows when you edit the file later, and change only the rows below
> `DATASTART`.

### Using a database

The databases list shows one row per database:

| Column | What it is |
|---|---|
| **Title** | Opens the database in the hub's DataViewer, in a new tab |
| **Source File** | Downloads the `.csv` the database was built from |
| **Created On**, **Created By** | Who built it and when |
| **Update Database** | Rebuilds it from the current `.csv` |
| **Delete** | Removes the database |

The last two appear only for managers and collaborators.

A source file the hub can no longer find is greyed out, and **Update
Database** is disabled until the file is restored. A source file that has
changed since the database was built is flagged, which is your cue to update.

Until the database is attached to a published publication, only members of
the project can open it in the DataViewer.

To change a database's title or description without rebuilding it, select
the small edit control next to its title in the list, change the values in
the **Update Title & Description** box, and save.

### Updating a database

Do this whenever the data changes, or when you want to change how a column
is displayed. If you only want to change the display, skip to step 6.

1. Select **Databases**, then the database's **Source File** to download
   the `.csv`.
2. Open it in a spreadsheet program or a text editor. The first three rows
   are the DataStore header; the data starts after the `DATASTART` row.
3. Add, remove, or change rows *below* `DATASTART` only. Keep the values
   consistent with the column types, which the second row lists.
4. Save it in the same `.csv` format, under the same name.
5. Go to **Files**, select **Upload**, and upload the file back into the same
   folder, replacing the old one.
6. Go back to **Databases** and select **Update Database** on the row.
7. Check the columns in step 2, adjusting any column properties you want to
   change, and select **Next »**.
8. Confirm the title and description and select **Finish**.

The old table is dropped and rebuilt, so anything the DataViewer showed
before is replaced by the new data.

### Deleting a database

Select **Delete** on the database's row and confirm. This drops the table
and removes the database from the project. The source `.csv` stays in the
project's files, so you can build the database again from it.

### Quota

Project databases are stored on the hub's database server, not in the
project's file area, so they do not count against the project's disk quota.
The source `.csv` in the file area does.

### Publishing a database

A project database is private to the team. Attaching it to a publication is
how you make it readable outside the project, and how it gets a DOI. The
publication's editor offers a **Select a Database** picker listing the
project's databases; pick **Databases** as the publication type when you
start the draft. See [Publications](18-publications.md).

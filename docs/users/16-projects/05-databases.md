<!--
status: rewritten
reviewed-against: 2.4-main @ 1924c22171
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/users/projects/databases
source-id: 3318
modified: 2014-11-19
imported: 2026-09-09
-->
# Databases

A project database — DataStore Lite — turns a spreadsheet in your project's
files into a searchable, sortable table anyone on the team can browse. You
upload a `.csv` file, tell the hub what each column holds, and the hub builds
a real database table from it and opens it in the hub's DataViewer.

You need to be a manager or a collaborator on the project. Reviewers can see
the databases a project has but cannot create, update, or delete them.

## If there is no Databases tab

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
[Projects](../../managers/09-components/15-projects/README.md#enabling-a-project-feature)
in the managers book, and ask the hub's support staff.

A configured tab can still fail at the last step. If the databases list
loads but selecting a database's title gives you an error rather than a
table, the read-only account exists but cannot read what the read/write
account created. That is also one for the administrator.

## Preparing the spreadsheet

The source has to be a `.csv` file — comma-separated values, which every
spreadsheet program can save.

- The first row holds the column labels.
- Every row below it is one record.
- To link a database row to a file in the project, put the file's name and
  extension in a column, spelled exactly as the file is — `projectstep1.png`,
  not `ProjectStep1.PNG`. The file has to be in the same folder as the `.csv`
  or in a folder below it.

## Creating a database

### Upload the file

1. Open the project and select **Files**.
2. Select **Upload**.
3. Drag the `.csv` file onto the drop area, along with any images or
   documents the database will link to. Select **Upload now!**.

See [Project files](02-projectfiles.md) for more on uploading.

### Step 1: select the file

1. Select **Databases**.
2. Select **Create a database**.
3. Pick the `.csv` file from the drop-down. It lists every `.csv` file in the
   project, grouped by folder.
4. Select **Next »**.

If the drop-down is empty, the project has no `.csv` files, or the only ones
it has are already used by a database. The screen offers links back to the
databases list and to the file area.

### Step 2: verify data

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

### Step 3: title and description

1. Type a **Title** and a **Description**.
2. Select **Finish**.

The hub creates the table and returns you to the databases list.

> **Important:** Creating a database rewrites the source `.csv` in your
> project files. The hub writes three header rows into it — the column
> labels, the column properties, and a row reading `DATASTART` — followed by
> the data. That is how the database remembers its column setup. Keep those
> three rows when you edit the file later, and change only the rows below
> `DATASTART`.

## Using a database

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

## Updating a database

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

## Deleting a database

Select **Delete** on the database's row and confirm. This drops the table
and removes the database from the project. The source `.csv` stays in the
project's files, so you can build the database again from it.

## Quota

Project databases are stored on the hub's database server, not in the
project's file area, so they do not count against the project's disk quota.
The source `.csv` in the file area does.

## Publishing a database

A database can be attached to a publication, which is how you make it
readable outside the project. The publication's editor offers a **Select a
Database** picker listing the project's databases. See
[Publications](../18-publications/README.md).

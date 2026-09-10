<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
-->
# DataViewer

The DataViewer turns a MySQL table into a searchable, sortable spreadsheet on
a hub page. It has no content of its own and no list of things to manage: it
is a renderer that other components link into, and every table it draws
belongs to something else. On a hub built from this repository the only thing
that links into it is the **Projects - Databases** plugin, whose *DataStore
Lite* databases open in the DataViewer, and the publications that attach one.

There is an administrator screen at **Components → DataViewer**, but it edits
configuration files belonging to a separate `com_databases` component that is
not part of Hubzero 2.4. On a stock hub that screen lists nothing and there is
nothing on it to do. What the DataViewer actually needs from you is set
elsewhere: the read-only MySQL account on the **Projects - Databases**
plugin. Without it the DataViewer cannot read anything, and the Databases tab
does not appear in projects at all.

So the question this chapter answers is narrow. A project team asks to keep a
few thousand rows of measurements on the hub and have people browse them
rather than download a spreadsheet. That is what the DataViewer is for, and
what it costs you is two MySQL accounts and one plugin. A hub whose projects
only ever exchange files needs none of it, and can stop reading here.

## What it is not

The DataViewer is not a database manager, and it is not somewhere you put
data. It never creates a table, has no import screen, and holds no rows of its
own; a project team creates the data through the project's own Databases tab.
It is also not a chart tool for arbitrary content — it draws exactly the
tables another component hands it, and on this hub that means project
databases and the publications built from them.

## What a manager has to configure

Do this once, before the first project team asks. Both accounts go on one
plugin, and getting the read-only one wrong is the fault you will spend an
afternoon on, because the symptom appears in a project rather than here.

Everything that makes the DataViewer work is on the Projects - Databases
plugin, not on this component:

1. Go to **Extensions → Plug-in Manager** and open **Projects - Databases**.
2. Set **Database Host**, the read/write account (**Database User [rw]**,
   **Database Password [rw]**) that creates the tables, and the read-only
   account (**Database User [ro]**, **Database Password [ro]**) that the
   DataViewer reads them with. The read-only user defaults to `dataviewer`.
3. Set **Status** to **Enabled** and select **Save & Close**.

See [Projects](26-projects.md#enabling-a-project-feature) for the rest
of that plugin, and [Databases](../../users/16-projects.md#databases) in
the users book for what a project team does with the result.

> **Note:** If a project's databases list loads but opening a database's
> title gives an error instead of a table, the read/write account works and
> the read-only one does not. That is the usual symptom of a wrong password or
> of a read-only user that has not been granted `SELECT` on the databases the
> read/write user creates.

## Where the DataViewer appears

Nothing on the hub links to `/dataviewer` on its own. Two things link into it:

| Link comes from | URL it builds |
|---|---|
| A project's **Databases** tab | `/dataviewer/view/<project alias>:dsl/<database name>/` |
| A data attachment on a [publication](27-publications.md) | `/dataviewer/view/publication:dsl/<database name>/?v=<version>` |

The segment after the colon is the mode. Three modes exist in the code:

| Mode | Reads from | State in Hubzero 2.4 |
|---|---|---|
| `dsl` | `#__project_databases`, using the Projects - Databases plugin's read-only account | Works. This is the one the hub uses. |
| `db` | JSON configuration files under a `com_databases` base directory | Inert — `com_databases` does not ship with the CMS |
| `ds` | A `com_datastores` component's `ds_*` databases | Inert — `com_datastores` does not ship with the CMS |

A hub that has none of the extra components installed only ever sees `dsl`.

## Who can see a data view

Access is decided per data view, not per hub. In `dsl` mode:

- An **unpublished** project database is visible to the project's owners.
- A database attached to a **published** publication version is public.
- A database attached to an unpublished version is visible to the project's
  owners and to the publication's curator and curator groups.
- A visitor who is not logged in and is not entitled to see the data is sent
  to the login form.

The component's own **ACL Users** and **ACL Groups** options are a second,
blunter layer. **ACL Users** never takes effect on a project database,
because the data view supplies its own user list and that list wins.
**ACL Groups** does take effect, because a project database supplies no group
list.

> **Warning:** Any group named in **ACL Groups** can read *every* project
> database on the hub, including databases in projects its members are not on.
> Leave the field empty unless you mean exactly that.

## What a visitor sees

The data view opens as a table with a global search box, per-column filters,
and a page-size selector. Above it is a toolbar; which buttons appear depends
on what the data definition declares:

| Button | What it does | When it appears |
|---|---|---|
| **Download** | Downloads the current result set as CSV | Unless the data definition turns it off |
| **Fullscreen** | Expands the table to the whole window | Unless the data definition turns it off |
| **Filter Dialog** | Opens the data definition's own filter form | When the data definition declares filters |
| **Clear Filters** | Clears the column filters and the global search | Always |
| **No-Wrap** | Stops cell text wrapping | Unless the data definition turns it off |
| **Charts** | Opens a chart panel, with **Download Chart** | When the data definition declares charts |
| **Maps** | Opens a map panel, with **KML**, **KMZ** and **SHP** exports | When the data definition declares `show_maps` |
| **Customize DataView** | Lets the reader pick a subset of columns | When the data definition declares a customizer |

Large tables switch from browser-side to server-side processing on their own
when **Dynamic Processing mode** is on; see the options below.

## The administrator screen

**Components → DataViewer** opens a screen headed **Database List**. It scans
a base directory on disk — taken from `com_databases`, or `/db/databases`
when that component is absent — for `*/database.json` files, and lists one row
per database it finds, with links to **Edit Config** and **Dataviews**. Those
lead to a pair of JSON editors: one for a per-database DataViewer
configuration file, one for individual data definitions.

On a hub installed from this repository the directory does not exist, so the
list is empty and there is nothing to open. The only working control on the
screen is **Options**.

Project databases do not appear here. Their data definitions live in the
`#__project_databases` table and are built by the project team through the
Databases tab, not by editing files.

> **Warning:** Unlike every other component, this one performs no permission
> check of its own. Anyone who can log in to the administrator interface at
> all can open it, whatever the **Permissions** tab says. The component
> declares the usual seven actions and exposes a permissions fieldset, and
> nothing reads either. On a stock hub there is nothing on the screen to
> misuse; on a hub that has the `com_databases` component, there is. It is
> Recorded with the project.

## Options

Select **Options** on the DataViewer screen. The full parameter list is in the
[generated reference](../../reference/configuration/components/dataviewer.md).

- **Record Display Limit** — rows per page in a new data view: 5, 10, 25, 50
  or 100.
- **Dynamic Processing mode** and **Client-side threshold** — when the mode is
  on, a data view whose row count times its visible column count exceeds the
  threshold is paged and sorted by the database instead of in the browser.
  Raising the threshold makes small tables feel faster and large ones slower;
  lowering it does the reverse. The threshold is counted in cells, not rows.
- **DB Mode enabled** — only meaningful with the separate `com_databases`
  component installed. Leave it off.
- **ACL Users** and **ACL Groups** — see the warning above.

The **Permissions** tab has no effect at all: nothing in the component reads
the actions it sets. Access to a data view is decided per data view, and
access to the administrator screen is not decided at all.

## What does not work

- **The `db` and `ds` modes.** Both need components that are not part of
  Hubzero 2.4. A URL that names either mode fails.
- **File and image columns.** A data definition that declares a `file` or
  `gallery` column renders links to `/dataviewer/file/…` and
  `/dataviewer/gallery/…`. The component implements only two front-end tasks,
  `view` and `data`, so both links return *Invalid or Missing Dataview*. The
  view templates that would serve them are present but unreachable. Only the
  DataStores mode produced such columns, so a project database is unaffected.
- **The Maps panel.** It loads the Google Maps JavaScript API with no API key
  and with the long-removed `sensor` parameter. Google refuses unkeyed
  requests, so the map does not draw. The **KML**, **KMZ** and **SHP** export
  buttons in the same panel are generated server-side and do work — but they
  are inside the panel the map failed to fill.

## The component's own database access

The DataViewer connects to the data with `mysqli` directly, using the
credentials it is handed for the mode, and not through the CMS's database
layer. That is why the read-only account on the Projects - Databases plugin
matters and why a mistake in it produces a raw connection error on the page
rather than a Hubzero error message.

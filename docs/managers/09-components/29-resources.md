<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/managers/components/resources
-->
# Resources

Resources are the hub's catalogue: tools, datasets, presentations, courses,
documents, and anything else worth putting on a shelf. Each has a type,
contributors, attachments, tags, and a set of tabs supplied by plugins.
Members submit them through a wizard at `/resources`; this chapter covers
the administrator's side, and the
[Hub users](../../users/21-resources.md) book covers contributing and browsing.

## The pipeline

This is the last of three components that belong together.

| Stage | Component | What it is |
|---|---|---|
| Work | [Projects](26-projects.md) | The private team workspace. |
| Release | [Publications](27-publications.md) | A frozen, versioned, citable release with a DOI. |
| Catalogue | **Resources** | The public shelf. What a visitor browses, searches and lands on from Google. |

What you decide at this stage is **what kinds of thing the hub catalogues,
what a contributor has to supply for each kind, and whether anybody checks a
submission before it is public**. Types answer the first two; the
**Auto-approve** option answers the third.

A resource is not a publication. Publishing a publication does not create a
resource, and nothing in the tree links the two records: they are separate
tables with separate browse pages and separate search plugins. A hub that
wants a released dataset on the catalogue shelf as well has to have someone
contribute it here. Nor is a resource a file store — the files belong to it,
but the thing being catalogued may be a link, a tool, or a recorded seminar
with no files at all.

A hub that publishes nothing and hosts nothing still usually wants this
component, because it is where seminars, teaching material and links to
outside work go.

## Where to find it

Open it under **Components > Resources**. The sub-menu across the top has
**Resources** (the list below), **Orphans**, **Types**, **Licenses**,
**Authors**, **Roles**, **Plugins** (only if you may manage plugins),
**Import**, and **Import Hooks**.

## The Resources list

This is the screen you live in. Its most useful setting is the **Status**
filter set to **Pending**: those are the submissions members have made and
nobody has looked at yet, and on a stock hub they sit there until you do.

The list shows *standalone* resources only — the ones that appear in
searches and listings. Child resources are reached through their parent, or
through **Orphans**.

Columns are **ID**, **Title**, **Status**, **Access**, **Modified**,
**License**, **Type**, **Children**, and **Tags**. Click a heading to sort.
Above the list, filter by search term, **Status**, **License**, and
**Type**, then press **Go**; **Clear** resets everything. A numeric search
term is treated as a resource ID; anything else is matched against the title
and the main text.

Six states are possible: **Draft (user created)**, **Draft (internal)**,
**Pending**, **Unpublished**, **Published**, and **Trashed**. A published
resource whose start date is still in the future shows as **Pending**, and
one past its finish date shows as **Expired**.

Clicking a value in the row acts on it directly. The **Status** cell toggles
between published and unpublished. The **Access** cell steps through
Public, Registered, Special, Protected, and Private, and wraps around. The
**Children** cell opens the child list, or **Add** starts a new child. The
**Tags** cell opens the tag editor for that resource.

The toolbar offers **Audit**, **Options**, **Add Child**, **Publish**,
**Unpublish**, **New**, **Edit**, and **Delete**.

> **Warning:** **Delete** is permanent. It also destroys the resource's
> non-standalone children, its links to any parents, and its entire upload
> directory. To take a resource off the site without losing it, set its
> status to **Unpublished** or **Trashed** instead.

### Reviewing a submission

A member has contributed a dataset through the wizard at `/resources`. On a
stock hub — **Auto-approve** ships **No** — it was saved as **Pending** and
is waiting for you. Nothing tells the member how long that takes, so the
first hub-running habit worth forming is checking this filter.

1. Go to **Components > Resources**.
2. Set **Status** to **Pending** and press **Go**.
3. Select the resource's title to open it. This checks the record out; if
   you wander off, come back and press **Cancel** rather than closing the
   tab, or the record stays checked out and nobody else can edit it.
4. Read the **Abstract/Description** and the **Main Text**, check the
   **Type** is right, and open the **Files** panel to see what was actually
   uploaded.
5. If something needs changing, fix it and press **Save**. There is no Save
   & Close on this form.
6. When you are satisfied, set **Status** to **Published** in the
   **Publishing** panel and press **Save**. You can do the same from the
   list by clicking the **Status** cell.

Publishing is reversible: set the status back to **Unpublished** and the
resource leaves the site with everything intact.

> **Note:** The contributor is not told. **Email When Published** ships
> **No**, so approving a submission sends nothing. Turn it on in
> [Options](#options) if you want contributors to hear back, or write to
> them yourself.

## Creating or editing a resource

**New** and **Edit** open the same form. It saves with **Save**; **Cancel**
checks the record back in and returns to the list. There is no Save & Close.

**Details**

| Field | Notes |
|---|---|
| Title | Required. |
| Type | Required. The main type; drives which custom fields and which tabs the resource gets. |
| Alias | The last segment of the resource URL. Left blank, one is generated from the title. |
| License | One of the licenses defined under **Licenses**, or `(none)`. |
| Location | Free text, stored as an attribute; shown in the resource's metadata. |
| Time | Free text date and time in `YYYY-MM-DD hh:mm:ss` form, for the event a seminar or lecture was recorded at. |
| Canonical | URL of a canonical version elsewhere. Tells search engines to prefer that address. |
| Abstract/Description | Required. The summary shown in listings and at the top of the page. |
| Main Text | The body of the resource page. |

Below **Details**, a **Custom fields** panel shows the extra fields defined
for the resource's type. They are stored inside the main text as `<nb:...>`
tags rather than in their own columns.

The panel on the right of a saved resource lists its **ID**, **Created**,
**Creator**, **Modified**, **Modifier**, **Ranking**, **Rating**, and
**Hits**. Each of the last three has a button that zeroes it, and **View**
next to the rating opens the individual ratings members have left.
**Contributors** below lets you add hub members or plain names as authors.

The rest of the form is a set of collapsible panels.

| Panel | Contents |
|---|---|
| Publishing | **Status**, **Group** (a hub group that owns the resource), **Access Level**, **Creator**, **Start Publishing**, **Finish Publishing**. |
| Access Control List | Extra members and groups granted access beyond the access level. |
| Files | An embedded file manager for the resource's upload directory, plus a **With selected** action — **Set as main file**, **Insert HTML: image**, or **Insert HTML: linked file** — applied with **Apply**. |
| Tags | A comma-separated tag list. |
| Badges | Badges to display alongside the resource. |
| Parameters | Per-resource overrides of the display options described under [Options](#options). |

> **Note:** **Finish Publishing** shows the word *Never* rather than an
> empty date when the resource has no end date. Leave it as it is to keep
> the resource published indefinitely.

A child resource has a different Details section — **Logical Type**,
**File/URL**, **Duration**, **Width**, **Height**, and **Attributes** —
and its Parameters panel offers only **Link action** (Default, New window,
Lightbox, Download) and **Restrict Direct Access** (Inherit from Type, No,
Yes).

> **Note:** Resources of the **Tools** type are created and versioned by the
> tool pipeline, not here. Their edit form shows a warning and exposes only
> **Alias** and **Canonical**.

## Children, series, and orphans

A resource can own other resources: the attachments on an ordinary
resource, or the members of a series or workshop. **Add Child** on the list
or in the toolbar asks how to add one — **Create new**, which opens a blank
child form, or **Add existing** with a **Resource ID** — and then **Next**.
Add one child at a time.

The **Children** screen lists a parent's children with their ID, Title,
Status, Access, Type, and ordering arrows, and adds **Add Child** and
**Remove Child** to the toolbar. **Remove Child** only unlinks the child
from its parent, which is how children end up in **Orphans** — the screen
listing every non-standalone resource with no parent, so you can re-attach
or delete them.

## Types

A type is the most consequential thing on this page, because it decides what
a contributor is asked for and what a visitor sees. Adding one is how you
make the hub catalogue something it does not yet catalogue — a set of
teaching notebooks, a series of instrument runs — and the **Custom Fields**
table is how you make sure every contribution of that kind arrives with the
metadata you will wish you had asked for.

Adding a type is safe. Editing one that resources already use is not: a
custom field renamed or removed does not migrate the values already stored
in existing resources, because they live inside the main text as `<nb:...>`
tags rather than in columns.

**Types** lists the resource types in one category at a time; the
**Category** filter defaults to **Main Types**, the types members can
contribute and browse. The other categories are **Logical Type**, **Sub
Type**, **Type**, and **Group**. Columns are ID, Title, Alias, Category, and
Published, and the Published cell toggles.

The type form has **Title** (required), **Alias**, **Category**,
**Contributable** ("Users can contribute this type from the front-end"),
**Collection** ("This type is a collection of internal resources"),
**Linked file action**, **Restrict Direct Access**, **State**, and
**Description**.

Beside it, a **Plugins** table lists every enabled `resources` plugin with
an **on**/**off** radio pair. This is what decides which tabs a resource of
this type gets.

> **Note:** Every plugin switch defaults to **off**. Enabling a resources
> plugin under **Plugins** only makes it available; you must also turn it on
> for each type that should show it.

The **Custom Fields** table at the bottom defines the extra fields members
fill in when they contribute this type. Each row has a **Field name**, an
**Input type** — Single-line text box, Textarea, Select list, Radio buttons,
Checkboxes, Hidden, or the pre-defined Date, Geo Location, and Language
List — a **Display in** target, a **Required** checkbox, and **Options** for
list-style fields. Drag the handle in the **Reorder** column to change the
order, and **+ Add new row** to add a field.

> **Note:** The stock types Seminars, Workshops, Documents, Tools, and
> Series cannot be selected for deletion, and their alias is read-only.

## Licenses, Authors, and Roles

Three small screens that only matter once contributors start arriving.
Licenses decide what a contributor may choose on the last step of the
wizard; Authors is how you fix a credit; Roles is how a hub distinguishes
an author from, say, a data collector.

**Licenses** holds the licenses offered on the last step of the
contribution wizard. Each has a **Title**, an **Alias**, an optional
**URL**, and the license **Content** shown to the contributor.

**Authors** lists everyone credited on a resource and the hub account they
are linked to. Editing one shows every resource they are credited on and
lets you correct the **Name**, **Organization**, and **Role** on each, or
re-point the author to a different account with the **ID** field.

**Roles** defines the roles a contributor can hold besides plain **Author**,
which is the default and always available. A role has a **Title**, an
**Alias**, and a list of the resource types it applies to; a contributor on
a resource of one of those types can then be given that role. No roles are
installed by default, so a new hub has only Author until you add some.

## Plugins

**Plugins** is a filtered view of the site's plugin manager showing the
`resources` group. Publish, unpublish, and reorder them here, or open one to
edit its parameters. A few add their own management screen, reached from the
**Manage** column.

## Import

This is for the hub that inherits a catalogue: a departmental listing being
moved onto the hub, or a feed from a repository somewhere else. Loading a
dozen resources by hand is quicker than writing the data file. Loading three
hundred is not.

The importer bulk-loads resources from a data file, creating the parent
resource, its tags, its children, and its contributors. Each import has a
**Name**, **Notes**, a **Data File**, and a **Mode** — **UPDATE**, which
replaces a matched record wholesale, or **PATCH**, which overwrites only the
incoming fields.

**Import Parameters** set the **Status**, **Access**, and **Group** of the
imported resources, whether to **Match by Title** against existing
resources (a roughly 90% title match), and whether to **Check Required
Custom Fields**, which fails a record that is missing one without stopping
the rest of the run.

**Import Hooks** are reusable PHP scripts that run against one record at a
time at three points: **Post Parse**, after a record is read; **Post Map**,
after it is mapped to resource objects; and **Post Convert**, after it is
saved. Manage them under **Import Hooks** and attach them to an import from
its form.

Run an import with **Run**, or with **Test Run** for a dry run that
exercises the hooks and settings but writes nothing. Do the dry run first.
**Help** on the toolbar opens the importer's own reference page. If a data
file is too large to upload through the browser, save the import first; the
form then shows the file space path to copy the file into.

## Audit

A catalogue rots. Links to outside pages go dead and uploaded files get
moved off the filesystem, and neither leaves a trace on the resource itself.
Run this once a term on a hub with any external links in it.

**Audit** on the Resources toolbar runs a check over every resource and
reports how many **passed**, **failed**, and were **skipped**. One test
ships, **Link Checker**: it takes each resource's file or URL, requests
external addresses and checks that uploaded files still exist under the
upload path. Click a count to list the entries behind it.

## Options

The one setting on this screen that changes how the hub behaves rather than
how it looks is **Auto-approve** on the **Creation** tab. It ships **No**,
in both the manifest and the shipped install row, and that is the sensible
default: submissions wait as **Pending** until someone publishes them. Set
it to **Yes** only if you know who is contributing and trust all of them,
because there is no undo for what a stranger puts on your front page in the
meantime. **Auto-approved Users** on the same tab is the middle course — a
comma-separated list of usernames whose submissions publish immediately
while everyone else's still waits.

> **Note:** The Publications component is set the other way round. Its
> auto-approve ships **on**, so a hub running both has a catalogue that
> queues submissions and a publication pipeline that does not — see
> [Curation](27-publications.md#curation).

**Options** opens the component-wide settings, grouped as Basic, Files,
Creation, Entry, and Import. They set the default resource picture and the
default custom fields for new types; the upload paths and size limit;
whether submissions are auto-approved, who is notified when one arrives,
whether contributors are emailed when one is published, and whether a
license is offered; and which parts of a resource page are shown. Every
option is listed with its values in the
[configuration reference](../../reference/configuration/components/resources.md).

> **Warning:** Two of the Files options do less than they appear to.
> **Extensions** is not read anywhere in the component, so it does not
> restrict what members may upload, and **Max upload** applies only to the
> drag-and-drop uploader on the Attach step — every other upload path is
> bounded by PHP's own `upload_max_filesize`. Set the real limits in the
> server configuration.

## Permissions

The **Permissions** tab in Options sets who may administer and manage the
component, and who may create, delete, edit, change the state of, and edit
their own resources. Three further sets of the same actions apply to
resources owned by a group (*For Group*), to resource types (*Type*), and,
as separate asset sections, to individual resources, types, roles, and
licenses.

## API

Read-only endpoints under `/api/resources` list resources, autocomplete
titles, and fetch recent content; see the
[API reference](../../reference/api/resources.md).

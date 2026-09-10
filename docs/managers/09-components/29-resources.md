<!--
status: rewritten
reviewed-against: 2.4-main @ d48e29db14
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/managers/components/resources
-->
# Resources

Resources are the hub's catalogue: tools, datasets, presentations, courses,
documents, and anything else worth putting on a shelf. Each has a type,
contributors, attachments, tags, and a set of tabs supplied by plugins.
Members submit them through a wizard at `/resources`; this chapter covers
the administrator's side, and the
[Hub users](../../users/21-resources.md) book covers contributing and browsing.

Open it under **Components > Resources**. The sub-menu across the top has
**Resources** (the list below), **Orphans**, **Types**, **Licenses**,
**Authors**, **Roles**, **Plugins** (only if you may manage plugins),
**Import**, and **Import Hooks**.

## The Resources list

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

**Audit** on the Resources toolbar runs a check over every resource and
reports how many **passed**, **failed**, and were **skipped**. One test
ships, **Link Checker**: it takes each resource's file or URL, requests
external addresses and checks that uploaded files still exist under the
upload path. Click a count to list the entries behind it.

## Options

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

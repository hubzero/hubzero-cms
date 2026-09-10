<!--
status: rewritten
reviewed-against: 2.4-main @ d48e29db14
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/managers/components/publications
-->
# Publications

Publications are versioned, citable releases of research output — datasets,
papers, presentations, code. A member assembles one inside a project, a
curator reviews it, and once approved it gets a DOI and a permanent page at
`/publications/<id>`. This chapter covers the administrator's side. The
[Hub users](../../../users/18-publications/README.md) book covers writing and
curating one, and [DOI registration](datacite.md) covers the identifier
service.

Open it under **Components > Publications**. Six sub-menu links sit at the
top left: **Publications**, **Licenses**, **Categories**, **Master Types**,
**Batch Create**, and **Plugins** — the last opens the Plugins component
filtered to the `publications` folder, and only appears if you may manage
plugins.

> **Note:** The component ships with **Component ON/OFF** set to Off, and the
> Publications list then shows the banner *This component is currently
> disabled and is inaccessible to end users.* The banner is all that is left
> of the option: the front-end code that redirected `/publications` to the
> Resources component is commented out, so the site works either way. What
> actually decides whether members can publish is the `projects` Publications
> plugin.

## Publications

The **Publication Manager** lists every publication version. Columns are
**ID**, **Title**, **@v.** (the version label), **Status**, **Project**,
**Releases** (a link to that publication's version list), **Master Type/
Category**, and **Last Modified**. ID, Title, and Project sort. Filter by a
search term, by **Status**, or by **Category**, then press **Go**. Rows
awaiting review are highlighted; a row another administrator has open shows
**[Checked out]** in place of its checkbox, and a key under the list explains
the status colours.

The toolbar carries **Options**, **Edit**, and **Delete**. There is no
**New** button — publications can only be started from the front end.
Deleting removes the checked publications and all of their versions,
including the files on disk; the principal version of a multi-version
publication cannot be deleted on its own.

## Editing a publication

Clicking a title opens one version for editing and checks it out. The left
column holds the content, the right column the record's metadata and
publishing controls.

| Field | Notes |
|---|---|
| Title | Required. |
| Category | Required. Chosen from the contributable categories. |
| Alias | Optional. Used in place of the numeric ID in the publication's URL. |
| Synopsis | Short plain-text summary. Stored in the version's `abstract` column. |
| Abstract | The long description, in the editor. Stored in the version's `description` column. |
| Metadata | The custom fields defined by this publication's master type. Shows *No metadata collected* when the type defines none. |
| Release Notes | What changed in this version. |
| Authors | Reordered by dragging. **Add author** picks a hub member or takes details for someone without an account. |
| Tags | Comma-separated. |
| License type / License text | One of the active licenses, and its wording. |
| Disable download Link | Hides the download button on the publication page. |

> **Note:** The labels do not line up with the columns behind them:
> **Synopsis** writes to `abstract` and **Abstract** writes to `description`.
> The publication page shows the **Abstract** field as its body text.

The right column shows the ID, creation date and creator, owning project, and
master type; for published and unpublished versions it also shows **Ranking**
and **Rating**, each with a button that resets the score. **Version** holds
the version ID, an editable **Version** label, the public **URL**, and who
last modified the record. **Publishing** decides whether the version is
visible:

| Field | Notes |
|---|---|
| Status | draft, ready, pending approval, changes required, published, unpublished, or deleted. |
| Unpublished Reason | Enabled only when the status is unpublished: *The dataset is not available anymore*, *The dataset has error within it*, or *Others* with a free-text reason. Shown on the tombstone page. |
| Featured | Whether the publication is flagged as featured. |
| Access | Public, Registered, or Private. |
| Group owner | Assigns a hub group as owner. |
| Publish Date / Unpublish Date | Embargo window. A future publish date holds the version back. |
| DOI | The registered identifier, editable by hand. |
| Archival Package | **produce archival package** builds the version's bundle, or serves the existing one with a **[Repackage]** link beside it. |

**Manage Publication** holds the actions that also email the authors: **Send
message**, and — depending on the status — **Unpublish version**,
**Republish version**, or **Approve and publish** and **Revert to draft**.
Approving stamps the accepted date, registers the DOI if the master type
requires one, freezes the curation manifest onto the version, and runs the
archival packaging.

The **Parameters** slider shows or hides individual sections of the public
page for this one publication: authors, audience, gallery, tags, license,
notes, metadata, and submitter. Values default to the master type's setting.

**Releases** in the list opens the **Versions** screen: one row per version
with its ID, label, title, status, DOI, and a **manage version »** link.

## Licenses

Licenses are the choices authors get when they license their work. The list
shows **ID**, **Name**, **Title**, **Status**, **Default**, and **Order**,
with a search box over titles. The toolbar adds **New**, **Edit**, **Make
Default**, **Publish/Unpublish**, and **Delete**; a license a publication
already uses cannot be deleted.

**License Information** takes a **Title** and **Name** (both required; the
name is generated from the title if blank), a **URL** to the full text, an
**About** blurb (required), the license **Content**, and an **Icon** path.
**License Configuration** holds four yes/no settings: **Active** (offered as
a choice for new publications), **Customizable** (authors may edit the text),
**Agreement required**, and **Allow Derivatives**.

## Categories

Categories group publications for browsing, and each carries a Dublin Core
type used in exported metadata. The list shows **ID**, **Name** with its
alias, URL alias and dc:type underneath, **Contributable**, and **Status**,
with **New**, **Edit**, **Change Status**, and **Delete** on the toolbar.

**Category Information** takes a **Name** (required), an **Alias**, a **URL
alias** used in `/publications/<url alias>`, a **dc:type** chosen from the
twelve Dublin Core types, and a short **About**. **Item Configuration** sets
the **Status** (Active or Inactive) and whether the category is
**Contributable**.

**Master Type Configuration** decides, per master type, whether this category
is offered when someone publishes that type. **Plugins** turns each installed
`publications` plugin on or off for this category — that is how a category
gains or loses its extra tabs. **Custom Fields** defines extra metadata
inputs, each with a name, input type, and required flag.

## Master Types

A master type is the blueprint for a kind of publication: which panels the
author fills in, in what order, what each one accepts, and whether a DOI is
required. The list shows **ID**, **Name**, **Alias**, **Contributable**, and
**Order**, with **New**, **Edit**, and **Delete** on the toolbar and reorder
arrows in the last column.

Opening a type shows **Master Type Information** — **Name**, **Alias**,
**Description**, **Contributable** — and **Curation Configuration**:

| Field | Notes |
|---|---|
| Curator Group | A hub group whose members see publications of this type in the curation list. |
| Default Publication Title | Title given to a new draft. |
| Default Category | Category assigned to a new draft. |
| Require DOI? | *Do not require or show DOI*, *Require DOI*, or *Offer choice to publish with DOI or post without DOI*. |
| Bundle display and behavior | *Do NOT bundle*, *Always produce and serve a bundle, regardless of file count*, or *Bundle only in the case of multiple primary files*. |
| List all publication contents on the main publication page? | Yes or No, with a **Label for the list of publication contents**. |
| Auto Approve? | Publish submissions of this type without review. |
| Request review? (when set to auto approve) | Offer the author a box asking for a curator anyway. |

> **Note:** A new master type must be saved before any of this appears — the
> panel shows *New type needs to be saved before curation can be configured*
> until it has an ID.

**Blocks Configuration** lists the panels the author walks through, in order.
Each block switches **Active** or **Inactive** and carries a **Short label**,
**Title**, **Panel heading (draft)**, **Panel tagline (draft)**, **User
tips**, and **Admin tips**, plus its own parameters — *Input required*,
*Editing allowed in published status?*, the license IDs to offer. **Block
Elements** lists what the block collects; **[Edit]** sets each element's
label, instructions, attachment **Type** (file, link, data, or Publication
(link)), **Attachment role** (Primary, Supporting, or Gallery), **Minimum
count** and **Maximum count**, allowed extensions, and how files are copied
out of the project.

**[Add a block]** adds one, **[Edit]** beside a block's order opens the
drag-to-rearrange screen, and **[Delete]** confirms before removing the block
and everything authors entered into it. **Advanced curation editing** exposes
the manifest as raw JSON.

> **Note:** The element editor warns that *Elements editing is not fully
> featured in this hub release.* A malformed manifest falls back to the basic
> Files manifest.

**Type supported?** reports whether a `projects` plugin with the same alias
is installed and enabled. A type marked off cannot actually be produced.

## Batch Create

**Batch Create** imports publications from an XML file. Choose the project
and the master type, attach the file, and press **Process data (you'll have a
chance to review)**. The file is validated against a schema you can download
from the **schema** link; records are then shown for review — with missing
licenses, unknown user IDs, and missing files flagged — before anything is
written.

## Curation and scheduled jobs

Curation is not an administrator screen. Members of the group named in the
**Name of Curators Group** option review submissions at
`/publications/curation` on the site — see
[Curation](../../../users/18-publications/curation.md).

> **Note:** Curation email — the note to a newly assigned curator, the
> approval and change-request notices to authors, the new-submission notice
> to curators — is only sent when **Email Notifications** is On. It is Off by
> default, and nothing warns you that the messages are being dropped.

> **Note:** A master type's own **Curator Group** narrows what its members
> see in the curation list, but it is stored as a group ID and compared
> against group names when the review screen checks permission, so on its own
> it does not let them open a submission. Put type curators in the
> component-wide curators group, or assign them to individual publications.

Several background tasks belong to the component and have to be scheduled in
[Cron](../12-cron.md): `runMkAip`, `issueMasterDoi`, `buildPublicationBundles`
(needed when asynchronous bundling is on), `updateFtpLinks`,
`sendAuthorStats`, and `rollUserStats`.

## Options

**Options** opens the component-wide settings, on six tabs. **Basic** holds
the on/off switch, decides whether members may publish without a project, and
sets the default thumbnails and storage paths. **Curation** names the
curators group, the reply address for curation email, the grace period, and
the auto-approved users. **DOI** configures the identifier service — see
[DOI registration](datacite.md). **Sections** shows or hides parts of the
public publication page, and **AIP** points at a trusted digital repository's
archival storage. Every option is listed with its values in the
[configuration reference](../../../reference/configuration/components/publications.md).

The **Permissions** tab controls who may configure the component
(`core.admin`), reach the administrator screens (`core.manage`), and create,
edit, change the state of, or delete publications. Permissions are read at
the component level only: the code asks for actions on `com_publications`
itself, so per-category and per-publication rules have no effect here.

## API

`GET /api/publications/list`, in two versions, returns the publications the
authenticated member is an author of — see the
[API reference](../../../reference/api/publications.md).

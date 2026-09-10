<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/managers/components/publications
-->
# Publications

Publications are versioned, citable releases of research output — datasets,
papers, presentations, code. A member assembles one inside a project, a
curator reviews it if the hub is set up to require that, and on approval it
gets a DOI and a permanent page at `/publications/<id>`. This chapter covers the administrator's side. The
[Hub users](../../users/18-publications.md) book covers writing and
curating one, and [DOI registration](#doi-registration) covers the identifier
service.

> **Warning:** On a stock hub nothing is curated. The shipped install turns
> auto-approve on, and a member's submission goes straight to published with
> no curator involved. If you believe review is happening, read
> [Curation](#curation) before anything else on this page.

## The pipeline

This is the middle stage of three components that belong together.

| Stage | Component | What it is |
|---|---|---|
| Work | [Projects](26-projects.md) | The private team workspace the material comes out of. |
| Release | **Publications** | A frozen, versioned, citable slice of that work, with a DOI. |
| Catalogue | [Resources](29-resources.md) | The public shelf a visitor browses at `/resources`. |

What you decide at this stage is **what a release has to contain, whether
anyone checks it before the world sees it, and whether it gets a DOI**. Those
are the three questions, and they map onto three screens: [Master
Types](#master-types) for the contents, [Curation](#curation) for the check,
and [DOI registration](#doi-registration) for the identifier.

A publication is not a resource. Nothing in the tree creates a resource
record from a publication, and the two live in separate tables with separate
browse pages. It is also not a file share: it is a fixed set of files, a
citation, and a metadata record, and once published its files do not change
without a new version.

Publications need Projects. A publication is assembled inside a project's
**Publications** tab, so if the `projects` Publications plugin is disabled
nobody can make one, whatever this component's settings say.

## Where to find it

Open it under **Components > Publications**. Six sub-menu links sit at the
top left: **Publications**, **Licenses**, **Categories**, **Master Types**,
**Batch Create**, and **Plugins** — the last opens the Plugins component
filtered to the `publications` folder, and only appears if you may manage
plugins.

> **Note:** **Component ON/OFF** does nothing. The manifest declares it Off
> and the shipped install row sets it On, and the stored row is what runs, so
> a hub installed from this release has it On. Setting it Off changes one
> thing: the Publications list grows the banner *This component is currently
> disabled and is inaccessible to end users.* The banner is all that is left
> of the option — the front-end code that redirected `/publications` to the
> Resources component is commented out, so the site works either way. What
> actually decides whether members can publish is the `projects`
> Publications plugin.

## Publications

You come here to fix or withdraw something, not to make one — there is no
**New** button and there cannot be, because a publication is assembled in a
project. The common errands are approving a submission when the curators
are away, unpublishing a dataset that turned out to be wrong, and correcting
a DOI.

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

Whether your hub needs to touch this depends on your institution. The
shipped list covers the usual open licences; add one when your legal office
requires particular wording, or when data comes with terms of use a
depositor must agree to. **Agreement required** is the setting that turns a
licence into a click-through the depositor cannot skip.

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

Categories do two jobs that are easy to confuse. They group publications for
browsing on the site, and each one carries the Dublin Core type that goes
into the DOI metadata record as `resourceType` — so choosing the wrong
**dc:type** misdescribes the work to every system that reads the DOI, not
just to visitors.

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

This is where you decide what a release of a given kind has to contain
before anyone can call it published: which panels the author fills in, what
each panel accepts, how many files of what sort, and whether a DOI is
required. A hub that publishes datasets and a hub that publishes teaching
material want different answers, and a master type is how you give them.

Editing a type is not a small change. Blocks and elements are read at
submission time, so a block you make required starts blocking drafts that
are already half-written, and **[Delete]** on a block removes everything
authors have entered into it. Add a type and experiment on that rather than
reworking one people are using.

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
| Auto Approve? | Publish submissions of this type without review. On is enough on its own, whatever **Options → Curation → Auto-approve** says — see [Curation](#curation). |
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

Use this when a hub inherits a collection — a departmental data archive
being moved onto the hub, say — and creating each publication by hand is not
practical. For anything under a dozen records it is more work than doing it
by hand, because the XML has to validate first.

**Batch Create** imports publications from an XML file. Choose the project
and the master type, attach the file, and press **Process data (you'll have a
chance to review)**. The file is validated against a schema you can download
from the **schema** link; records are then shown for review — with missing
licenses, unknown user IDs, and missing files flagged — before anything is
written.

## Curation

### Nothing is curated until you change a setting

Start here. A hub installed from this release publishes every submission the
moment its author presses the button. No curator sees it, no approval is
recorded, and the DOI is minted and made findable. This is not a bug you
will notice, because the publication appears and looks correct; it is only
wrong if you believed someone was checking.

Two switches cause it, and **either one being on is enough**:

| Switch | Where | Manifest default | What ships |
|---|---|---|---|
| **Auto-approve** | **Options → Curation** | No | **Yes** |
| **Auto Approve?** | [Master Types](#master-types) → a type's Curation Configuration | No | No |

The component manifest declares **Auto-approve** off. The shipped install row
sets it on, and the stored row is what runs, so the screen you have is set to
Yes and the screen the documentation was written against was set to No. The
submission code asks for either:

```php
if (!$review && ($autoApprove || $this->_pubconfig->get('autoapprove') == 1))
{
	$state = 1;
}
```

— [`publications.php:2216`](../../../core/plugins/projects/publications/publications.php),
where `$autoApprove` is the master type's own setting and `$review` is the
author's own request for a review. `$state = 1` is published.

There is a third bypass below it: **Auto-approved Users** on the same
Options tab is a comma-separated list of usernames whose submissions publish
without review even when both switches are off. It ships empty.

### Turning curation on

Do this on a new hub before anyone publishes, because it does not
retrospectively review what is already out.

1. Create the group whose members will curate, and note its alias. A group
   with no members curates nothing, and there is no fallback to
   administrators.
2. Go to **Components > Publications** and select **Options**.
3. On the **Curation** tab, set **Auto-approve** to **No**.
4. Put the group's alias in **Name of Curators Group**. It ships empty.
5. Set **Reply Email Address** to an address someone reads. Left blank, the
   messages come from the site's global *From email* address.
6. Go to the **Basic** tab and set **Email Notifications** to **On**. Leave
   it Off and every curation message is silently dropped — see the note
   below.
7. Select **Save & Close**.
8. Go to **Master Types**, open each type in turn, and check that **Auto
   Approve?** is **No**. One type left on Yes exempts every publication of
   that type.

From then on a submission lands at *pending approval* and waits at
`/publications/curation` on the site. Curation is not an administrator
screen: it is a front-end screen the curators group reaches — see
[Curation](../../users/18-publications.md#curation). An administrator can
still approve from [Editing a publication](#editing-a-publication) with
**Approve and publish**.

Turning auto-approve back on is equally quick and equally undramatic:
submissions already waiting stay waiting; only new ones skip the queue.

### What else to check

> **Note:** Curation email — the note to a newly assigned curator, the
> approval and change-request notices to authors, the new-submission notice
> to curators — is only sent when **Email Notifications** is On. It is Off by
> default, and nothing warns you that the messages are being dropped. A hub
> that turns curation on and leaves this Off has a queue nobody is told
> about.

> **Note:** A master type's own **Curator Group** narrows what its members
> see in the curation list, but it is stored as a group ID and compared
> against group names when the review screen checks permission, so on its own
> it does not let them open a submission. Put type curators in the
> component-wide curators group, or assign them to individual publications.

> **Note:** **Grace Period for Changes** on the same tab offers *None* or
> *One month*, and it does two things at once. It lets an author revert a
> published version to draft and change it within a month of approval, and
> it holds back the archival package: with no grace period the package is
> built at approval, and with one it waits for the `runMkAip` cron job. Turn
> it on and you must schedule that job, or nothing is ever archived.

## Scheduled jobs

Several background tasks belong to the component and have to be scheduled in
[Cron](12-cron.md): `runMkAip`, `issueMasterDoi`, `buildPublicationBundles`
(needed when asynchronous bundling is on), `updateFtpLinks`,
`sendAuthorStats`, and `rollUserStats`. None of them run unless you create
the cron jobs; a hub that produces archival packages or master DOIs and has
scheduled nothing simply never produces them.

## Options

**Options** opens the component-wide settings, on six tabs. Two of them
change what the hub does; the rest change how it looks.

**Basic** holds the on/off switch, decides whether members may publish
without a project, sets the default thumbnails and storage paths, and
carries **Email Notifications** — which is Off by default and silently drops
every curation message. **Curation** names the curators group, the reply
address for curation email, the grace period, and the auto-approved users,
and holds **Auto-approve**, which ships **Yes**: read
[Curation](#curation) before you leave that tab. **DOI** configures the identifier service — see
[DOI registration](#doi-registration). **Sections** shows or hides parts of the
public publication page, and **AIP** points at a trusted digital repository's
archival storage. Every option is listed with its values in the
[configuration reference](../../reference/configuration/components/publications.md).

The **Permissions** tab controls who may configure the component
(`core.admin`), reach the administrator screens (`core.manage`), and create,
edit, change the state of, or delete publications. Permissions are read at
the component level only: the code asks for actions on `com_publications`
itself, so per-category and per-publication rules have no effect here.

## API

`GET /api/publications/list`, in two versions, returns the publications the
authenticated member is an author of — see the
[API reference](../../reference/api/publications.md).
## DOI registration

Every published version can be given a DOI — a permanent identifier that
resolves to its page and carries a metadata record other systems can read.
Hubzero registers DOIs through one of two services, DataCite or EZID, chosen
on the **DOI** tab of the Publications [Options](#options). This
chapter describes what the component sends and what you have to configure to
make it work.

### Choosing a service

The **DOI Service** option has three values:

| Value | Effect |
|---|---|
| None | No DOIs are issued. Publishing a version that requires one fails with *No DOI Service Activated*. |
| EZID | Identifiers are minted through an EZID endpoint. |
| DataCite | Identifiers are minted directly against the DataCite Metadata Store (MDS) API. |

The form hides the fields that do not apply to the service you pick. The
component ships set to **EZID**, so a hub that means to use DataCite has to
change this explicitly.

> **Note:** DOI registration only switches on when the service is selected
> **and** the credentials are complete: a DataCite setup needs **DOI
> Namespace Start**, **DataCite DOI Service Url**, and **DataCite DOI Service
> User:Password**; an EZID setup needs **DOI Namespace Start**, **DOI
> Namespace End**, **EZID DOI Service Url**, and **EZID DOI Service
> User:Password**. If any of those are blank the component behaves as if no
> service were configured, without saying so on the Options screen.

### What to configure

| Option | Notes |
|---|---|
| About DOI link | A page explaining what DOIs are. Linked from the publication page. |
| DOI Namespace Start | The prefix your registrar assigned, for example `10.5072`. Required. |
| DOI Namespace End | The hub-specific shoulder that follows the slash, for example `F2K`. Used by EZID. |
| DataCite DOI Service Url | The MDS base address — `https://mds.datacite.org` in production, `https://mds.test.datacite.org` for testing. |
| DataCite DOI Service User:Password | The MDS credentials, as one `user:password` string. Sent over HTTP basic authentication, so the address must be `https`. |
| EZID DOI Service Url / User:Password | The equivalent pair for EZID. |
| DOI XML Schema | The schema URL that generated metadata is validated against before it is sent to EZID. Defaults to the DataCite kernel-4 schema. |
| DOI Publisher | The `publisher` element in the metadata. Defaults to the site name. |
| Publisher Identifier, Scheme, Scheme URI | Optional re3data identifier for the publisher, written as attributes on `publisher`. |
| DOI Resolve Url | Prefix used when the hub renders a DOI as a link. Defaults to `https://doi.org/`. |
| DOI Verification Url | Prefix behind the `[→]` link beside a DOI in the project's publication status panel. |
| Issue master DOI for publication? | Mints one extra DOI per publication that points at `/publications/<id>/main` rather than at a single version. |

### What gets registered

A DOI is minted when the author submits the draft, not when a curator
approves it: the submission builds a DataCite kernel-4 `resource` document
from the publication and asks the service for an identifier. Approval then
re-sends the metadata and registers the target URL. (An administrator who
approves from the **Publications** screen mints the DOI at that point
instead, if the version does not already have one.) The component refuses to
build a document unless the publication year, publisher, resource type,
title, a creator, and the target URL are all present.

The document carries:

- `identifier` — the DOI, left for the service to mint on a first
  registration.
- `creators` — every author, as `Last, First`, with `givenName`,
  `familyName`, an ORCID `nameIdentifier` where the member has one, and an
  `affiliation` carrying a ROR identifier where one is recorded.
- `titles`, `publisher`, `publicationYear`.
- `contributors` — the corresponding authors chosen on the review screen,
  as `ContactPerson`.
- `dates` — `Available`, `Submitted`, and `Accepted`.
- `language`, and `resourceType` taken from the publication category's
  **dc:type**.
- `relatedIdentifiers` — `IsNewVersionOf` and `IsPreviousVersionOf` links to
  the neighbouring versions' DOIs, plus the citations the author entered,
  mapped to DOI, PURL, Handle, ARK, arXiv, URN, or URL as their form allows.
- `version`, `rightsList` (the chosen license and its URL), and
  `fundingReferences` from the grant fields.
- `formats` and `sizes` — the MIME types, file count, and total size of the
  attached content.
- `subjects` — the publication's tags, with Library of Congress and Fields of
  Science and Technology schemes recognised by tag description prefixes.
- `descriptions` — the abstract, plus the synopsis as an `Other` description.

The target URL is always `<your hub>/publications/<id>/<version number>`.

### How DataCite registration runs

DataCite registration is two calls, both `PUT` with HTTP basic
authentication:

1. On submission, the metadata goes to
   `<service url>/metadata/<namespace start>`. Because the URL ends at the
   prefix, DataCite mints the DOI itself and returns it; the DOI is in
   *draft* state at this point.
2. On approval, the DOI and its target URL go to `<service url>/doi/<doi>`,
   which moves it to *findable*. The metadata is re-sent first, to
   `<service url>/metadata/<doi>`.

Reverting a published version to draft sends `DELETE` to
`<service url>/metadata/<doi>`, which puts the DOI back in *registered* state
so it stops resolving publicly; approving it again restores it. Unpublishing
a version instead re-sends its metadata and repoints the DOI at the
publication's tombstone page, so the identifier keeps resolving and explains
the retraction.

EZID registration is a single call that posts the identifier's metadata and
target together, after validating the generated XML against the **DOI XML
Schema**. A validation failure is reported but does not stop the
registration — the identifier is created without the DataCite metadata block.

> **Note:** Nothing in the administrator interface tests your credentials. If
> a registration fails, the publish attempt stops with the service's error on
> the message bar; DataCite's
> [testing guide](https://support.datacite.org/docs/testing-guide) and the
> `mds.test.datacite.org` endpoint are the way to check a new configuration.

### Master DOIs

A master DOI is a second identifier that belongs to the publication rather
than to one of its versions, and points at `/publications/<id>/main`. That
URL redirects to the publication's **Versions** panel, so a master DOI
resolves to the list of releases rather than to any one of them. Master DOIs
are issued by the `issueMasterDoi` cron job, which has to be scheduled in the
Cron component; nothing issues them on publish.

> **Warning:** In this release the job does not work. It asks the DOI model
> to register without telling it what to register, so neither the DataCite
> nor the EZID path sends anything and no identifier comes back. Turning
> **Issue master DOI for publication?** on has no effect until that is
> fixed.

### When DOIs are required

Whether a publication needs a DOI at all is a property of its master type,
not of the component: **Require DOI?** in a type's Curation Configuration
offers *Do not require or show DOI*, *Require DOI*, or *Offer choice to
publish with DOI or post without DOI*. With the third setting the author
chooses on the review screen between **Publish draft**, which mints a DOI,
and **Post draft**, which does not and leaves the publication editable and
unlisted.

### Further reading

- [DataCite MDS API guide](https://support.datacite.org/docs/mds-api-guide)
- [DataCite metadata schema](http://schema.datacite.org/)
- [DOI states](https://support.datacite.org/docs/doi-states)

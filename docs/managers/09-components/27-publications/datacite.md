<!--
status: rewritten
reviewed-against: 2.4-main @ d48e29db14
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/managers/components/publications/datacite
-->
# DOI registration

Every published version can be given a DOI — a permanent identifier that
resolves to its page and carries a metadata record other systems can read.
Hubzero registers DOIs through one of two services, DataCite or EZID, chosen
on the **DOI** tab of the Publications [Options](README.md#options). This
chapter describes what the component sends and what you have to configure to
make it work.

## Choosing a service

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

## What to configure

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

## What gets registered

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

## How DataCite registration runs

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

## Master DOIs

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

## When DOIs are required

Whether a publication needs a DOI at all is a property of its master type,
not of the component: **Require DOI?** in a type's Curation Configuration
offers *Do not require or show DOI*, *Require DOI*, or *Offer choice to
publish with DOI or post without DOI*. With the third setting the author
chooses on the review screen between **Publish draft**, which mints a DOI,
and **Post draft**, which does not and leaves the publication editable and
unlisted.

## Further reading

- [DataCite MDS API guide](https://support.datacite.org/docs/mds-api-guide)
- [DataCite metadata schema](http://schema.datacite.org/)
- [DOI states](https://support.datacite.org/docs/doi-states)

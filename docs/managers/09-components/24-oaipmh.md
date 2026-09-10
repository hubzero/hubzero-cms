<!--
status: rewritten
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
-->
# OAI-PMH

The OAI-PMH component publishes the hub's publications and resources as
machine-readable metadata at `/oaipmh`, so that repository aggregators,
library discovery services, and other harvesters can index them. It has no
visitor-facing pages and nothing a member ever sees. A manager touches it
twice: once to fill in who the repository claims to be, and once more if a
harvester complains.

The protocol it speaks is the Open Archives Initiative Protocol for Metadata
Harvesting, version 2.0. A harvester asks the endpoint for a list of records
and comes back later for whatever has changed; the hub answers in XML.

## The administrator screens

**Components → OAIPMH** has two screens and a link out to a third.

**About** is the component's own help text — an explanation of the protocol,
a note about where records come from, and a hand-harvesting cheat sheet.
Nothing on it is editable. **Options** and **Help** are the only toolbar
buttons, and **Options** appears only for someone with `core.admin` on the
component.

**Schemas** lists the metadata formats compiled into the component, with the
`metadataPrefix` a harvester passes to ask for each:

| Name | Prefix | Format call |
|---|---|---|
| Dublin Core | `oai_dc` | `&metadataPrefix=oai_dc` |
| Qualified Dublin Core | `oai_qdc` | `&metadataPrefix=oai_qdc` |

The list is read from the classes in
[`core/components/com_oaipmh/models/schemas`](../../../core/components/com_oaipmh/models/schemas);
there is nothing to add or configure on the screen.

**Plugins** is a shortcut into the Plugin Manager, filtered to the `oaipmh`
group. It is only shown to someone who can manage plugins.

Reaching any of this needs `core.manage` on `com_oaipmh`.

## What the endpoint answers

The endpoint is `/oaipmh`, and it takes the six OAI-PMH verbs:

| Verb | What it returns |
|---|---|
| `Identify` | The repository's name, base URL, admin address, earliest datestamp, deleted-record policy, and granularity — all of them from **Options**. |
| `ListMetadataFormats` | The two schemas above. |
| `ListSets` | One set per publication category and per resource type. |
| `ListIdentifiers` | Record identifiers only. Needs `metadataPrefix`. |
| `ListRecords` | Identifiers and metadata. Needs `metadataPrefix`. |
| `GetRecord` | One record. Needs `metadataPrefix` and `identifier`. |

`ListIdentifiers` and `ListRecords` also take `from` and `until` dates and a
`set`. Long answers are cut at **Result Limit** and continued with a
`resumptionToken`, which the harvester passes back to get the next page.

```text
https://yourhub.org/oaipmh?verb=ListRecords&metadataPrefix=oai_dc
https://yourhub.org/oaipmh?verb=ListRecords&metadataPrefix=oai_dc&set=publications:datasets
https://yourhub.org/oaipmh?verb=GetRecord&metadataPrefix=oai_dc&identifier=https://yourhub.org/publications/42/1
```

A record's identifier is its own URL — `/publications/{id}/{version}` or
`/resources/{id}` — and `GetRecord` also accepts the record's DOI, with or
without the resolver prefix in front of it.

Opening any of these in a browser shows a readable page rather than raw XML:
the response carries an XSL stylesheet, served from `/oaipmh/stylesheet`,
which the browser applies.

## Where the records come from

The component holds no content of its own. Each content type is supplied by a
plugin in the `oaipmh` group, managed under
**Extensions → Plugins** (see [Plugins](../10-extensions/03-plugins.md)).
Two ship, and both are enabled on a new hub:

| Plugin | Supplies | Sets it declares |
|---|---|---|
| **OAIPMH - Publications** | Published versions of [publications](27-publications.md) | `publications:{category alias}` |
| **OAIPMH - Resources** | Published, standalone [resources](29-resources.md) | `resources:{type alias}` |

Each has a filter parameter. **Publication Category** on the publications
plugin and **Resource Type** on the resources plugin narrow the component to
a single category or type; left unset, the publications plugin offers every
publication category, and the resources plugin offers the resource types in
the default type category — the same set **Components → Resources → Types**
opens on. **Include Citations** on the resources plugin adds a resource's
citations to its record as references.

Disable both plugins and the endpoint still answers, but `ListSets` reports
`noSetHierarchy` and `ListRecords` reports `noRecordsMatch`. That is the
usual reason a harvester sees an empty repository.

## Options

**Options** in the toolbar. The full parameter list is in the
[generated reference](../../reference/configuration/components/oaipmh.md);
what the settings are for:

**Repository Name**, **Base URL**, and **Admin E-Mail** are what `Identify`
reports about the hub. They default to the hub's site name, the request's own
base URL, and the site mail-from address, which is usually right but is
guessed per request — set them explicitly if the hub answers on more than one
hostname.

**Earliest Datestamp** is the date the repository claims nothing predates.
It ships as `2012-02-12 00:00:00`, which is a placeholder, not a fact about
your hub. Set it to something true; harvesters use it to decide how far back
to ask.

**Deleted Record** declares whether the repository reports withdrawn records
— `No`, `Transient`, or `Persistent`. The component never emits a deleted
record, so the honest answer is `No`.

**Harvesting Granularity** picks the finest date resolution the endpoint
accepts, either whole days or seconds.

**Result Limit** is how many records go into one page of a response before a
`resumptionToken` is issued. The default is 50.

> **Note:** **Allow ORE** does nothing. It is stored and passed to the
> response builder, which never reads it, and no ORE schema ships with the
> component — `ListMetadataFormats` offers `oai_dc` and `oai_qdc` whatever
> this is set to.

## Permissions

`com_oaipmh` declares the usual seven actions and exposes a **Permissions**
tab in **Options**, but only `core.manage` and `core.admin` are ever tested:
`core.manage` to open the component at all, `core.admin` to see the
**Options** button. The endpoint itself is public and does no access check —
it publishes what its plugins select, which is published content only.

<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
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

The hubs that care are the ones whose work is meant to be found from outside.
A group publishes datasets on the hub and the university library wants them in
the institutional discovery service alongside everything else the department
produces; the librarian asks for "an OAI endpoint" and a base URL. That is
this component, and the answer is one URL and a few settings. A hub that
publishes nothing, or whose publications are for its own members, can leave it
alone.

## What it is already doing

It is not something you switch on. A fresh install enables the component and
both of its content plugins, so the endpoint answers from the first day the
hub is up, and anything that reaches it sees:

- every **published** version of every [publication](27-publications.md), and
- every **published**, standalone [resource](29-resources.md),

as Dublin Core metadata — title, authors, description, date, subject tags,
type, publisher and an identifier that resolves back to the record. No files
are served: a harvester gets the catalogue entry and a link, and following the
link puts it back in front of the hub's own access rules.

> **Warning:** *Published* is the only test either plugin applies. Neither
> looks at a record's **Access** setting, so the metadata of a publication or
> resource that is published but restricted to registered members, or to one
> group, is served to any anonymous harvester: title, authors, abstract, tags
> and the record's address. The files behind it stay protected, but the
> catalogue entry does not. If the hub has restricted publications whose
> existence is itself sensitive, disable the plugins below. Recorded in
> a record kept with the project.

If that is not wanted, disable the two `oaipmh` plugins under **Extensions →
Plugins**. The endpoint keeps answering, but with nothing in it.

## What it is not

This is not a search engine feed and not a sitemap; ordinary web crawlers do
not read it. It is not a backup or an export of the hub's content either — it
carries metadata, not files, and it has no import side. And it is not where
DOIs are minted; the component reports the DOI a publication already has.

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

A record's identifier is a resolvable address. Where the record has a DOI, it
is the DOI with the resolver in front of it; where it does not, it is the
record's own URL on the hub — `/publications/{id}/{version}` or
`/resources/{id}`. `GetRecord` accepts either form, and accepts a DOI with or
without the resolver prefix.

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
your hub, and it is the one setting on this screen that is wrong by default on
every hub. Set it to something true — the date of the hub's oldest published
record will do. Harvesters use it to decide how far back to ask, and a date
earlier than the truth only wastes their time.

**Deleted Record** declares whether the repository reports withdrawn records
— `No`, `Transient`, or `Persistent`. The component never emits a deleted
record, so the honest answer is `No`.

**Harvesting Granularity** picks the finest date resolution the endpoint
accepts, either whole days or seconds. It ships on seconds, which is the more
permissive of the two; leave it there unless a harvester asks otherwise.

**Result Limit** is how many records go into one page of a response before a
`resumptionToken` is issued. The default is 50, which is a sensible starting
point: raising it makes each response slower to build and larger to send, and
harvesters follow resumption tokens without help. Raise it only if one
complains about the number of round trips.

> **Note:** **Allow ORE** does nothing. It is stored and passed to the
> response builder, which never reads it, and no ORE schema ships with the
> component — `ListMetadataFormats` offers `oai_dc` and `oai_qdc` whatever
> this is set to.

## Permissions

`com_oaipmh` declares the usual seven actions and exposes a **Permissions**
tab in **Options**, but only `core.manage` and `core.admin` are ever tested:
`core.manage` to open the component at all, `core.admin` to see the
**Options** button. The endpoint itself is public and does no access check —
it publishes whatever its plugins select, which is every published record
whatever that record's own **Access** setting says. See the warning under
**What it is already doing**, above.

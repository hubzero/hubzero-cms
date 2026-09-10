<!--
status: rewritten
reviewed-against: 2.4-main @ 6efbbe32ed
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/managers/components/citations
-->
# Citations

Citations are the works — journal articles, books, theses, conference
papers — that have cited the hub or something published on it. The
component keeps a catalogue of them, links each one to the resource or
publication it references, formats it with a bibliographic style you
choose, and offers it for download in BibTeX or EndNote. The catalogue is
on the site at `/citations`; the [Hub users](../../users/06-citations/README.md)
book covers browsing, submitting, and importing.

Open it in the administrator interface under **Components > Citations**.
Five sub-menu links sit at the top left: **Citations**, the list below,
**Stats**, **Types**, **Sponsors**, and **Format**.

## Citations

The list shows every citation on the hub, whatever its scope. Columns are
**ID**, **Type**, **Title** / **Author(s)**, the published state, **Year**,
**Affiliated**, **Funded by**, **Scope**, and **Scope ID**. Click a column
heading to sort by it; click again to reverse the order. A citation with no
type is listed as **Generic**; a citation with no scope is listed as `Hub`,
and a scope ID of 0 as `N/A`.

Above the list, type into the **Search** box and press **Go** to match
titles, authors, and the other indexed fields, or press **Clear** to reset
it. The **Scope** select narrows the list to hub, group, or member
citations.

The toolbar offers:

- **Options** — the component's configuration; see [Options](#options).
- **New** — create a citation.
- **Edit** — open the checked citation. Clicking a title does the same.
- **Delete** — remove the checked citations. This is permanent.
- **Help** — the built-in help screen.

There are no Publish and Unpublish toolbar buttons. Instead, click the
state icon in a row to toggle that citation between published and
unpublished, and click the icon in the **Affiliated** or **Funded by**
column to toggle those flags.

> **Note:** Several labels on this screen render as their untranslated
> keys — the published column heading reads `PUBLISHED`, and the
> affiliation and funding toggles read `YES` and `NO` — because those
> strings are not defined in the component's language file.

## Creating or editing a citation

The edit screen has two columns. **Save & Close** stores the citation and
returns to the list, **Cancel** discards changes.

The left column starts with **Details**, a long form in which you fill in
only the fields that apply to the work being cited:

| Field | Notes |
|---|---|
| Type | The citation type, from the **Types** screen. Shown as title and alias. |
| Cite key | A unique identifier, such as `grossman93`. |
| Ref Type | Free text. The browse page's **Reference Type** filter matches on it. |
| Date submitted, Date accepted, Date published | Free text dates. |
| Year, Month | The year is used for sorting, filtering, and the yearly statistics. |
| Author(s) | Semicolon-separated. `Lastname, Firstname; …` |
| Author Address | |
| Editor(s) | |
| Title/Chapter | The citation's title. Required. |
| Book title, Short Title, Journal | |
| Volume, Issue/Number, Pages | |
| ISBN/ISSN, DOI | A DOI is linked through `https://doi.org/`. |
| Call Number, Accession Number | |
| Series, Edition | |
| School, Publisher, Institution | |
| Address, Location | |
| How published | For nonstandard publishing methods. |
| URL | The link the citation's title points at. |
| E-print | A link to an electronic copy, such as a PDF. Takes precedence over the URL on the single-citation page. |
| Abstract, Text snippet/Notes, Keywords, Research Notes | Editor fields. |

Below that, **Manually Format Citation** lets you override everything the
formatter would produce: pick a **Format Type** of **APA** or **IEEE** and
type the finished citation into the **Citation** box. Anything entered here
is displayed verbatim wherever the citation appears.

The right column holds four panels.

| Panel | Notes |
|---|---|
| Citation for | Associations to hub content. Each row has a **Type** (**Resource** or **Publication**), an **ID**, and a **Context** of **References this citation** or **Referenced by this citation**. Five blank rows are offered; **+ Add a row** adds more. Clearing a row's type or ID deletes that association on save. |
| Affiliation | **Affiliated with your organization** and **Funded by your organization**, the two flags shown in the list and used by the statistics. |
| Scope | **Scope** — `Hub`, `Group`, or `Member` — and **Scope ID**, the group or member the citation belongs to. |
| Options | **Sponsors**, a multiple-selection list of the sponsors defined on the **Sponsors** screen; **Tags** and **Badges**, each shown only when the matching option is enabled; **Exclude from export**, fields to leave out of this citation's downloads; and **Show Abstract in Rollover**, which overrides the component-wide **Show Abstract** setting for this one citation. |

## Stats

**Stats** is a read-only table of citations per **Year**, split into
**Affiliated** and **Non-affiliated** counts with a **Total** column. The
same figures appear on the public `/citations` page.

## Types

A type is a kind of cited work — article, book, thesis. The list shows
**ID**, **Alias**, and **Title**; the toolbar has **New**, **Edit**,
**Delete**, and **Help**.

| Field | Notes |
|---|---|
| Alias | The short machine name, such as `article`. |
| Title | The name shown in menus and on the site. |
| Description | What qualifies as this type. |
| Fields | The fields this type uses, one placeholder per line or comma-separated. The submit form on the site shows only these fields once a type is chosen. Type and Title are always included. |

The edit screen lists every available placeholder and the field it maps to
beside the form.

## Sponsors

Sponsors are the organizations credited for a citation's abstract. The list
shows **ID**, **Sponsors**, **Link**, **Image**, and **Actions**; each row
carries **Edit** and **Delete** links. The toolbar has only **New** and
**Help**.

| Field | Notes |
|---|---|
| Name | The sponsor's name, shown when no image is set. |
| Link | The URL the sponsor's name or logo points at. |
| Image | A URL to the sponsor's logo. |

Sponsors appear on a citation only while the **Citations Sponsors** option
is on.

## Format

**Format** controls how citations are rendered. Choose a **Format Style**
from the styles in the database, and the **Format String** box fills with
that style's template. The table beside it lists every **Placeholder**,
such as `{AUTHORS}` or `{TITLE/CHAPTER}`, with the field it stands for;
clicking a row inserts it. **Save & Close** stores the edited template.

> **Note:** Saving a style named anything other than **Hub Custom** edits
> that style's template in place. Choosing **Custom Format** and saving
> creates or overwrites the single style named **Hub Custom**.

Which style citations actually use is set by the **Default Format** option,
which takes a style name.

## Options

The **Options** button opens the component's configuration. The **Basic**
tab covers presentation: whether a citation's title links to its own page,
the default bibliographic format, the label shown beside each citation in
browse mode, whether abstracts and sponsors are shown, COinS and OpenURL
output, how the title is linked, the "internally cited" image, and whether
tags and badges may be attached and displayed. The **Import/Export** tab
controls who may submit and import citations and whether single and bulk
downloads are offered. Every option is listed with its values in the
[configuration reference](../../reference/configuration/components/citations.md).

The **Permissions** tab controls who may administer and manage the
component and who may create, delete, edit, edit the state of, and edit
their own citations. The same five actions can be set separately for
citations, types, and sponsors.

## Import plugins

Bulk import is handled by the plugins in the **Citation** group. **Citation
- Default** takes a `.txt` upload and, if it holds EndNote records, hands
it on; **Citation - BibTex** reads `.bib` files and **Citation - Endnote**
reads `.enw` files. Only the last two advertise themselves on the upload
page. Each has a title-match percentage that decides when an
uploaded record is treated as a duplicate of one already on file, and the
EndNote plugin can map custom tags to badges. **Citation - DOI** adds the
citation to a publication's DOI metadata record.

## Member and group citations

Members and groups keep their own citation lists, stored in the same table
with a scope of `member` or `group`. They are managed from the member
profile and the group pages by
[`core/plugins/members/citations`](../../../core/plugins/members/citations/citations.php)
and [`core/plugins/groups/citations`](../../../core/plugins/groups/citations/citations.php),
and appear here with their scope in the **Scope** column.

## API

The component exposes `GET /api/citations/list`, in two versions, for
listing citations. See the [API reference](../../reference/api/citations.md).

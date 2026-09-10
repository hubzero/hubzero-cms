<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/users/groups/groupcitations
source-id: 3307
modified: 2015-09-22
-->
# Group citations

A group citation is a record of a published work that came out of the
group's work — a paper, a book, a thesis, a patent. The group's **Citations**
tab lists them, and a group manager decides whether the list is the group's
own curated set or that set plus whatever the group's members have cited on
their own profiles.

Use it when a group wants one bibliography it controls. The `soilcarbon`
group used through this chapter has papers from four institutions and a
reporting requirement: every year it has to hand its funder the list of
publications the project produced. Rather than chase people for BibTeX, the
managers import each new paper as it appears, so the list is always current
and anyone can export the whole thing in one go.

> **Note:** The tab is not on every group. A manager switches it on under
> **Access Permissions**; see
> [Customization](03-groupcustom.md#which-tabs-appear). The hub as a whole
> also has a [citations catalogue](../06-citations.md), which is a different
> list: it records works that cite the hub. A group's citations are works the
> group wrote, and nothing you add here appears there.

## Setting the tab up

The first time a manager opens **Citations** on a group that has none, the
tab redirects to its settings page with *Please select your settings for this
group.* Fill it in and select **Save**.

The settings page has four sections.

### Sources

**Select which sources of citations to display**:

| Option | What the tab lists |
|---|---|
| **Display group-attributed citations only.** | Only citations added to the group |
| **Display group-attributed and group member-attributed citations.** | Those, plus citations belonging to the group's members |

### Badge and Tag Options

**Display Tags** and **Display Badges**, each **Yes** or **No**. They control
whether each entry carries its tag and badge clouds.

### COinS Options

**Include COinS** and **COinS Only**.

COinS — ContextObjects in Spans — embeds an OpenURL reference in the page's
HTML. Browser extensions and reference managers read it to find the full text
of a cited work, or the copy your library holds. Turning it on adds the
markup; it is invisible to a reader who is not looking for it.

### Citation Format

The bibliographic style used to render every entry in the group. Pick one of
the hub's formats, or **Custom format for group**.

A group gets one custom format. Choosing it opens a text area preloaded with
the format you had selected, so you can start from something rather than a
blank box, and a table of the fields you can use. Type a field's key into the
text area, or select its row in the table to insert it.

## Adding citations

Only a group manager can add citations. Three buttons sit at the top of the
tab: **Submit a citation**, **Import Citations** and **Settings**.

Import is the quicker route when you already have the records. The steps
under **Importing a file** below follow a manager of `soilcarbon` loading the
year's four papers from a single file exported out of a reference manager.

### One at a time

**Submit a citation** opens a form. **Type** and **Title** are required;
everything else — authors, journal, year, DOI, abstract, tags — is optional.

### Importing a file

**Import Citations** runs in three steps:

1. **Upload citations file.** BibTeX (`.bib`) and EndNote (`.enw`) are
   accepted, up to 4 MB.
2. **Preview imported citations.** The hub reports how many records it read
   and flags any that need a decision — usually a record that duplicates one
   the group already has. For each duplicate, choose **Replace Old Version
   with Uploaded One**, **Keep Old and Import Uploaded Version** or **Don't
   Import Uploaded Version**. Then select **Submit Imported Citations**.
3. **Browse uploaded citations.** The new entries, with links to import more
   or to browse them all.

## Publishing

A citation you enter or import starts **unpublished**: nobody but a group
manager sees it, and it does not count towards the group's citation total.
Unpublished rows are drawn on a pale yellow background.

To publish one, select the publish control on its row. To publish several,
tick their boxes and use **Toggle published state** at the top of the list.
The same controls unpublish an entry that is already published.

## Editing and deleting

A manager's row controls are **Edit**, **Delete** and publish/unpublish.

**Delete** does not erase the citation: it moves it to the trashed state,
where the group can no longer see it. Only a hub administrator can remove it
for good, or bring it back. If you delete one by accident, ask the hub's
administrators to republish it.

> **Note:** These three controls only appear on citations that belong to the
> group. If you turned on **Display group-attributed and group
> member-attributed citations**, the member-attributed entries in the list
> have no controls — they belong to the member, not to the group, and only
> that member can change them.

## Linking an author to a hub profile

In the citation form's **Author(s)** field, start typing a member's full name
or username. The autocompleter offers matching accounts with their ID
numbers, so you can be sure of the right one. Select the person, then select
**Add**.

The author's name then renders as a link to their profile on the hub. The
authors already attached to the citation are listed under the field, each
with a **Delete** link.

## Reading and exporting

Anyone who can reach the tab can search the list — by title, author, ISBN,
DOI, publisher and abstract — and filter it by **Type**, **Tags**, **Author
By**, **Published In**, year range and upload date range. **Sort by** offers
year, date created, title, author and journal. **All** and
**Member-contributed** switch between the whole list and the members' own
entries.

Tick the citations you want and use **EndNote** or **BibTex** under **Export
Multiple Citations** to download them.

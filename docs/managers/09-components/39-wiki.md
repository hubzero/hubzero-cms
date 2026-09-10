<!--
status: rewritten
reviewed-against: 2.4-main @ ddeb90135f
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/managers/components/wiki
-->
# Wiki

The wiki is a set of community-editable pages written in wiki markup. Every
page keeps a full revision history, can carry comments and file attachments,
and can be tagged. The site wiki lives at `/wiki`; groups get their own wiki
under `/groups/<group>/wiki`. This chapter covers the administrator's side;
the [Hub users](../../users/wiki.md) book covers writing and editing pages.

Open it in the administrator interface under **Components > Wiki**. Two
sub-menu links sit at the top left: **Wiki Pages**, the list below, and
**Plugins**, which opens the Plugins manager filtered to the `wiki` folder.
The Plugins link appears only if you may manage plugins.

## Wiki Pages

The list shows every page in every wiki — site and group alike. Columns are
**ID**, **Title** (with the page's path underneath, prefixed by `/wiki/`),
**Mode**, **State**, **Locked**, **Scope**, **Revisions**, and **Comments**.
Click a sortable column heading to sort by it; click again to reverse.
**Mode** is not sortable.

The **Revisions** and **Comments** cells are links: they open the revisions
or comments screen for that page.

Above the list are three filters:

- **Search** — matched against page titles only, then **Go**. **Clear**
  resets it.
- **Scope** — a `scope:scope_id` pair, such as `site:0` or `group:42`.
- **Namespace** — anything before a colon in a pagename. Shipped namespaces
  include `help`, `template`, and `special`.

The toolbar offers **Options** (component configuration, shown only to users
with the Configure permission), **New**, **Edit**, **Delete**, and **Help**.
Delete asks for confirmation on a separate screen and is permanent — it
removes the page's revisions, comments, attachments, and tags with it.

Clicking a page's **State** or **Locked** icon is not the same thing. State
toggles between published and unpublished; the **Locked** column reflects the
page's `protected` flag and is display-only in this list.

## Creating or editing a page

**Details**

| Field | Notes |
|---|---|
| Title | Required. Shown as the page heading. |
| Pagename | Required. The identifier used in URLs. Letters, numbers, and colons only; anything before a colon becomes the page's namespace. Left blank on a new page, it is generated from the title. `special:`, `image:`, and `file:` are reserved and rejected. |
| Path | The parent path for a sub-page, for example `ParentPage`. |
| Scope | `site` for the main wiki, `group` for a group wiki. |
| Scope ID | 0 for the site wiki, or the group's ID for a group wiki. |
| Authors | A comma-separated list of usernames. Meaningful in Knol mode, where these users hold edit rights and are listed on the page. |
| Tags | Optional, comma-separated. |

A panel beside the form shows the page's **ID**, **Created** date,
**Creator**, **Hits**, and its number of **Revisions**. The values are read
only; there is no reset-hits button on this form.

**Parameters**

| Field | Notes |
|---|---|
| Mode | **Wiki** — any logged-in user may edit. **Knol** — only the listed authors may edit, and the author list is shown on the page. **Static** — the page renders without the wiki tabs and chrome, using the full content area. |
| Hide author list | Suppresses the author byline on a Knol page. |
| Allow other users to submit suggested changes | On a Knol page, lets non-authors save revisions that stay unapproved until someone approves them. |
| Allow other users to post comments | Enables the comment thread on a Knol page. |
| State | Unpublished, Published, or Trashed. Only published pages are reachable on the site. |
| Access Level | Which viewing level a visitor needs. Enforced on site-scope pages; group pages use the group's own membership rules instead. |

Press **Save** to save and stay, **Save & Close** to return to the list, or
**Cancel** to discard.

> **Note:** The page's lock (the `protected` flag shown in the **Locked**
> column) has no field on this form. It is set by page editors on the site,
> through the **Lock page. Only administrators may make changes.** checkbox
> in the site editor.

## Revisions

The revisions screen opens from a page's **Revisions** link. A summary table
at the top repeats the page's title, scope, ID, pagename, and path. Below it,
a search box filters revisions by the text of the revision, and the list
shows **ID**, **Revision** number, **Edit Summary**, **Approved**,
**Minor edit**, **Created**, and **Creator**. Click the **Approved** icon to
flip a revision between approved and not approved.

Only approved revisions are shown to readers. Approving a revision makes it
the page's current version and un-approves whichever revision held that place
before. The toolbar has **New**, **Edit**, **Delete**, and **Help**.

The edit form carries **Edit summary** and **Text**, with **Minor edit** and
a **State** of Not approved, Approved, or Trashed in the parameters panel.
Page title, pagename, scope, page ID, revision ID, revision number, creator,
and created date are shown for reference and submitted unchanged.

> **Note:** A page's only approved revision cannot be deleted; the screen
> refuses with "Can not remove only available revision".

## Comments

The comments screen opens from a page's **Comments** link. It lists **ID**,
**Comment** (truncated to 90 characters and linked to the edit form),
**Creator**, **Anonymous**, **State**, and **Created**. Replies are shown
indented under the comment they answer. The toolbar has **Delete**, **Edit**,
**New**, and **Help**, and a search box filters by comment text.

The edit form has just two fields: an **Anonymous** checkbox and the required
**content** body. Creator, created date, and the page are shown for
reference.

> **Note:** Comment states do not match page states. A comment with state 0
> is published, state 1 means it was reported as **Abusive** and is hidden
> pending review, and state 2 is trashed.

## Plugins

The wiki depends on plugins in the `wiki` folder, reachable from the
**Plugins** sub-menu link:

- **Parser - Default** — the Trac-style wiki markup parser, the macro
  library, and the LaTeX formula renderer.
- **Parser - Markdown** — Markdown instead of wiki markup.
- **Editor Toolbar** and **Editor WYKIWYG** — the buttons above the page
  text box in the site editor.

> **Note:** Only one parser runs. The component picks the first enabled
> plugin in the `wiki` folder whose name starts with `parser`, hub-wide.
> Enabling both parsers does not let pages choose between them.

## Options

The **Options** button opens the component-wide configuration: the subpage
separator, the default main page name, the maximum pagename length, whether
comments and comment ratings are allowed, where an automatic table of
contents is placed and how many headings trigger it, the upload paths for
attachments, math images, and temporary files, and page caching with its
lifetime. Every option is listed with its values in the
[configuration reference](../../reference/configuration/components/wiki.md).

A group manager can override the automatic table-of-contents mode and
heading threshold for that group's wiki from **Wiki Settings** in the group's
wiki area.

## Permissions

The **Permissions** tab sets who may configure, manage, create, delete,
edit, change the state of, and edit their own wiki content, both for the
component as a whole and for the `page` section. On a site-scope wiki page in
standard Wiki mode, editing requires `core.edit`, or `core.edit.own` if you
created the page; deleting requires `core.delete`; and anyone holding
`core.manage` gets full rights over every page. Group wiki pages defer to
the group's membership and manager roles instead.

> **Warning:** The maintenance special pages — `Special:FixVersion`,
> `Special:FixLinks`, and `Special:FixLength` — write to the database but
> carry no permission check of their own. Anyone who knows the URL can run
> them.

## API

The wiki exposes create, read, update, delete, and list endpoints under
`/api/wiki`; see the [API reference](../../reference/api/wiki.md).

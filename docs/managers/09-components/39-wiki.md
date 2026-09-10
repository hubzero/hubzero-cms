<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/managers/components/wiki
-->
# Wiki

The wiki is a set of community-editable pages written in wiki markup. Every
page keeps a full revision history, can carry comments and file attachments,
and can be tagged. The site wiki lives at `/wiki`; groups get their own wiki
under `/groups/<group>/wiki`. This chapter covers the administrator's side;
the [Hub users](../../users/23-wiki.md) book covers writing and editing pages.

## Whether your hub needs it

The wiki is for the document that several people maintain and nobody owns —
a protocol, a parameter table, a page of conventions that drifts as the work
drifts. Its distinguishing feature is the revision history: every save is
kept, you can see who changed what, and you can put back the version from
before somebody's well-meant rewrite. Nothing else in this section can do
that.

The typical case: a lab on the hub runs an instrument and keeps a page of
settings that three postdocs edit between them. Any one of them can fix a
number; if one of them fixes it wrongly, the history says so and the
previous revision is one click away.

Against its siblings:

- Better than the [Knowledge base](19-kb.md) at anything more than one
  person keeps up to date, because members edit it without an administrator
  account and every edit is recoverable. Worse at policy: a page any logged-in
  member may edit is not where the hub's rules should live, and there is no
  approval step on an ordinary wiki page — a save is live immediately.
- Better than the [Forum](17-forum.md) at conclusions; worse at reaching
  them. A wiki page shows the current state and hides the argument that got
  there.
- The group wikis are where most hubs' wiki activity happens. The site wiki
  at `/wiki` is a public space any member may edit; a group wiki is bounded
  by the group's membership, which is what most people actually want.

**What it is not:** it is not a plain editor. Pages are written in wiki
markup, which members have to learn, and a hub that switches on the Markdown
parser instead changes the syntax for every page on the hub at once — see
the note under [Plugins](#plugins).

If nobody has asked for a wiki, the site wiki is worth leaving off and the
group wikis worth leaving to the groups that want them. An unwatched
public-edit wiki is the component on a hub most likely to fill with spam.

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

## Putting back a page somebody broke

Following the instrument-settings example: a postdoc has overwritten the
page with something wrong and the lab wants yesterday's version back. This is
the job the wiki exists for, and the whole of it happens on the revisions
screen.

1. Go to **Components > Wiki**. Filter **Scope** to the group's
   `group:<id>` if it is a group page, and find the page by title.
2. Click the number in the page's **Revisions** column.
3. Read down the list. **Created** and **Creator** say who saved what and
   when; **Edit Summary** says why, when the editor bothered to write one.
   The revision currently carrying the **Approved** mark is the one readers
   see.
4. Click the **Approved** icon on the revision you want back. That revision
   becomes the page's current version and the one that held the place before
   it is un-approved.
5. Reload the page on the site to confirm.

Nothing is destroyed by this. The bad revision is still in the list,
un-approved, and you can approve it again if you have picked the wrong one.
That makes step 4 one of the safest things in this book.

> **Warning:** **Delete** on the revisions screen is a different matter — it
> removes the revision permanently, and with it the ability to go back to it.
> Un-approving is almost always what you want instead. The screen does refuse
> to delete a page's only approved revision.

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

Most of these are set-and-forget and the shipped values are reasonable. Two
deserve a look. The **upload paths** for attachments, maths images, and
temporary files should be left alone unless you are moving storage: changing
one does not move the files already there, so existing attachments stop
resolving. And **page caching** trades freshness for speed — with it on, an
edit does not appear until the cached copy expires, which on a wiki people
are actively editing looks like the save failing. It ships off, and on a
small hub it should stay off.

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

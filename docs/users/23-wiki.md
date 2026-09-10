<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/users/wiki
-->
# Wiki

The wiki is a set of pages a community writes together. Anyone can read them.
Editing them takes a permission, and who has it differs between the hub's own
wiki and a group's — see [Who may edit](#who-may-edit). Every save keeps the
old text, so nothing is ever lost. The hub's wiki is at
`https://<your hub>/wiki`, and each group has its own wiki under the group's
**Wiki** tab; see [Groups](11-groups/README.md).

A wiki is the right place for knowledge that changes and has no single owner.
A thermal transport lab writes down how it prepares samples and calibrates the
apparatus: the protocol is corrected every few months, three people maintain
it, and every new student needs it on their first day. Kept in the group's
wiki, it has one address, everyone in the group can fix it, and the history
shows what changed when a result stops reproducing.

## The wiki is not the other places you could put this

- A **blog** post is dated and stays as written. A wiki page is meant to be
  rewritten. See [Blog](05-blog.md).
- A **project**'s files are private to the project team and are the work
  itself. A wiki page is the writing about the work. See
  [Projects](16-projects.md).
- A **resource** is a finished thing you are publishing to the whole hub,
  with authors and a citation. A wiki page is a living document. See
  [Resources](21-resources.md).

One consequence worth knowing before you commit to it: on a hub running Solr
search, wiki pages are not in the search index, so the hub's main search box
will not find the lab's protocol. Its readers reach it by link, by the wiki's
own **Search** box, or by tag. See [Search](24-search.md) and
[Tags](27-tags.md).

## Who may edit

Reading is open to anyone who can see the page. Everything else depends on a
permission, and this is the usual reason a member cannot find the **Edit**
tab.

In a **group** wiki, any member of the group can create pages, and edit and
delete unlocked ones. Group managers can do all of that plus edit locked
pages. This is the arrangement the lab's protocol wants, and it is why most
working documents live in a group wiki rather than the hub's — and why a
group member should treat the **Delete** tab with care.

In the **hub's** wiki, creating and editing are hub-wide permissions an
administrator grants. A bare install grants them to the hub's content-editing
roles, not to every registered member, so on a hub whose administrator has not
widened them, an ordinary member can read the site wiki and comment on it but
cannot create or edit a page. If the **Edit** tab is not there, that is why;
ask the hub's support team. See [Support](12-support.md).

The `Help:` pages that ship with the wiki, and any page an administrator has
locked, are editable only by wiki managers in both cases.

## Finding your way around

A wiki page's address is its page name: `https://<your hub>/wiki/MyPageName`.
Sub-pages add path segments: `.../wiki/ParentPage/ChildPage`.

The sidebar beside every page holds a **Search** box for this wiki, and two
lists of links:

- **Wiki** — **Main Page**, **Help**, **Page Index** (an alphabetical list of
  every page), and **Recent Changes**.
- **Tools** — **What links here**, **Cite this page**, **Download PDF**, and
  **New page** if you are logged in and allowed to create pages.

Above the page body is a row of tabs: **Article**, **Edit**, **Comments**,
**History**, and **Delete**. You only see the tabs you have rights to use.

> **Note:** The wiki search uses MySQL full-text matching. It works well once
> a wiki has a lot of content, but it returns few or no results on a small
> wiki, and it ignores very common words such as "the".

## Creating a page

Log in, then select **New page** from the sidebar. If the link is not there,
you do not hold the create permission — see [Who may edit](#who-may-edit).

> **Note:** On the hub's own wiki the link can be there and the form still
> refuse you. **New page** is offered to anyone holding the create
> permission, but the form behind it demands the edit permission as well, so
> a member granted only the first is turned away with *You are not authorized
> to perform this action.* Group wikis are not affected. Ask an administrator
> to grant both. In a group, open the
group's **Wiki** tab first. You can also follow a red link to a page that
does not exist yet and take the offer to create it.

The form has these fields:

| Field | Notes |
|---|---|
| Parent page | Optional. Puts the new page under another page in the URL, for example `.../wiki/MainPage/Details`. Child pages are not linked from the parent automatically; add the links yourself. |
| Template | Optional. Pre-fills the page text and tags from a page in the `Template:` namespace. |
| Title | Required. |
| Page text | Required. The page body, in wiki markup. |
| Treat as | **Wiki page anyone can edit**, **Knowledge article with specific authors**, or **Static (open layout)** where it is offered. |
| Authors | For a knowledge article, the members who may edit it. |
| Hide author list | Leaves the author byline off a knowledge article. |
| Allow other users to submit suggested changes | Intended to let others save revisions that an author then approves. It does not work — see the warning under [Editing a page](#editing-a-page). |
| Allow other users to post comments | Turns the comment thread on. |
| Lock page. Only administrators may make changes. | Freezes the page against further editing. |
| Tags | Comma-separated keywords; see [Tags](27-tags.md). Editing tags alone does not create a new revision. |
| Edit summary | A short description of what you changed. |

Press **Preview** to see the rendered result without saving, or **Save** to
publish. The page name is generated from your title; to change it later, open
the page and use the **Rename** link on the edit form.

## Editing a page

Open a page and choose the **Edit** tab. The same form appears, filled in.
Write your **Edit summary**, then **Save**. Say the lab's calibration step
changes: open the protocol page, change the paragraph, put *new calibration
interval* in the summary, and save. The old text stays in the history.

Your revision goes live immediately. There is no approval queue for the people
who may edit.

> **Warning:** The **Allow other users to submit suggested changes** option on
> a knowledge article does not work. The edit screen turns anyone who is not
> an author away with *You are not authorized to perform this action.* before
> it reaches the suggestion path, so a non-author cannot open the form at all
> and no suggested revision can be created. The **[ approve ]** link described
> under [Page history](#page-history) therefore only ever appears for
> revisions made some other way. Do not rely on the option; name the people
> who need to edit an article as its authors instead.

Locked pages, and the shipped `Help:` pages, are editable only by wiki
managers.

## Attaching and embedding files

The edit form has a file area marked **Click or drop file**. Drop a file on it
or click it to choose one, and it uploads to the page straight away. Then put
a macro in the page text where the file should appear:

- `[[Image(picture.jpg)]]` embeds an image. Add arguments to size or align it:
  `[[Image(picture.jpg, 120px)]]`, `[[Image(picture.jpg, right)]]`,
  `[[Image(picture.jpg, nolink)]]`.
- `[[File(document.pdf)]]` makes a download link.

File types and sizes follow the hub's media settings, so very large files or
unusual extensions may be refused.

## Wiki syntax essentials

The default parser uses Trac-style markup. The buttons above the page text box
insert most of it for you.

| You write | You get |
|---|---|
| `'''bold'''`, `''italic''`, `__underline__` | bold, italic, underline |
| `~~strike~~`, `^super^`, `,,sub,,` | strike-through, superscript, subscript |
| `` `code` `` or `{{{code}}}` | monospaced, unparsed text |
| `= Heading =`, `== Subheading ==` | headings, one to five `=` signs |
| ` * item` and ` # item` (leading space required) | bulleted and numbered lists |
| ` term::` then an indented line | a definition list |
| `[[BR]]` | a forced line break |
| `----` | a horizontal rule |
| `MainPage`, `[MainPage Main Page]` | a link to another wiki page, plain or titled |
| `[http://example.org Example]` | an external link |
| `!NotALink` | text that would otherwise become a link |
| `<math>x^2</math>` | a rendered LaTeX formula |

Tables are rows of cells fenced by doubled vertical bars, one row per line:

```text
||        ||= stable =||= latest =||
||= 0.10 =||  0.10.5  || 0.10.6dev||
||= 0.11 =||  0.11.6  || 0.11.7dev||
```

Wrapping a cell's contents in `=` makes it a header cell. Leaving a cell empty
merges it into the next non-empty one, and a leading `<.`, `>.`, `=.`, `^.`,
or `~.` aligns the cell.

Two more blocks are worth knowing. A `{{{` … `}}}` block with `#!` on its
first line runs a *processor*: `{{{#!html …}}}` passes raw HTML through, and
`{{{#!wiki note …}}}` draws an admonition box (`warning`, `caution`,
`important`, `note`, `tip`). And `[[Include(PageName)]]` pulls the whole of
another wiki page into this one.

The wiki ships its own reference pages, reachable from **Help** in the
sidebar: **Help:WikiFormatting**, **Help:WikiMacros**, **Help:WikiPageNames**,
**Help:WikiHtml**, **Help:WikiMath**, **Help:Processors**,
**Help:Admonitions**, **Help:Templates**, **Help:Includes**, and
**Help:PageHistory**.

## Macros

A macro inserts generated content into a page. Calls go in double square
brackets, with arguments in parentheses: `[[Timestamp]]`,
`[[TitleIndex(Help)]]`, `[[Tag(heattransfer)]]`.

Useful ones include `[[TitleIndex]]` (a list of every page in this wiki,
optionally filtered by a name prefix), `[[TableOfContents]]`,
`[[Children]]` and `[[Parents]]`, `[[FootNote(text)]]`, `[[Anchor(name)]]`,
`[[RandomPage]]`, `[[Resource(id)]]`, `[[Contributor(username)]]`,
`[[YouTube(id)]]`, `[[Video(file)]]`, and `[[Slider]]`.

> **Tip:** To see every macro your hub has installed, with its documentation,
> put `[[MacroList]]` on a page and save it. `[[MacroList(Image)]]` shows the
> documentation for one macro only.

## Page history

The **History** tab lists every revision, newest first, with when it was made,
who made it, its length and the change in bytes, and its status. Click a date
to read that version, or **Markup** to see its raw wiki text.

Pick two revisions with the radio buttons and press **Compare selected
versions** to see a line-by-line difference: deletions are marked before the
change, additions after.

If you may edit the page, each revision also offers **Mark as current
version** and **Delete revision**. Unapproved revisions show an
**[ approve ]** link. A page's only approved revision cannot be deleted.

## Comments

Where a page allows comments, the **Comments** tab holds the discussion. It is
for talking about the article, not for publishing your own views on the
subject.

Log in, write your comment, and press **Save**. Check **Anonymous** to keep
your name off it. Use **Reply** on a comment to answer it in place. Where the
hub has turned comment ratings on, a top-level comment also carries a
one-to-five star rating. You can filter the thread to the comments left
against one version of the page.

If a comment is offensive, use **Report abuse**. It is hidden with a notice
saying it is under review until hub staff deal with it.

## Renaming and deleting

The edit form links to **Rename**, which changes the page name used in URLs.
The wiki's home page cannot be renamed.

The **Delete** tab removes the page. Tick **Confirm deletion.** and press
**Delete**. This takes all of the page's revisions, comments, and attachments
with it, and cannot be undone.

## Special pages

Pages in the `Special:` namespace are generated on demand rather than edited:
**All Pages**, **Recent Changes**, **New Pages**, **Short Pages**,
**Long Pages**, **File List** (everything uploaded to this wiki),
**Search**, **Cite this page** (a ready-made citation for the version you were
reading), and **What links here**.

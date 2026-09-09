<!--
status: rewritten
reviewed-against: 2.4-main @ ddeb90135f
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/users/wiki
-->
# Wiki

The wiki is a set of community-editable pages. Anyone can read them; logged-in
members can create pages, edit them, attach files, and comment. Every save
keeps the old text, so nothing is ever lost. The hub's wiki is at
`https://<your hub>/wiki`, and each group has its own wiki under the group's
**Wiki** tab; see [Groups](groups/README.md).

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

Log in, then select **New page** from the sidebar. In a group, open the
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
| Allow other users to submit suggested changes | Others may save revisions, but they stay unapproved until an author approves them. |
| Allow other users to post comments | Turns the comment thread on. |
| Lock page. Only administrators may make changes. | Freezes the page against further editing. |
| Tags | Comma-separated keywords; see [Tags](tags.md). Editing tags alone does not create a new revision. |
| Edit summary | A short description of what you changed. |

Press **Preview** to see the rendered result without saving, or **Save** to
publish. The page name is generated from your title; to change it later, open
the page and use the **Rename** link on the edit form.

## Editing a page

Open a page and choose the **Edit** tab. The same form appears, filled in.
Write your **Edit summary**, then **Save**.

On a page in Wiki mode, your revision goes live immediately. On a knowledge
article you are not an author of, the form warns you that any changes will be
saved as a **suggested** revision; an author has to approve it before readers
see it. Locked pages, and the shipped `Help:` pages, are editable only by hub
administrators.

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

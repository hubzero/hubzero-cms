<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: ok
source: https://help.hubzero.org/documentation/240/managers/content
source-id: 3367
modified: 2014-10-10
imported: 2026-09-09
-->
# Content

An **article** is a page of written content that the hub stores and serves
itself: the About page, the terms of use, a policy statement, a short
explanation of how the community works. Articles are the hub's own pages, as
opposed to the resources, wiki pages, group pages, and publications that
members create.

Every hub needs a handful of these and almost none needs more. A hub that
opens to the public wants an About page, a contact page, a citation or
acknowledgement page the funder asks for, and a page of terms. That is the
whole job for most managers: half a dozen pages, written once, edited twice a
year. If your hub already has those and nobody has complained about them, you
can skip this section and come back when something needs changing.

Articles live in the **Content** menu of the administrator interface, which
holds three screens:

| Menu entry | Screen | Chapter |
|---|---|---|
| **Article Manager** | The list of articles and the article editor. | [Article Manager](articlemanager.md) |
| **Category Manager** | The categories articles are filed under. | [Categories](categories.md) |
| **Media Manager** | The shared image and file library the editor's image button browses. | — |

**Add New Article** and **Add New Category** appear under their managers when
you may create one.

Nothing on these screens is dangerous by accident, with one exception, and it
is worth knowing before you open anything: **Delete** on the article list and
the category list does not delete. It sets a state. Elsewhere in the
administrator interface a button with the same wording erases the row for
good. [States, deleting and check-out](states.md) says which is which, and it
is the chapter to read before you press a delete button anywhere on this hub.

![The Content menu in the administrator interface, open over the control panel](../media/content-backendcontent.png)

## What an article is not

Managers who arrive here from another job often assume this is where the hub's
content lives. It is not. Datasets, tools and their documentation are
resources. Formally released datasets are publications. Pages members write
and revise themselves are wiki pages. Group material belongs to the group.
Questions, forum threads, blog entries, support tickets, knowledge base
entries and courses each have their own component, their own permissions and
their own chapter in [Components](../09-components/README.md).

The test is simple: if you type it once and visitors only read it, it is an
article. If it has authors, versions, tags, ratings or a review step, it is
not, and building it as an article throws all of that away.

## Content is not layout

What you type into an article is content, not presentation. The template
supplies the typography, the colours, and the page furniture; the article
supplies the words. Styling individual articles by hand fights the template
and breaks the next time the template changes. If a page needs a look the
template does not give it, change the template — see
[Templates](../10-extensions/02-templates.md).

## Where articles appear

An article has no URL of its own until something points at it. The two ways
to reach one are:

- **A menu item.** A menu item of type **Articles → Single Article** carries
  its own alias, and that alias — plus the aliases of its parents — is the
  URL. This is how the pages shipped with a hub work: the sample **Terms of
  Use** article is reached at `/about/terms` because a menu item with that
  path points at it.
- **The article's own category path.** With no menu item, the hub matches a
  URL against category path plus article alias.

Both are explained in [URLs](urls.md), along with redirects.

## In this section

- [Article Manager](articlemanager.md) — what articles are for, a worked
  example that publishes an About page end to end, the article list, the
  editor, categories, and the publishing workflow.
- [Categories](categories.md) — the shared category manager, which serves
  articles, user notes, the knowledge base, and events.
- [URLs](urls.md) — how an article's address is built, and how to redirect
  one address to another.
- [States, deleting and check-out](states.md) — what published, unpublished,
  archived and trashed store, which delete buttons are permanent, and why a
  record gets stuck checked out. This one is not about articles alone. It
  explains behaviour you will meet on every list screen in the administrator
  interface, and it is the most useful page in this section.

Every option on the Article Manager's **Options** screen is listed in the
[Content configuration reference](../../reference/configuration/components/content.md).

# Writing guide

How the Hubzero documentation is written, so that every page reads the same
way and builds cleanly. The site is generated from these files by
[`gh-pages/build_site.py`](../gh-pages/README.md); pages are also read
directly on GitHub, so everything here works in both places.

## Files and names

- One book per directory under `docs/`, listed in `gh-pages/site.json`.
- A book's landing page is its `README.md`. A section is a subdirectory with
  its own `README.md`. Every other `*.md` file is a chapter.
- Name chapters with lowercase words separated by hyphens: `access-levels.md`.
  Where order matters, add a two-digit prefix: `01-requirements.md`. The
  prefix is dropped from the published URL.
- Images live in the book's `media/` directory and are referenced with a
  relative path: `![The Members list](../media/members-list.png)`.

## The page

Every page has exactly one level-one heading, and it is the page title:

```markdown
# Knowledge base

The knowledge base houses articles and frequently asked questions ...
```

Sections start at `##`. Do not skip levels.

Keep the first paragraph a plain summary of the page; the site uses it as the
description in search results and on section landing pages. A different
summary can be given in the metadata header instead.

## Metadata header

A page may begin with an HTML comment of `key: value` lines. The builder reads
it; GitHub hides it.

```markdown
<!--
status: reviewed
reviewed-against: 2.4-main @ 0ebd2294a3
reviewed: 2026-10-01
source: https://help.hubzero.org/documentation/240/managers/components/kb
modified: 2016-03-01
-->
```

| Key | Meaning |
|---|---|
| `status` | `imported` (converted from help.hubzero.org, unreviewed), `merged` (imported, with material merged from an older version), `draft`, `reviewed` (checked against the code), `rewritten` (replaced wholesale after review), `generated` (produced by a script from the source tree) |
| `reviewed-against` | The branch and commit the page was checked against |
| `reviewed` | Date of that review |
| `source` | Where an imported page came from |
| `source-id`, `modified`, `imported` | Bookkeeping from the import |
| `merged-from` | The documentation version merged into an imported page |
| `screenshots` | Whether the page's images still match the software: `ok`, `stale` (some or all show screens that have changed), or `none` (the page has no images). Recorded during review; the recapture is a separate pass |
| `summary` | Overrides the first paragraph as the page description |
| `order` | Overrides the filename prefix for ordering |
| `title` | Overrides the level-one heading as the page title |

Imported pages show a banner until their status changes. Flip the status to
`reviewed` only after checking the page against the code and fixing what has
drifted; the `/status/` page counts what is left.

## Links

- Link to other pages with relative paths to the Markdown file:
  `[access levels](../users/access-levels.md)`. Linking to a directory reaches
  its `README.md`. Anchors work: `access-levels.md#viewing-levels`.
- Link to code in the repository with a relative path to the file:
  `[`Relational`](../core/libraries/Hubzero/Database/Relational.php)`. The
  site turns it into a GitHub link.
- External links are ordinary absolute URLs.

## Code

Fenced code blocks always name a language:

````markdown
```php
$rows = Article::all()->whereEquals('state', 1)->rows();
```
````

Prefer showing real files to pasting copies. The include directive inserts
the current contents of a repository file, optionally a line range:

```markdown
<!--include: core/components/com_kb/site/router.php:20-45-->
```

## Notes and warnings

A blockquote whose first words are one of the labels below renders as a
callout:

```markdown
> **Note:** Menu items are cached; changes appear after the next save.

> **Warning:** This deletes the group's files as well.
```

Labels: `Note`, `Tip`, `Warning`, `Important`, `Caution`.

## Style

- The product is **Hubzero**. Not HUBzero, not HubZero, except when quoting
  something that spells it that way.
- Write to the reader: "you" for the person doing the task, "the user" for a
  hub visitor, "the administrator" for someone in the admin interface.
- Name interface elements as they appear on screen, in bold the first time:
  select **Save & Close**.
- Use the present tense and the active voice. Short sentences beat long ones.
- Tables for parameters and options; numbered lists for steps; bullets for
  everything else that is a list.
- Screenshots show the current template, cropped to the part that matters,
  with alt text that says what the picture shows.

## Before you commit

```bash
sh tools/docs/rebuild.sh
```

That regenerates the references, builds the site, checks every link, and
runs the tests. Commit the rebuilt `gh-pages/public/` with the change; the
Pages workflow refuses a stale copy.

# Hubzero Documentation Program

A plan for moving Hubzero's documentation out of the help.hubzero.org database and
into this repository as a multi-book, multi-chapter set of Markdown files under
`docs/`, rendered to GitHub Pages by a static builder under `gh-pages/`. The system
follows the pattern already in production for the nyxcraft projects (forterp,
itsfs, s5fs, pdp11-xdev, vax11-xdev, sixbit, tappty) and for hubzero/botshield.

Written 2026-09-09. Status: approved direction, decisions recorded in section 7.
Scope: Hubzero 2.4 on the `2.4-main` branch.

## 1. Goals

1. Documentation lives next to the code, versioned with it, reviewed in pull
   requests, and rebuilt on every push.
2. One canonical source. help.hubzero.org stops being an editing surface; it either
   redirects to the Pages site or renders the same built output.
3. Every page is verified against the current codebase before it is marked
   reviewed. The imported material is a decade old in most places.
4. Reference material that can be generated from code (configuration parameters,
   REST endpoints, muse commands, events) is generated, never hand-maintained.
5. The site meets WCAG 2.2 AA, works without JavaScript, and renders acceptably
   when browsed as raw Markdown on GitHub.

Non-goals for the first release: translation, comments, in-page editing, and
converting documentation trees older than 2.2.

## 2. What exists today

### 2.1 help.hubzero.org

The live documentation is served by a site-specific component,
`app/components/com_documentation` on help.hubzero.org (about 4,700 lines of PHP,
its own git repo, not part of hubzero-cms). Facts that shape the import:

- Storage is a nested-set table of articles (`parent_id`, `lft`, `rgt`, `level`,
  `path`, `alias`, `title`, `content`, `state`, `access`, publish window).
- Content is raw HTML from CKEditor. Two macros exist, `{{version}}` and
  `{{versionpath}}`, and `/site/documentation/...` image paths are rewritten to
  `/app/site/documentation/...` at render time.
- URL scheme is `/documentation/<version>/<path>`. The alias `current` resolves to
  the newest numeric version root. Each page has a PDF export, alone or with
  children.
- A public read API exists: `GET /api/documentation/articles/list?limit=N&start=M`
  returns every published article with its full HTML content and tree fields.
  This is the export path; no database access is required for the public trees.
- The database also holds unpublished trees for versions 1.0.0 through 2.2.0
  (the highest `lft` seen is 6923, so roughly 3,400 rows exist against 362 public
  ones). Those are not reachable through the API. Their media directories survive
  under `/var/www/help/app/site/documentation/{1-0-0,...,2-1-0,220}`. The 2.2
  tree matters: 2.4 was probably copied from it, and the copy may have dropped
  pages. Exporting it takes a script run as root on help.hubzero.org; see
  section 9.

Public inventory, pulled through the API and a full crawl on 2026-09-09:

| Root | Title | Pages | Words | Images | Code blocks |
|---|---|---|---|---|---|
| `240` | Hubzero CMS v2.4 | 251 | 146,206 | 175 | 583 |
| `platform_2_4` | Hubzero Platform v2.4 | 36 | 20,324 | 8 | 113 |

Breakdown of the 2.4 CMS tree by top-level section, from the site menu:

| Section | Pages | Words | Notes |
|---|---|---|---|
| installation | 7 | 987 | EL8 only: linux, webserver, php, database |
| introduction | 1 | 269 | |
| managers | 73 | 44,957 | admin guide; 34 per-component chapters |
| users | 40 | 23,833 | end-user guide, per feature |
| webdevs | 102 | 55,252 | developer guide; 566 code blocks |

Hubzero Platform tree: tooldevs (28 pages, 15k words), tool-administrators (4),
users (4). This covers the tool session layer: invoke scripts, submit, Jupyter,
file transfer, tool paths.

Freshness, by last-modified year of the 261 menu-reachable pages:

| Never | 2009–2013 | 2014–2016 | 2017–2020 | 2022 | 2025 |
|---|---|---|---|---|---|
| 34 | 37 | 146 | 32 | 6 | 6 |

Other counts that matter for conversion: 88 internal documentation links, 114
external links, 80 references to `/app/site/documentation` media, 23 tables, 6
macro uses, 20 pages under 60 words (section stubs), and a 404 at
`/documentation/roadmap` even though the landing page links to it.

### 2.2 In this repository

- `README.md` points readers to help.hubzero.org for installation, documentation,
  contribution guidelines, and the roadmap. All of those become local links.
- 218 `.phtml` help pages live under `core/components/*/site/help/en-GB/` and
  `admin/help/en-GB/`, rendered by `com_help` inside a hub. They are a second,
  smaller, also-stale documentation set. The plan folds their content into the
  managers and users books and leaves `com_help` pointing at the Pages site.
- There is no `docs/` directory on `2.4-main` today apart from untracked scratch
  files.
- Codebase size for the accuracy pass: 58 components (48 with `config.xml`,
  595 parameters in total), 39 plugin groups with 332 plugins, 101 modules,
  70 REST API controllers, 21 muse console commands, 1,400 migrations, about
  990k lines of PHP, and 18.7k language strings.
- `Hubzero\Api\Doc\Generator` already parses API controller docblocks to feed
  `com_developer`. It can be driven to emit Markdown.
- GitHub Pages is not enabled on hubzero/hubzero-cms. The default branch is
  `2.4-main`.

### 2.3 The reference builders

forterp and botshield share one builder design, and this is what gets ported:

- `gh-pages/site.json` lists the site identity and each top-level doc entry:
  slug, title, source, summary, featured flag, optional template.
- A `chapters` key on an entry (forterp) names a directory whose other `*.md`
  files become chapters of that book, titled from their first H1, in filename
  order (`01-`, `02-`, `A-`). This is the multi-book mechanism.
- `gh-pages/build_site.py` renders CommonMark plus tables with markdown-it-py,
  strips the H1 into the page title, builds an H2/H3 section nav, renders a
  chapter rail for books, rewrites relative `.md` links to pretty URLs, rewrites
  image paths, expands `<!--include: path-->` into fenced code from the repo,
  and writes `gh-pages/public/` with a `.nojekyll`.
- Three templates: `home.html`, `doc.html` (chapter rail), `page.html` (plain).
  One `site.css`, a logo, a cache-busting query string on the stylesheet.
- botshield adds `test_build_site.py`, `check_links.py`, a pinned
  `requirements.txt`, and a CI gate: the workflow rebuilds, runs the tests and
  the link check, and fails if the committed `gh-pages/public/` differs from a
  fresh build. `public/` is committed so the site is readable without a build.
- `pages.yml` triggers on `docs/**`, `gh-pages/**`, and the workflow file,
  builds with Python 3.12, uploads the artifact, and deploys with
  `actions/deploy-pages`. Pull requests build and check but do not deploy.

What the hubzero docs need beyond that:

- Nested sections. The old tree is up to seven levels deep and the managers and
  webdevs books have natural sub-sections (`managers/components/search/*`). The
  builder needs sections as subdirectories with their own `README.md`, and a
  collapsible rail.
- A version label from `site.json` shown in the header, so a later release can be
  published beside 2.4 without redesigning the site.
- Client-side search over a JSON index the builder emits.
- A redirects table so the old help.hubzero.org paths keep resolving.
- Admonitions, an "edit this page on GitHub" link, and a "last reviewed against"
  stamp per page.

## 3. Target layout

```
docs/
  README.md                     index of books (also the Pages docs landing)
  STYLE.md                      writing conventions for contributors
  media/                        shared images
  getting-started/              book: what a hub is, concepts, first steps
    README.md
    01-what-is-hubzero.md
    02-concepts.md              components, plugins, modules, groups, tools
    03-quick-tour.md
  installation/                 book
    README.md
    01-requirements.md
    02-el9.md  03-el8.md  04-docker.md  05-first-hub.md  06-upgrading.md
  managers/                     book: hub administration
    README.md
    01-administrator.md ... (configuring, users, content, extensions,
                             maintenance, spam, menus)
    components/                 section: one chapter per component
      README.md  answers.md  blog.md  ... wishlist.md
  users/                        book: using a hub
    README.md
    dashboard.md profile.md groups/ projects/ publications/ ...
  developers/                   book: extending the CMS (old "webdevs")
    README.md
    foundation/ basics/ components/ plugins/ modules/ templates/
    database/ services/ testing/ conventions/ tutorials/
  tools/                        book: the tool platform (old platform_2_4)
    README.md
    developers/ administrators/ users/
  reference/                    generated books, one directory each
    configuration/              from config.xml and plugin manifests
    api/                        REST endpoints from Api\Doc\Generator
    muse/                       console commands
    events/                     Event::trigger names and their payloads
  contributing/                 book: commits, coding style, tests, phpstan,
                                pull requests, security reporting
  releases/                     changelog and upgrade notes
  plan/                         this file and other plans (not published)
  _import/                      raw import staging, deleted at the end of phase 2

gh-pages/
  README.md  site.json  build_site.py  check_links.py  test_build_site.py
  requirements.txt  redirects.json  templates/  assets/  public/
tools/docs/
  import_help.py  gen_config_reference.py  gen_api_reference.py
  gen_muse_reference.py  gen_events_reference.py  screenshots.py
.github/workflows/pages.yml
```

Conventions, to be written out in `docs/STYLE.md` during phase 0:

- One H1 per file, and it is the page title. Chapters are `NN-slug.md`; sections
  are directories with a `README.md` landing page.
- Relative `.md` links only, so pages work on GitHub and on the site.
- Images under the book's `media/` directory, referenced relatively, with alt text.
- Fenced code blocks always carry a language. Repository code is pulled in with
  the include directive rather than pasted.
- Each page carries an HTML comment header:
  `<!-- status: imported | reviewed | rewritten; reviewed-against: 2.4-main @ <sha>; date -->`.
  The builder surfaces the status as a banner on imported pages and as a footer
  stamp on reviewed ones.
- The product name is Hubzero. Not HUBzero, not HubZero.
- Admonitions use blockquotes with a bold lead: `> **Note:**`, `> **Warning:**`.

## 4. Branch and versions

The documentation system and the 2.4 books live on `2.4-main`, the default
branch. Documentation changes ride in the same pull request as the code they
describe. The Pages workflow runs on `2.4-main` and publishes to the site root.

The builder reads a version label from `site.json` and shows it in the header.
When a later release needs its own documentation, the same workflow can build that
branch's `docs/` into a versioned subpath. Nothing about that is designed now.

The 2.2 tree is not a separate section of the site. It is merged into the 2.4
import (phase 1) so that pages the 2.4 copy lost come back, and duplicates are
resolved once. Trees older than 2.2 are not converted.

## 5. Phases

### Phase 0: Foundation (about 1 week)

Deliverable: an empty but live site at hubzero.github.io/hubzero-cms with the
builder, CI gate, and conventions in place.

1. Port `gh-pages/` from forterp (chapters) and botshield (tests, link check,
   staleness gate). Add nested sections, the status banner, the edit link, the
   redirects table, and the search index. Keep the dependency footprint at
   markdown-it-py plus pytest.
2. Write `site.json` with the book list from section 3 and `docs/README.md`.
3. Write `docs/STYLE.md`.
4. Add `.github/workflows/pages.yml` and enable Pages with source "GitHub
   Actions" (one `gh api` call, recorded in the commit message as botshield did).
5. Point `README.md` at the new site and drop its roadmap link.
6. Add `docs/LICENSE.md` (MIT, the same as the code).

### Phase 1: Import and merge (about 4 weeks)

Deliverable: every 2.4 page, plus every 2.2 page that 2.4 lacks, rendered on the
site under the legacy structure, marked "imported, not yet reviewed", with old
URLs redirecting.

1. Export the whole documentation table with `tools/docs/export_help_db.php`
   (section 9). This supersedes the public API as the source because it includes
   the 2.2 tree and any unpublished 2.4 pages; the API stays as a fallback.
2. Align 2.2 and 2.4 by path. Three outcomes per page: only in 2.4, keep as is;
   only in 2.2, import it into the matching 2.4 section and mark it
   `merged-from: 2.2`; in both, diff the two. Identical or trivially different
   pages keep the 2.4 copy. Where 2.2 is longer or newer, keep 2.4 as the base
   and append the 2.2-only material under a clearly marked heading for the
   reviewer to reconcile in phase 3. Emit a merge report listing every decision.
3. `tools/docs/import_help.py`: read the export, then convert
   each article's HTML to Markdown. Pandoc handles the bulk; a post-processing
   pass fixes what it cannot: language guessing for the 696 `<pre>` blocks,
   `{{version}}` macros, CKEditor artifacts (`&nbsp;`, empty paragraphs, inline
   styles), and tables. Images are fetched from help.hubzero.org into the book's
   `media/` directory and re-linked.
4. Rewrite internal links to relative `.md` paths and emit
   `gh-pages/redirects.json` mapping every old `/documentation/...` path, 2.2
   and 2.4 alike, to its new page. The builder writes a meta-refresh stub for
   each entry.
5. Fold the 218 `com_help` `.phtml` pages into the same staging tree so they get
   triaged alongside the imported pages rather than separately.
6. Run the conversion on a branch, review a sample of 30 pages by hand across all
   books, fix the converter and the merge rules, rerun. Convert once; do not
   hand-edit until the converter is final.
7. Land the result. The 20 stub pages become section `README.md` files.

### Phase 2: Restructure (about 2 weeks)

Deliverable: the new information architecture from section 3, with landing pages,
navigation, and search working, still on unreviewed content.

1. Move pages into the new books. Merge the Platform trees into `tools/`, move
   `webdevs/index/contributions` and the conventions chapters into
   `contributing/`, move `webdevs/api` under `reference/api/` as the narrative
   introduction, and split the largest pages (the maintenance/tools page is
   nearly 4,000 words).
2. Write each book's `README.md` as a real landing page: who it is for, what is
   in it, and where to start.
3. Finalize `redirects.json` against the new paths and run the link checker.
4. Delete `docs/_import/`.

### Phase 3: Accuracy pass against the codebase (about 10 weeks, parallel)

This is the bulk of the effort and the part that can be fanned out. Each page moves
from `imported` to `reviewed` only after the reviewer has checked it against the
code on `2.4-main`, fixed or rewritten it, and regenerated any screenshots.

Per book:

- **Installation.** Verify against the `docker/` tree and the current package
  set. Add EL9 and Docker chapters; the existing text covers EL8 only.
- **Managers.** For each of the 34 component chapters, open the admin
  controllers, views, and `config.xml`, and confirm every described screen,
  field, and option exists. Cross-check with the corresponding `admin/help`
  `.phtml` page. Regenerate screenshots on a seeded 2.4 hub. Add chapters for
  components the old docs never covered: activity, oauth, saml, oaipmh,
  dataviewer, developer, checkin, services, categories, languages, redirect.
- **Users.** Same procedure against site controllers and views, feature by
  feature. The groups, projects, and publications chapters carry the most
  screenshots and drift the most.
- **Developers.** Verify every code sample against `core/libraries/Hubzero`:
  facades, the ORM (`Hubzero\Database\Relational`), routing, events, the module
  and plugin loaders, the view layer, muse. Replace pasted samples with include
  directives pointing at real files in `core/`. Add chapters for testing with
  phpunit and phpstan (both now configured at the repo root) and for CSP-safe
  JavaScript.
- **Tools.** The tool platform is not in this repository, so this book cannot
  be verified against code here. Write it from the 2.4 and 2.2 platform trees,
  merged and deduplicated, with every page left at `imported` status and a
  banner saying so. The platform side takes it from there once the rest is done.

Mechanics:

- One tracking table in `docs/plan/review-tracker.md` (page, book, status,
  reviewer, reviewed-against sha). The builder can also emit a coverage report
  from the status headers so the table never drifts from reality.
- Work in batches of 10 to 15 pages per pull request, grouped by component.
- Subagents do the code inspection and first-draft rewrite; a human reads the
  result before the status flips to `reviewed`.
- Screenshots come from `tools/docs/screenshots.py` driving Playwright against a
  local hub populated by seed scripts written for this purpose (sample articles,
  members, groups, resources, and so on). Extend the seed set as chapters need
  it.

### Phase 4: Generated references (about 4 weeks, parallel with phase 3)

Deliverable: four reference books regenerated by CI, with a check that fails when
the committed output is stale.

1. **Configuration reference.** `gen_config_reference.py` parses every
   `config.xml` and plugin manifest: one page per extension, one table per
   fieldset, with type, default, options, and the description string resolved
   from the language files. 595 component parameters plus plugin parameters.
2. **REST API reference.** Drive `Hubzero\Api\Doc\Generator` through a muse
   command or a small PHP script to dump the parsed docblocks as JSON, then
   render one page per component with endpoints, methods, parameters, and
   responses. 70 controllers. Docblock gaps found here become code fixes.
3. **Muse reference.** Each console command already carries help text; render it.
4. **Events reference.** Grep `Event::trigger(` and `->trigger(` across `core/`,
   group by plugin type, and list each event with its arguments and the plugins
   that listen. This is new material; the old docs never had it.

Each generator writes into `docs/reference/<book>/` and CI reruns it and diffs.

### Phase 5: New material (about 6 weeks)

Written from scratch, prioritized by the gaps the accuracy pass exposes. Known
gaps now:

- Upgrading a hub from 2.2 to 2.4.
- Security: CSP, authentication factors, SAML, OAuth, bot protection.
- Search: Solr setup and tuning.
- Developer environment: Docker, seeding, Playwright, phpstan.
- Accessibility guidance for template authors.
- Releases: a changelog.

### Phase 6: Cutover (about 2 weeks)

1. Point help.hubzero.org's `/documentation` at the Pages site with permanent
   redirects driven by `redirects.json`, or install a thin component that serves
   `gh-pages/public/` from a checkout. The redirect is simpler and is the
   recommendation.
2. Switch `com_help` in the CMS to link out to the site instead of rendering
   `.phtml` pages, then delete the 218 `.phtml` files in a later release.
3. Announce, update `README.md` badges, and close the com_documentation editing
   accounts.

## 6. Effort summary

| Phase | Calendar | Human effort | Agent-parallelizable |
|---|---|---|---|
| 0 Foundation | 1 week | 3 days | builder port, tests |
| 1 Import and merge | 4 weeks | 1.5 weeks | converter iterations, merge report, sampling |
| 2 Restructure | 2 weeks | 1 week | moves, landing pages |
| 3 Accuracy pass | 10 weeks | 5 weeks | code inspection, first drafts, screenshots |
| 4 Generated references | 4 weeks | 1 week | all generators |
| 5 New material | 6 weeks | 3 weeks | drafts from code |
| 6 Cutover | 2 weeks | 3 days | redirects, link checks |

About 23 calendar weeks with phases 3, 4, and 5 overlapping, and roughly 12 weeks
of human review time. The imported corpus is about 166k words; expect the reviewed
site to land near 200k words plus the generated references.

## 7. Decisions made (2026-09-09)

1. **Pre-2.4 documentation.** The 2.2 tree is exported and merged into the 2.4
   import, page by page, deduplicated, not kept as a separate section. Trees
   older than 2.2 are not converted.
2. **License.** MIT, the same as the code. `docs/LICENSE.md` says so and the
   site footer links to it.
3. **Web address.** `hubzero.github.io/hubzero-cms`. No custom domain.
4. **Tools book.** Documented from the material we have, marked unverified, and
   handed to the platform side once the rest of the site is done.
5. **Roadmap.** None. The roadmap links in `README.md` and the old site are
   removed rather than replaced.

## 8. First two weeks, concretely

1. Create `gh-pages/` on `2.4-main` by porting forterp's builder and botshield's
   tests, link checker, and workflow. Add nested sections and the status header
   parsing. Land with a `docs/README.md` and an empty book skeleton.
2. Enable Pages, confirm the deploy, and add the badge to `README.md`.
3. Write `docs/STYLE.md` and `docs/LICENSE.md` (MIT).
4. Run the database export (section 9) and check the 2.2 and 2.4 trees into
   `docs/_import/`, so the merge rules are designed against real data.
5. Write `tools/docs/import_help.py` against that export, convert, and review a
   30-page sample.
6. Start `docs/plan/review-tracker.md` from the export so phase 3 has a complete
   page list on day one.

## 9. Exporting the database

`tools/docs/export_help_db.php` dumps every row of the documentation table to
JSON, including the unpublished 2.2 tree, and prints one summary line per
version. It has to run as root on help.hubzero.org because
`hubconfiguration.php` is root-only. It prints no credentials.

```bash
scp tools/docs/export_help_db.php help.hubzero.org:/tmp/
ssh help.hubzero.org sudo php /tmp/export_help_db.php
mkdir -p docs/_import
scp help.hubzero.org:/tmp/documentation-export.json docs/_import/help-export.json
```

The export is roughly 15 MB of HTML. It is working data for phase 1 and is not
committed; `docs/_import/` is deleted at the end of phase 2.

## 10. Progress

- **2026-09-09.** Phase 0 done and merged to `2.4-main`: builder, tests,
  link checker, workflow, conventions, license, and the book skeleton.
  GitHub Pages is enabled and the first deploy succeeded at
  https://hubzero.github.io/hubzero-cms/.
- **2026-09-09.** Phase 1 done from the database export. The comparison
  settled the 2.2 question: 2.4 was a verbatim copy of 2.2 (all 302 shared
  pages identical), with 50 copied pages left unpublished (the EL8
  installation steps and add-ons, the extension requirements, parameters,
  and languages chapters, hub settings, browser support). Those are now
  imported and marked as unpublished sources. Of the 182 pages only in 2.2,
  the tool sections were identical to the Platform 2.4 tree, the CentOS 7,
  Debian, and RHEL installation trees and the internal documentation were
  left behind, and 14 pages came across: the security considerations
  section (now `managers/security/`), the tool developer prerequisites and
  process pages, and Autohub. The import report in `docs/_import/` lists
  every decision. 324 imported pages, 234 images, 614 redirects.
- **2026-09-09, phase 2 and phase 4.** The four generated references are
  in: configuration (39 component pages, 36 plugin group pages, 1,285
  parameters), REST API (31 component pages, 246 endpoints), muse (30
  commands), and events (41 groups, 279 events), each regenerated and
  diffed by CI. Hand-written landings for the installation, managers,
  users, and developers books; the thirteen one-command EL8 service pages
  and the two user-note pages are consolidated into single pages by
  importer rules, so reruns reproduce them; the pre-2.0 unpublished
  sections were dropped.
- **2026-09-09, phase 3 begins.** Twelve components reviewed against the
  code: knowledge base, answers, blog, forum, wiki, tags, events, citations,
  wishlist, support, newsletters, storefront. Twenty-four chapters rewritten,
  plus the framework foundation chapters. The reviews turned up real faults
  in every component; those fixed are in the history under `[CORE] Fix
  defects found while reviewing …`, and the ones needing a decision are in
  [review-findings.md](review-findings.md). Two repo-wide bug classes came
  out of it: 730 unimported facade calls, each a fatal error when its line
  runs, and 488 language keys nothing defines. Both now have a linter and
  the first runs in CI.
- **Still open from phase 2:** the com_help pages are converted into
  `docs/_import/com_help/` but not yet folded into chapters, and the
  largest pages have not been split.

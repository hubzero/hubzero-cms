# WCAG AA 2.1 Accessibility Fixes — Delta Science Gateway

This document tracks all WCAG accessibility remediation work performed on the
Delta Science Gateway (HubZero CMS) at `https://gateway.delta.ncsa.illinois.edu`.

Changes are split between the **dlt template** (CSS overrides, template markup)
and **core HubZero** (structural HTML, ARIA attributes, component logic).

---

## Template Changes (dlt)

### Global Template (`index.php`, `error.php`)

- **Header search form**: Changed `aria-label` to `aria-labelledby` on the
  search input to match Illinois Web Toolkit CSS selectors (`ilw-header`
  requires `aria-labelledby` for proper height styling). Added
  `aria-label="Site search"` on the form for unique landmark naming (SIA-R55).
- **Search button**: Removed `background-image` via CSS override — the
  toolkit SVG icon prevented contrast detection. Button shows text "Search"
  only (icon was purely decorative).
- **Cookie banner fix**: Added JS (MutationObserver) to inject
  `background-color: #13294b` on `#ilaCookieBXButton` and
  `.ila-cookieb__cookieb` after the Illinois cookie consent banner loads,
  so scanners can verify contrast on the "X" close button.
- **Main landmark**: Added `<main>` element wrapper
- **Viewport meta**: Allow user zoom (removed `maximum-scale=1`)
- **Cookie consent**: Integrated Illinois cookie consent script
- **Notifications section**: Removed redundant `role="region"` from
  `<section class="hub-top">` (implicit from `<section>` + `aria-label`)

### LESS / Compiled CSS (`less/`)

#### Buttons (`less/theme/components/_buttons.less`)
- Reduced button padding from `12px 20px` to `8px 20px` for better proportion
  with adjacent form inputs
- Fixed button font size and contrast ratios

#### Forms (`less/legacy/components/_forms.less`)
- Changed `.entry-search label` from `display:none` to visually-hidden
  pattern so screen readers can access form labels (SC 1.3.1)
- Improved form input contrast ratios

#### Pagination (`less/legacy/components/_pagination.less`)
- Fixed low contrast on current page number (`#777` → `#595959`, 7.0:1 ratio)
- Added focus indicators and proper disabled state colors

#### Tabs (`less/legacy/components/_tabs.less`)
- Fixed contrast ratios on inactive tab text

#### Typography (`less/theme/_typography.less`)
- Fixed link and body text contrast ratios to meet 4.5:1 minimum

#### Content Header (`less/theme/layout/_content-header.less`)
- Fixed heading text contrast

#### Template (`less/template/_template.less`)
- Added `sr-only` and `visually-hidden` utility classes
- Added `#getintouch` sidebar styles for accessibility statement page
- Fixed `blockquote` color contrast (`#888` → `#767676`, 4.54:1)

#### Colors (`less/tokens/_colors.less`, `less/theme/vars/_colors.less`)
- Updated color tokens to meet WCAG AA contrast minimums
- Darkened `@colorLink` from `#DD3403` to `#D13102` (4.52:1 on `#f2f2f2`
  gray sections, 5.06:1 on white)

#### Reset (`less/template/_reset.less`)
- Added `body { background-color: #fff }` — explicit background for WCAG
  contrast detection tools (SC 1.4.3). Without this, scanners can't verify
  contrast since the browser default white is not an explicit declaration.
- Added `background-color: #fff` on `main`, `.main`, `.aside`, `.subject`,
  `ilw-page` — scanners can't traverse through shadow DOM boundaries on
  Illinois toolkit web components.
- Added `ilw-content:not([transparent="true"]) { background-color: #fff }` —
  excludes `ilw-content[transparent]` which sits on dark colored sections
  (homepage banner, about section) with intentional white-on-dark text.
- Added `background-color: #fff` on `.container`, `.container-block`,
  `.innerwrap`, `.inner` — common content containers that are 10+ transparent
  ancestors deep from text elements. Without this, Siteimprove gives up
  traversing before finding a background color.

#### Components (`less/theme/components/_components.less`)
- Added visually-hidden pattern for `fieldset.entry-search label` (overrides
  core `display:none` which hides from assistive tech)
- Fixed comment form fieldset text contrast (`#777` → `#767676`, 4.54:1)
- Fixed comment form sidenote text contrast (`#aaa` → `#6e6e6e`, 4.55:1
  on `#f2f2f2` background)
- Fixed file uploader drop zone placeholder contrast (`#bbb` → `#888`
  on `#f7f7f7` background)
- Fixed usage overview stat label contrast (`rgba(0,0,0,0.4)` →
  `rgba(0,0,0,0.55)`, 4.81:1)
- Removed `background-image` from `#search-button` (ilw-header) and added
  explicit `background-color: #fff !important` so contrast tools can verify
  text without a background image blocking detection
- Added `background-color: #fff !important` on `ilw-header-menu-section a`
  and `ilw-header a` — ilw-header nav links are slotted into shadow DOM;
  `!important` needed to override toolkit's slotted content styles
- Added `background-color: #fff` and `position: relative; z-index: 1` on
  `.legendLabel` — lifts Flot chart legend labels above the transparent
  `canvas.flot-overlay` that covers the chart area for mouse events
- Added `background-color: #fff`, `position: relative`, `z-index: 1` on
  `fieldset > legend` — resolves scanner overlap detection where legend
  sits on the fieldset border

#### Navigation (`less/template/layout/page-head/navigation/_navigation.less`)
- Refactored `& when` CSS guards to mixin guards (LesserPHP compatibility)

#### Layout Mixins (`less/template/mixins/_layout.less`)
- Removed unsupported comparison guard on `.stretch()` mixin

### Component CSS Overrides (`html/`)

#### Members (`html/com_members/members.css`)
- Added `.com_members .inner { margin-top: 1.5em }` for breadcrumb spacing
- Fixed count badge contrast: solid backgrounds with sufficient ratios
  - Active: `#595959` bg + `#fff` text (7.0:1)
  - Inactive: `#d1d1d1` bg + `#555` text (4.88:1)
- Fixed page header arrow contrast (`#777` → `#757575`, 4.61:1)
- Fixed page header h3 contrast (`#999` → `#595959`, 7.0:1)
- Added `.breadcrumb-separator::before { content: "►" }` — renders the
  breadcrumb separator via CSS instead of a text character (avoids nonBmp
  contrast detection issues in axe-core)
- Added `#download-batch` export citations box styling (background, border,
  proper button sizing)
- Added `background-color: #fff` on `#page_menu li a` — explicit background
  on member profile tab navigation links for Siteimprove contrast detection

#### Knowledge Base (`html/com_kb/kb.css`)
- Fixed `.entry-identifier` icon contrast (`#999` → `#767676`, 4.54:1)
- Added `background-color: #fff` on `.categories a` — explicit background
  directly on category sidebar links so Siteimprove SIA-R69 can determine
  contrast (scanner can't traverse through shadow DOM to find body background)

#### Wiki (`html/com_wiki/wiki.css`)
- @import of core wiki CSS + override for admonition box text contrast
  (`#918263` → `#7f7257`, 4.53:1 on `#fffbe2` background)

#### Tags (`html/com_tags/tags.css`)
- @import of core tags CSS + override for "(none)" placeholder text contrast
  (`#ccc` → `#767676`, 4.54:1)
- Added `background-color: #fff` on `.com_tags .aside a` and
  `.com_tags .entries-menu a` — explicit background on tag category/filter
  sidebar links for Siteimprove contrast detection

#### Resources (`html/com_resources/resources.css`)
- @import of core resources CSS with accessibility overrides:
- Moved `.opensource_license` and `.closedsource_license` decorative icons
  from `background-image` to `::before` pseudo-elements so contrast tools
  can verify link text (SC 1.4.3)
- Replaced `.metaplaceholder` fixed-size box (215×135px with decorative
  speech-bubble GIF) with auto-sizing bordered notice box — original caused
  text overflow into adjacent content areas
- Moved `.viewalldocs` icon from `background-image` to `::before`
  pseudo-element (same pattern as license icons)

#### Autocompleter (`html/plg_hubzero_autocompleter/autocompleter.css`)
- @import of core autocompleter CSS + contrast fixes on token delete (×)
  buttons (SC 1.4.3):
  - Tags: `#DFD5AF` → `#917E47` on `#FFF6D3` (~4.7:1)
  - Groups: `#bdd1ad` → `#5A7A4A` on `#deecd4` (~4.5:1)
  - Members: `#a6b3cf` → `#4A5F80` on `#DEE7F8` (~4.5:1)

#### Resource Share Plugin (`html/plg_resources_share/share.css`)
- @import of core share CSS + override for share link text contrast
  (`#999` → `#767676`, 4.54:1)

#### Member Profile Plugin (`html/plg_members_profile/profile.css`)
- @import of core profile CSS + override for field label `.key` contrast
  (`#777` → `#767676`, 4.54:1)

### Template View Overrides (`html/`)

#### Support Tickets (`html/com_support/`)
- Added proper ARIA attributes to ticket forms
- Fixed form label associations

#### User Registration (`html/com_members/register/default.php`)
- Override `getLabel()` in Checkboxes/Radio to render spans instead of
  orphaned labels
- Added sr-only legends to fieldsets
- Added aria-label to "Other" text inputs
- Restructured ORCID field layout
- Added "Other" option to Select dropdowns with show/hide JS

#### User Login (`html/com_users/login/`)
- Removed explicit tabindex on login inputs
- Added login form CSS contrast fixes (`html/com_users/login.css`)

### Error Page (`error.php`)
- Improved semantic structure and contrast
- Fixed `.search-trigger` link: added `aria-label="Search"`,
  `aria-hidden="true"` on SVG, `class="vh"` on text span (SC 4.1.2)

### Home Page (`home.php`, `less/pages/home.css`)
- Fixed marketing button contrast (white text on colored backgrounds)
- Improved heading hierarchy
- Fixed `.sweet-home .marketing-button` contrast: added
  `background-color: rgba(255,255,255,0.85)` so navy text has sufficient
  contrast (was dark navy text on semi-transparent dark navy background)

---

## Core Changes (HubZero)

### Libraries

#### Pagination (`core/libraries/Hubzero/Pagination/Views/paginator.php`)
- Added `aria-current="page"` on active page (SC 1.3.1)
- Added `aria-label` on prev/next arrows (SC 4.1.2)
- Removed redundant Start/End links, added ellipsis (SC 2.4.4)
- Replaced float layout with flexbox
- Used solid colors for disabled state instead of opacity
- Fixed paginator select to not rely solely on JavaScript `onchange`

### Components

#### Wiki (`core/components/com_wiki/`)

- **MainPage default** (`default/MainPage.txt`): Added form label, fieldset
  legend, `role="search"`, `aria-label="Search wiki articles"`, removed
  redundant "Full Article List" link
- **MainPage database**: Updated stored wiki content with same WCAG fixes
  (legend, label, role="search", aria-label, redundant link removal).
  The `aria-label` distinguishes this from the site-wide "Site search"
  landmark to satisfy SIA-R55 (unique landmark names).
- **History view** (`site/views/history/tmpl/display.php`):
  - Added sr-only text to empty `<th>` elements ("Markup", "Actions")
  - Added `<label>` elements for `diff` and `oldid` radio buttons
- **Submenu** (`site/views/pages/tmpl/submenu.php`):
  - Removed redundant `title` attributes that duplicate visible link text
  - Removed `tooltips` class from action buttons
- **Compare view**: Fixed malformed `</dt>`/`</dd>` closing tags
- **CSS** (`site/assets/css/wiki.css`): Fixed `a.markup` contrast
  (`#999` → `#555`, 7.46:1)
- **Parser** (`plugins/wiki/parserdefault/parser.php`):
  - Removed invalid `role="code"` from `<pre>` elements (not a valid ARIA role)
  - Wrapped all `<pre>` output in `<code>` elements (`<pre><code>...</code></pre>`)
    so preformatted blocks are semantically marked as code content (SC 1.4.12).
    Applies to `_restorePre()` default output, error fallback, and space-indented
    block paragraphs (updated `_closeParagraph()` to emit `</code></pre>`).
  - Added `<thead>`/`<tbody>` structure and `scope` attributes to wiki table
    output (`scope="col"`, `scope="colgroup"`, `scope="row"`)
  - Prevented empty `<a>` tags when wiki link title resolves to empty string
  - Added inline backtick code processing in table cells before link parsing,
    preventing LaTeX bracket notation `[...]` from being misinterpreted as wiki links
- **Markdown parser** (`plugins/wiki/parsermarkdown/markdown/block/TableTrait.php`):
  Added `scope="col"` to `<th>` cells in header rows
- **Help:WikiFormatting default** (`default/Help_WikiFormatting.txt`,
  `plugins/wiki/parserdefault/default/Help_WikiFormatting.txt`): Added
  descriptive text after each demo heading in the "Headings" section
  display examples so they are not empty headings (SC 2.4.6). Also updated
  stored wiki page content in database (`jos_wiki_versions`).
- **Special:Cite template** (`site/views/special/tmpl/cite.php`): Wrapped
  BibTeX entry `<pre>` in `<code>` element (SC 1.4.12)
- **Help:Processors default** (`default/Help_Processors.txt`): Added header row
  to processors table (`||=Processor=||=Description=||`)
- **Help:WikiMath database**: Fixed inline `{{{<math>...</math>}}}` wiki markup
  to multi-line format so `<title>` renders in `<head>` and MathJax processes
  all 163 formulas (was only rendering 1 due to broken `<pre>` tag)
- **RecentPage macro** (`plugins/wiki/parserdefault/macros/recentpage.php`):
  Removed redundant "Read more" links (heading already links to article)

#### Members (`core/components/com_members/`)

- **Profile view** (`site/views/profiles/tmpl/view.php`):
  - Conditionally render `span.meta` only when it has content
  - Replaced `<span aria-hidden="true">►</span>` text character with
    `<span class="breadcrumb-separator" aria-hidden="true"></span>` +
    CSS `::before { content: "►" }` — eliminates nonBmp contrast
    detection issues while rendering identically
  - Only render `#page_notifications` div when there are messages,
    added `role="status"`
- **Credentials remind** (`site/views/credentials/tmpl/remind.php`):
  - Added `id="email"` to input to associate with `<label for="email">`
- **Credentials reset** (`site/views/credentials/tmpl/reset.php`):
  - Added `id="username"` to input to associate with `<label for="username">`
- **Profile plugin** (`plugins/members/profile/views/index/tmpl/default.php`):
  - Added `hidden` attribute to `<ul id="profile">` by default; revealed by
    `profile.js` when visible items exist (fixes empty list container on
    public profiles with all-hidden fields)
- **Profile plugin JS** (`plugins/members/profile/assets/js/profile.js`):
  - Added check in `initialize()` to reveal profile list when it has
    visible `<li>` items (not `.hide` or `.hidden`)

#### Member Navigation (JavaScript)
- Added `aria-label` to JS-generated member navigation `<select>`

#### Citations (`core/components/com_citations/`)

- **Controller** (`site/controllers/citations.php`): Disabled dead
  worldcatlibraries.org OpenURL lookup (10s timeout, OCLC discontinued
  November 2025). Code commented out for future replacement.

#### Resources (`core/components/com_resources/`)

- **License view** (`site/views/license/tmpl/default.php`): Wrapped license
  text `<pre>` in `<code>` element (SC 1.4.12)
- **Browse tags view** (`site/views/browse/tmpl/tags.php`): Fixed empty `<ol>`
  by using `count()` instead of truthiness check on collection object
- **Browse page**: Added missing `<h2>` heading (SC 1.3.1)
- **About plugin**: Changed `<h4>` → `<h3>` to fix skipped heading level
- **Cite-this**: Fixed `<h4>`/`<h3>` tag mismatch
- **Intro page**: Changed `<h2>` → `<h3>` for proper hierarchy
- **Search fieldset**: Added sr-only legend (SC 1.3.1)
- **Tag browser** (`plugins/resources/tagbrowser/`):
  - Restyled as Miller column browser with flexbox, disclosure arrows
  - Added keyboard navigation (arrow keys, Home/End, Space) with live
    region announcements
  - Fixed `setScroll()` jQuery/DOM mismatch
  - Removed invalid `role="listbox"`/`role="option"`/`aria-selected`
  - Added `aria-hidden` to pipe separators
  - Fixed metadata and details text contrast

#### Resources Plugins
- **Questions plugin** (`plugins/resources/questions/views/browse/tmpl/default.php`):
  Fixed empty `<tbody>` by using `count()` instead of truthiness check on
  collection object; also fixed in publications questions plugin
- **Reviews plugin**: Fixed CSS contrast
- **Share plugin**: Fixed CSS contrast on share links
- **FindThisText plugin** (`plugins/resources/findthistext/`): Disabled dead
  worldcatlibraries.org OpenURL lookup

#### Courses (`core/components/com_courses/`)
- Replaced 6-column table layout with accessible accordion panels
- ARIA attributes for expand/collapse with keyboard support
- Fixed XSS on grandchild titles via `$this->escape()`

#### Events (`core/components/com_events/`)
- Added sr-only legend to category filter fieldsets (SC 1.3.1)
- Fixed label `for`/`id` typo: `event-cateogry` → `event-category`
- Changed category label from `display:none` to sr-only (SC 1.3.1)
- Added `aria-label` to prev/next navigation arrows (SC 4.1.2)
- Removed low-contrast `#aa9` color from calendar cells (SC 1.4.3)

#### Groups (`core/components/com_groups/`)
- Consolidated redundant image+title links into single card link (SC 2.4.4)
- Added sr-only legend to search fieldset (SC 1.3.1)

#### Projects (`core/components/com_projects/`)
- Consolidated redundant image+title links into single card link (SC 2.4.4)
- Removed redundant link on project image, set `alt=""` (SC 1.1.1)
- Removed self-link on project title (SC 2.4.4)
- Relocated "All Projects" button into project header
- Converted layout table `#infotbl` to definition list (SC 1.3.1)
- Set `alt=""` on decorative team member images (SC 1.1.1)

#### Blog (`core/components/com_blog/`)
- Generated unique IDs for anonymous checkboxes per comment (SC 4.1.1)
- Added sr-only legend to comment form fieldset (SC 1.3.1)
- Prevented empty `<ol>` from rendering when no comments exist

#### Help (`core/components/com_help/`)
- Added `lang` attribute to help page HTML
- Fixed broken viewport meta tag
- Wrapped content in `<main>` landmark
- Fixed link and footer text contrast in `help.css`
- Corrected heading skip (`<h4>` → `<h3>`) in courses help index

#### Search (`core/modules/mod_search/`)
- Added `role="search"` and `aria-label` to search form

#### Support (`core/components/com_support/`)
- Removed empty template files that overrode core views
- **New ticket view** (`site/views/tickets/tmpl/new.php`): Added
  `role="group"` on `#ajax-uploader` div so `aria-label` (set by JS
  from `data-instructions`) is valid on the element (SC 4.1.2)
- **New ticket JS** (`site/assets/js/new.js`): Added
  `attach.attr('role', 'group')` as belt-and-suspenders fix since a
  server-side process strips `role` from the template HTML output

#### CMS Content articles (database)
- **Article 37** (`jos_content`, /member page): Replaced empty `<p>&nbsp;</p>`
  at start of introtext with descriptive paragraph so the heading "How to
  become a member" has content after it (SC 2.4.6)

#### Knowledge Base articles (database)
- **Article 42** (`jos_kb_articles`): Fixed broken `<a>` tag in stored
  content — `page<a>.` was missing closing slash, producing stray `<a>`
  that bled through entire page DOM and broke `<ul>` list validation

#### Old Templates (`app/templates/delta/`)
- **index.php**: Fixed `.search-trigger` link — added `aria-label="Search"`,
  `aria-hidden="true"` on SVG, `class="vh"` on text span
- **error.php**: Same search-trigger fix

#### Login / Register (`core/components/com_users/`, `core/components/com_members/`)
- Removed explicit tabindex on login inputs
- Fixed PUCAS button contrast (darkened to `#6b5319`)
- Fixed reCAPTCHA: removed empty label, added visually-hidden label
- Fixed address field: added legend to fieldset
- Fixed ORCID field: set decorative image `alt=""`
- Override getLabel() for accessible checkbox/radio rendering
- Added sr-only legends, aria-labels, and proper field structure

### Plugins

#### Captcha (`core/plugins/captcha/image/`, `core/plugins/support/captcha/`)
- Fixed form label text for CAPTCHA input fields

#### Citations — Members (`core/plugins/members/citations/`)
- Disabled worldcatlibraries.org OpenURL lookup (commented out)
- Optimized: `total()` instead of `count()` (SQL COUNT vs PHP count)
- Reuse `grand_total` to avoid redundant queries
- Fixed citations browse view form labels and settings labels

#### Citations — Groups (`core/plugins/groups/citations/`)
- Disabled worldcatlibraries.org OpenURL lookup (commented out)

#### Usage (`core/components/com_usage/`)
- **Results view** (`site/views/results/tmpl/default.php`):
  - Added `aria-label="Usage categories"` to `<nav>` for unique landmark
  - Skip `<section>` wrapper for `no_html` iframe requests so embedded map
    pages render as valid standalone HTML with `<title>` in `<head>`

#### Usage Overview (`core/plugins/usage/overview/`)
- Prevented empty list from rendering when no data exists

#### GeSHi Syntax Highlighting (`core/plugins/content/geshi/geshi/geshi/`)
- Darkened low-contrast colors in 9 language files (php, php-brief, javascript,
  sql, xml, css, html4strict, diff, mysql) to meet WCAG AA 4.5:1 minimum

#### File Uploader (`core/assets/js/jquery.fileuploader.js` + 24 component/plugin JS files)
- Added `hidden` attribute and `aria-label="Uploaded files"` to empty
  `<ul class="qq-upload-list">` in uploader templates site-wide
- `hidden` removed in `_addToList()` when files are added
- Also fixed in 2 PHP templates (`plugins/projects/files/`)
- Fixes Siteimprove SIA-R68 (empty container element with `role="list"`)

#### Autocompleter Widget
- Added `aria-label` and label association

### Performance (related to accessibility)

#### Citations Page Load (10s → 67ms)
- Root cause: `_handleOpenURL()` curl request to dead
  `worldcatlibraries.org` with 10-second timeout
- OCLC discontinued the registry lookup API (November 2025)
- Disabled in 4 files (members, groups, resources, com_citations)
- Code commented out for future replacement when alternative found
- Additional optimization: replaced `getFilteredRecords()->count()`
  (fetches all rows) with `->total()` (SQL COUNT)

---

## WCAG Success Criteria Addressed

| Criterion | Description | Status |
|-----------|-------------|--------|
| 1.1.1 | Non-text Content | Fixed — decorative images use `alt=""`, `aria-hidden` |
| 1.3.1 | Info and Relationships | Fixed — legends, labels, landmarks, headings |
| 1.4.3 | Contrast (Minimum) | Fixed — all text meets 4.5:1 ratio |
| 1.4.4 | Resize Text | Fixed — removed viewport zoom restrictions |
| 1.4.12 | Text Spacing | Fixed — `<pre>` elements wrap content in `<code>` |
| 2.1.1 | Keyboard | Fixed — tag browser, accordions, pagination |
| 2.4.1 | Bypass Blocks | Fixed — landmarks (`main`, `search`, `nav`) |
| 2.4.2 | Page Titled | Verified |
| 2.4.4 | Link Purpose | Fixed — removed redundant links and titles |
| 2.4.6 | Headings and Labels | Fixed — proper hierarchy, no skipped levels, content after headings |
| 3.3.2 | Labels or Instructions | Fixed — all form inputs have labels |
| 4.1.1 | Parsing | Fixed — unique IDs, valid HTML |
| 4.1.2 | Name, Role, Value | Fixed — ARIA roles, states, properties |

---

## Build Notes

- **LESS compiler**: `php /var/www/delta/core/bin/lessc` (splitbrain/lesserphp v0.10.2)
- **Build command**: `php /var/www/delta/core/bin/lessc /var/www/delta/app/templates/dlt/less/main.less /var/www/delta/app/templates/dlt/less/main.css`
- LesserPHP does NOT support `& when` CSS guards — use mixin guards instead
- LesserPHP does NOT support comparison guards like `when (@var < 0px)`
- CSS overrides go in dlt template, not core files
- For component/plugin CSS: create `html/com_XXX/name.css` or `html/plg_folder_name/name.css`
  that `@import`s the original then adds overrides (template override replaces core file)
- For site-wide overrides: add to `_components.less` or the appropriate LESS file
- Testing: axe-core 4.10.3 via `axescan.py` (custom crawler + headless Chrome)
  - Scanner: `/var/www/delta/axescan.py` — crawls site and runs axe-core on each page
  - Reports: `/var/www/delta/reports/` (HTML + JSON)
  - Usage: `python3 axescan.py --max-pages 1000 https://gateway.delta.ncsa.illinois.edu/`
  - Default level: WCAG 2.1 AA (`wcag2a`, `wcag2aa`, `wcag21a`, `wcag21aa`)
  - Captures both `violations` AND `incomplete` results (most tools only check violations)
  - Latest scan: 1,705 pages, 0 violations, 48,124 passes

### Scanner Notes — Siteimprove vs axe-core

Siteimprove's SIA-R69 (contrast detection) is stricter than axe-core in two ways:
1. **Shadow DOM boundaries**: Siteimprove cannot traverse through shadow DOM
   on web components (`ilw-content`, `ilw-page`, `ilw-header`). Background
   color must be declared on the content side of the boundary, not just on
   `body` behind it.
2. **Incomplete results**: axe-core reports background-image issues as
   "incomplete" (needs manual review). Most axe integrations (pa11y,
   Lighthouse, axe-cli) only surface "violations" and silently drop
   "incomplete" items. Our scanner captures both.

### File Ownership

Template CSS overrides and LESS files must be owned by `hubadmin:access-php`
(not the editing user). After editing, run:
```
sudo chown hubadmin:access-php <file>
```

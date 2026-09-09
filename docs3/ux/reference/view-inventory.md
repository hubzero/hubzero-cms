# Component View Pattern Inventory

Catalog of UI patterns across all site-facing components, used to prioritize
which global blade components to build and which components to convert first.

## Pattern Frequency

Counted across ~34 components with site views, ~170 view directories.

| Pattern | Count | Components |
|---------|-------|------------|
| **Browse/List** | 30+ | answers, blog, cart, citations, collections, content, courses, events, forum, groups, jobs, kb, members, newsletter, poll, projects, publications, resources, search, storefront, support, tags, tools, usage, whatsnew, wiki, wishlist |
| **Detail/Single** | 25+ | answers, blog, cart, citations, collections, content, courses, developer, events, groups, help, jobs, kb, newsletter, publications, resources, storefront, support, tags, tools, wiki, wishlist |
| **Edit/Create Form** | 25+ | answers, blog, cart, citations, collections, courses, developer, events, feedback, forum, groups, jobs, members, newsletter, oauth, projects, publications, resources, support, tags, tools, wiki, wishlist |
| **Wizard/Multi-step** | 8 | cart (checkout), citations (import), courses (form), projects (setup), publications (submit), resources (steps), tools (pipeline/resources) |
| **Upload/Media** | 12 | blog, collections, courses, feedback, groups, members, publications, resources, support, tools, wiki |
| **Delete Confirmation** | 5 | blog, collections, courses, resources, wiki |
| **Dashboard/Overview** | 10 | cart, courses, developer, groups, jobs, projects, resources, storefront, support, usage |
| **Comments** | 6 | answers, blog, forum, kb, wiki, wishlist |
| **Profile** | 3 | jobs (seeker), members (profile), groups (view) |
| **Settings/Config** | 5 | groups, jobs, members, newsletter, tools, wishlist |
| **Search/Filter** | 5 | answers, forum, resources, search, wiki |
| **Email Templates** | 15 | answers, blog, cart, courses, events, forum, groups, jobs, members, newsletter, projects, publications, resources, support, wishlist |
| **Special/Utility** | 15+ | error pages, tombstones, restricted access, thanks/confirmation, redirects |

## Global Blade Components — Status

All components below are **built** and have CSS in `site.src.css`. Use them
when converting views — prefer components over raw HTML. See
[Global Blade Components](global-components.md) for the full API reference.

Status notes in this document refer to **site-facing conversion unless stated
otherwise**. Admin progress is tracked separately in the admin section below.

### Page Structure (use in every view)

| Component | Pattern | Status |
|-----------|---------|--------|
| `<x-page-container>` | Header + body + sidebar + tabs | Done |
| `<x-page-header>` | Standalone page header (rare) | Done |
| `<x-page-layout>` | Main + sidebar grid (rare) | Done |

### Forms

| Component | Pattern | Status |
|-----------|---------|--------|
| `<x-form-section>` | Fieldset + legend + body | Done |
| `<x-form-field>` | Label + input + hint + error | Done |

### Content Display

| Component | Pattern | Status |
|-----------|---------|--------|
| `<x-empty-state>` | Icon + message + action | Done |
| `<x-card-grid>` | Responsive card grid | Done |
| `<x-author-card>` | Avatar + name + affiliation sidebar | Done |
| `<x-tag-cloud>` | Tag pill list | Done |
| `<x-stat-card>` | Metric + label + trend | Done |

### Interactive

| Component | Pattern | Status |
|-----------|---------|--------|
| `<x-comment>` | Avatar + meta + body + replies | Done |
| `<x-vote-widget>` | Up/down vote + score | Done |
| `<x-confirm-dialog>` | Delete confirmation card | Done |
| `<x-step-nav>` | Numbered step indicators | Done |
| `<x-media-upload>` | Dropzone + file list + upload | Done |

### Not yet built

| Component | Pattern | Notes |
|-----------|---------|-------|
| `<x-calendar-grid>` | Month/week/day grid | events (specialized, low reuse) |

## Conversion Priority

Components ranked by user-facing importance and pattern coverage:

### Wave 1 — Core content (highest traffic, most shared patterns)

| Component | Views | Key Patterns | Current Site Status |
|-----------|-------|--------------|---------------------|
| **com_blog** | 2 | Browse, Detail, Edit, Delete, Comments, Media | Blade-enabled (8 files) |
| **com_resources** | 11 | Browse, Detail, Wizard, Media, Search | Done (35 files): Intro, Browse, Browse Tags, View (8 type layouts + 9 partials), Create (display/delete/thanks), Steps (10 templates), Play, Watch; admin done (33) |
| **com_publications** | 10 | Browse, Detail, Wizard, Curation, Media | Done (22 files); admin done (18) |
| **com_members** | 6 | Profile, Browse, Edit, Register, Media | Done (27 files): Browse, View (profile tabs + sidebar partial), Display, Edit, Activity, Change Password, Raise Limit, Spamjail, Unapproved, Member Card partial; Credentials (remind, reset, verify, setpassword); Register (default, create, confirm, change, select, send, unconfirmed, update, raceethnic); ORCID (display, redirect); Media (upload); admin done (60+) |
| **com_tags** | 1 | Landing/Cloud, Faceted Results, Browse Table, Edit, Cloud Partial | Done (5 files) |

### Wave 2 — Collaboration (groups, projects, wiki)

| Component | Views | Key Patterns | Current Site Status |
|-----------|-------|--------------|---------------------|
| **com_groups** | 7 | Dashboard, Browse, Edit, Delete, Media, Pages | Done (38 files): groups/ (11: _content, suggest, unapproved, delete, _group, browse, display, _customfields, _toolbar, _menu, view, edit); membership/ (2: invite, request); categories/ (2: display, edit); modules/ (2: display, edit); pages/ (13: display, list, item, manager, versions, edit, _view, _view_default, _view_component, _view_php, _view_login, _view_notapproved, _view_unpublished); media/ (7: filebrowser, filelist, movefile, movefolder, renamefile, renamefolder, newfolder); 1 new JS file (groups.ckeditor-insert.js CSP fix); 16 email templates skipped |
| **com_projects** | 6 | Dashboard, Browse, Wizard, Settings | Done (37 files): Intro (`page-container`, `empty-state`), Browse (`page-container`, `search-bar`), Internal (bespoke layout: _topheader, _header, _topmenu, _menu, _options, _image), External (bespoke layout), Features (`page-container`), Review, Pending, Invited, Suspended, Provisioned, Restricted; Setup (describe, team, finalize, edit + _form, _steps → `step-nav`, _title, _metadata, _picture, _sections, _edit_info, _edit_team, _edit_grant_info); Reports (display, custom); Change Owner; Error (`page-container`); 12 email templates skipped; 1 new JS file (reports-charts.js); admin done (5) |
| **com_wiki** | 5 | Detail, Edit, Delete, Comments, History, Media | Done (28 files); admin done (8) |
| **com_forum** | 4 | Sections, Category/Threads, Edit, Search, Thread Detail, Comments (recursive) | Done (8 files); admin done (8) |

### Wave 3 — Interactive (forms-heavy, specialized)

| Component | Views | Key Patterns | Current Site Status |
|-----------|-------|--------------|---------------------|
| **com_support** | 25 | Ticket list (3-pane), Edit ticket, Statuses, Categories, Messages, ACL, Stats, Queries, Abuse reports | Blade-enabled (17 files); admin done |
| **com_courses** | 10 | Dashboard, Browse, Detail, Edit, Delete, Copy/Fork, Offering LMS shell, Enrollment, Certificate | Done (25 files): Intro (`page-container`, `search-bar`, `card-grid`, `empty-state`), Browse (`page-container`, `search-bar`, `filter-tabs`, `sidebar-card`, `empty-state`), Badge; Course detail (bespoke plugin-tab layout with inline edit partials), Edit/Copy/Fork/NewOffering (`page-container`, `form-section`, `form-field`), Delete (`page-container`, `confirm-dialog`), Instructor/Button partials; Offering display (bespoke two-pane LMS shell), Enroll/Closed/Restricted; Certificate display; Managers (component-mode form); deferred: assets, forms, results, emails (plugin-rendered); admin done (34) |
| **com_tools** | 12 | Browse, Wizard, Sessions, Media | Blade-enabled (31 files); admin done (29) |
| **com_citations** | 8 | Intro, Browse, Detail, Edit, Import (3), Authors | Blade-enabled (8 files) |

### Wave 4 — Secondary (lower traffic, simpler views)

| Component | Views | Key Patterns | Current Site Status |
|-----------|-------|--------------|---------------------|
| **com_answers** | 3 | Browse, Detail, New, Voting, Comments | Blade-enabled (5 files) |
| **com_wishlist** | 2 | Browse, Detail, Edit, Settings, Comments, Voting | Blade-enabled (7 files) |
| **com_kb** | 3 | Overview, Category/Browse, Detail, Comments | Blade-enabled (4 files) |
| **com_events** | 5 | Calendar, Detail, Edit, Registration | Blade-enabled (19 files) |
| **com_jobs** | 10 | Browse, Detail, Edit, Dashboard, Resumes, Apply, Subscribe, Intro | Blade-enabled (13 files) |
| **com_collections** | 3 | Browse, Detail, Edit, Delete, Media | Blade-enabled (6 files) |
| **com_feedback** | 1 | Landing, Quotes, Story Form, Poll, Thanks | Done (5 files) |
| **com_poll** | 1 | Browse, Vote, Results | Done (3 files) |
| **com_whatsnew** | 1 | Browse by category/period, RSS feeds | Done (1 file) |

### Wave 5 — Utility & commerce

| Component | Views | Key Patterns | Current Site Status |
|-----------|-------|--------------|---------------------|
| **com_newsletter** | 4 | Detail, Subscribe/Unsubscribe, Email Preferences, Reply | Done (9 files); admin done (19) |
| **com_cart** | 16 | Checkout wizard (7), Browse, Orders, Download, Login, Complete | Blade-enabled (16 files) |
| **com_storefront** | 7 | Overview (auth-gated landing), Homepage (category grid), Collection browse, Product detail, Search | Done (6 files); admin done (31) |
| **com_developer** | 21 | Portal landing, API docs/endpoint/console/status, App browse/detail/edit/tokens, OAuth authorize, Tool/Web stubs | Blade-enabled (21 files) |
| **com_search** | 3 | Search form, results list, faceted sidebar (basic + solr engines) | Done (5 files); admin done (11) |
| **com_help** | 2 | Embedded help (plain iframe views, no page-container) | Blade-enabled (2 site + 2 admin) |
| **com_oauth** | 1 | OAuth authorization consent | Done (1 file) |

### Existing Site Blade Work Outside the Priority Waves

| Component | Blade files | Status |
|-----------|-------------|--------|
| **com_content** | 9 | Blade-enabled |
| **com_dataviewer** | 3 | Blade-enabled (Spreadsheet display + unauthorized + gallery — embedded DataTables grid; gallery is standalone popup, no page-container) |
| **com_login** | 8 | Blade-enabled (Login/Logout, MFA, Consent, Account Link, SSO Logout, Redirect) |
| **com_mailto** | 2 | Blade-enabled (Send form, Sent confirmation — popup/component shell, no page-container) |
| **com_redirect** | 1 | Blade-enabled (External link redirect interstitial with countdown) |
| **com_usage** | 1 | Done (1 file — tabbed plugin-generated statistics) |
| **com_users** | 8 | Done — hybrid: login/logout use auth-shell; MFA, consent, link, SSO logout use page-container |

### Not converting (admin-only or non-HTML site endpoints)

com_activity, com_billboards, com_categories,
com_config, com_cpanel, com_installer, com_languages,
com_menus, com_modules, com_oaipmh (site endpoint outputs XML/XSLT, not HTML),
com_plugins, com_saml, com_services, com_system, com_templates

## Shared Partials Pattern

Many components use `_comment.php`, `_list.php`, `_vote.php` underscore-prefixed
partials included via `$this->view()`. These map naturally to blade components
since `<x-comment>` is cleaner than `$this->view('_comment')->set(...)`.

Components with `_comment.php`: answers, blog, forum, kb, wiki, wishlist
Components with `_list.php`: answers, blog, collections, jobs, publications, resources, wishlist
Components with `_vote.php`: answers, kb, wishlist

## Admin Views

~51 components have admin views with ~250+ view directories. Admin conversion
is **active** — the `hzadmin` template provides a daisyUI-based admin shell
with backward-compatible dual-shell support (legacy views fall back to
Kameleon automatically).

See [Admin Views](../guides/admin.md) for the full reference, including admin global
components (`<x-admin-form>`, `<x-admin-edit>`, `<x-admin-toolbar>`).

Admin views follow these patterns:
- **List / Display** — filter bar, sortable table, bulk checkboxes, pagination
- **Edit / Form** — two-column grid, form sections, metadata sidebar
- **Bulk operations** — two-panel select-and-target (merge, pierce, export)
- **Action panels** — radio buttons controlling conditional form sections
- **Static overview** — info cards with no data (config-only components)
- **Placeholder** — warning alert + preferences link (no admin UI)
- **Visualization** — D3 graphs, maps, charts with component JS/CSS

All converted admin views are **CSP-compliant** — no inline JavaScript
(`onclick`, `onchange`, `<script>` blocks) or inline CSS (`style=""`,
`<style>` blocks). Event handling uses `data-*` attributes with delegation
in `admin.js`.

### Admin Blade Conversion Status

| Component | Views | Blade Files | Status |
|-----------|-------|-------------|--------|
| **com_kb** | articles (display, edit), categories (display, edit) | 4 | Done |
| **com_blog** | entries (display, edit), comments (display, edit) | 4 | Done |
| **com_answers** | questions (display, edit), answers (display, edit) | 4 | Done |
| **com_poll** | polls (display, edit) | 2 | Done |
| **com_activity** | activity (display) | 1 | Done |
| **com_billboards** | billboards (display, edit), collections (display, edit) | 4 | Done |
| **com_citations** | citations (display, edit, stats), sponsors (display, edit), types (display, edit), format (display) | 8 | Done |
| **com_collections** | collections (display, edit), items (display, edit), posts (display, edit) | 6 | Done |
| **com_cron** | jobs (display, edit, run) | 3 | Done |
| **com_developer** | applications (display, edit) | 2 | Done |
| **com_categories** | categories (default, edit) | 2 | Done |
| **com_events** | events (display, edit), pages (display, edit), respondents (display, respondent) | 6 | Done |
| **com_config** | application (default), component (default) | 2 | Done |
| **com_feedback** | quotes (display, edit) | 2 | Done |
| **com_forum** | sections (display, edit), categories (display, edit), threads (display, edit, thread, media) | 8 | Done |
| **com_help** | help (overview, display) — admin; help (display, index) — site. Plain embedded views (iframe/popup), no `<x-page-container>` | 2+2 | Done |
| **com_jobs** | jobs (display, edit), categories (display, edit), types (display, edit) | 6 | Done |
| **com_newsletter** | newsletters (display, edit, send, test, _dependency), mailings (display, tracking), mailinglists (display, edit, manage, addemail, editemail), stories (edit, _autogen), templates (display, edit), campaigns (display, edit), tools (display) | 19 | Done |
| **com_cpanel** | cpanel (default, module) — two-column module grid (icon + cpanel positions) | 2 | Done |
| **com_cache** | cleanser (cache, purge) | 2 | Done |
| **com_checkin** | checkin (default) | 1 | Done |
| **com_installer** | manage (default), migrations (display), packages (display, add, edit), repositories (display, edit), customexts (default, edit, fetched, merged), warnings (default, composer) | 13 | Done |
| **com_system** | cache (display, apcu, opcache), geodb (display), info (default), ldap (display) | 6 | Done |
| **com_login** | Admin: login (default, factors, consent) + login page shell + `mod_adminlogin`. Site: login (default, login, logout), factors, userconsent, link, endsinglesignon, logout (redirect) | 3+2 admin, 8 site | Done |
| **Admin modules** | mod_adminlogin, mod_toolbar, mod_menu, mod_search, mod_reportproblems, mod_whosonline (default_admin) — all sidebar/topbar admin modules | 6 | Done |
| **Site modules** | mod_answers, mod_custom, mod_groups, mod_members, mod_resources, mod_supporttickets, mod_wishlist — sidebar widgets for site-facing templates | 7 | Done |
| **com_oaipmh** | config (display, schemas) | 2 | Done |
| **com_redirect** | links (display, edit, _addform) | 3 | Done |
| **com_saml** | saml (saml) | 1 | Done |
| **com_search** | solr (overview, manageblacklist), searchable (display, edit, documentlisting, _recordtable), boosts (list, new, edit), basic (display), shared (_submenu) | 11 | Done |
| **com_services** | services (display, edit), subscriptions (display, edit) | 4 | Done |
| **com_tags** | entries (display, edit, merge, pierce, _activity_log_item), relationships (display, meta), tagged (display, edit) | 9 | Done |
| **com_usage** | data (display) | 1 | Done |
| **com_wiki** | pages (display, edit, remove), versions (display, edit, remove), comments (display, edit) | 8 | Done |
| **com_wishlist** | lists (display, edit), wishes (display, edit), comments (display, edit) | 6 | Done |
| **com_menus** | menus (display, edit), items (display, edit, edit_options, edit_modules, display_batch), menutypes (display) | 8 | Done |
| **com_languages** | languages (display, edit, multilangstatus), installed (display), overrides (display, edit) | 6 | Done |
| **com_plugins** | plugins (display, edit, _options) | 3 | Done |
| **com_templates** | templates (display, files), styles (display, edit, _options, _assignment), source (edit), prevuuw (default) | 8 | Done |
| **com_modules** | modules (display, edit, _options, _assignment, _batch, select, positions) | 7 | Done |
| **com_messages** | messages (display, edit, view), configs (display) — inbox list, compose, read, settings popup | 4 | Done |
| **com_media** | media (default, default_folders), medialist (default, list, thumbs, list_folder, list_doc, thumbs_folder, thumbs_doc, thumbs_img, info, path) | 12 | Done |
| **com_cart** | orders (display, view, edit, items, _submenu), downloads (display, sku, _submenu) | 8 | Done |
| **com_storefront** | products (display, edit, remove), skus (display, edit, remove), collections (display, edit, remove), optiongroups (display, edit, remove), options (display, edit, remove), serials (display, new, upload, uploadcsv), meta (display, edit-software, remove, _sku-software), restrictions (display, new, addusers, upload, uploadcsv), whitelist (display, new, addusers) | 31 | Done |
| **com_content** | articles (default, featured, edit, _batch, modal, pagebreak) | 6 | Done |
| **com_resources** | items (display, edit, _edit_script, _edit_tool_fields, _edit_non_tool_fields, children, addchild, authors, author, aclusers, acluser, aclgroups, aclgroup, ratings, check), types (display, edit), licenses (display, edit), roles (display, edit), authors (display, edit), importhooks (display, edit), tags (display), imports (display, edit, run), media (display, list), plugins (display, manage) | 33 | Done |
| **com_members** | members (display, display_batch, add, edit, edit_user, edit_profile, edit_groups, edit_hosts, edit_messaging, edit_password, modal, profile, debug), accessgroups (display, edit, debug), accesslevels (display, edit), notes (display, edit, modal), messages (display, edit, settings), plugins (display, manage), points (display, edit, batch, find, config, _submenu), quotas (display, displayclasses, edit, editclass, import, _submenu), imports (display, edit, run, fields, _fieldmap), importhooks (display, edit), passwordrules (display, edit), passwordblacklist (display, edit), registration (display, _submenu), exports (display), groups (display), hosts (display), mail (display), media (display), incremental (display), premis (display, save), whosonline (display) | 60 | Done |
| **com_support** | tickets (display, edit, add, batch), categories (display, edit), statuses (display, edit), messages (display, edit), abusereports (display, view, check, _submenu), media (list, _asset), queries (display, edit, editfolder, list, condition), stats (display), acl (display, _acl_aro_row, _acl_aro_row_toggle_field) | 25 | Done |
| **com_groups** | manage (display, edit), membership (display, new), roles (display, edit, assign), pages (display, edit, errors, scan, menu), categories (display, edit), modules (display, edit), imports (display, edit, run, fields), importhooks (display, edit), customfields (display, edit, fetched, merged) | 31 | Done |
| **com_publications** | items (display, edit, editauthor, editcontent, versions, _selectaccess, _selectauthors, _selectcategory, _selectcontent, _selectgroup, _selectlicense, _statuskey), categories (display, edit), licenses (display, edit), types (display, curation) | 18 | Done |
| **com_projects** | projects (display, edit), activity (display), team (display, new) | 5 | Done |
| **com_tools** | pipeline (display, edit), handlers (display, edit), versions (display, edit, component), zones (display, edit, locations, picture), sessions (_submenu, display, classes, edit), hosts (display, edit), hosttypes (display, edit), locations (display, edit, _fields), preferences (display, edit), windows (_submenu, display, edit, sessions, unconfigured) | 29 | Done |
| **com_courses** | courses (display, edit), offerings (display, edit), sections (display, edit), units (display, edit), students (display, add, edit, csv), codes (display, edit, options), assetgroups (display, edit, duplicateassets), pages (display, edit, files, list), certificates (display, edit, preview), roles (display, edit), managers (display), supervisors (display), assets (display, edit), logo (display), media (display, list) | 34 | Done |

All admin components converted.

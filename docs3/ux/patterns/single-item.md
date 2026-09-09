# Single Item / Detail

Full display of a single resource, blog entry, publication, or record. The most
content-rich page type, combining the item body with metadata, related items,
and user actions (comments, ratings, sharing).

## When to Use

- Viewing a blog post, publication, resource, wiki page, or ticket
- Any page showing the full content of one record
- Detail views reached by clicking an item in a list

## daisyUI Components Used

- [Card](https://daisyui.com/components/card/) — sidebar widgets, comment form
- [Badge](https://daisyui.com/components/badge/) — tags, status
- [Avatar](https://daisyui.com/components/avatar/) — author/commenter photos
- [Button](https://daisyui.com/components/button/) — actions
- [Link](https://daisyui.com/components/link/) — inline links
- [Menu](https://daisyui.com/components/menu/) — sidebar link lists
- [Textarea](https://daisyui.com/components/textarea/) — comment input
- [Divider](https://daisyui.com/components/divider/) — section separators

## Global Components

Use these [global blade components](../reference/global-components.md) for detail pages:

- **`<x-page-container>`** — wraps page header + body + sidebar layout
- **`<x-author-card>`** — author info sidebar card
- **`<x-tag-cloud>`** — tag pill list in entry footer
- **`<x-comment>`** — individual comment with avatar, meta, actions, replies
- **`<x-vote-widget>`** — up/down vote buttons (answers, kb, wishlist)

## New Markup

```blade
<x-page-container title="Blog">
    @slot('actions')
        <a class="btn" href="{{ $feedUrl }}">Feed</a>
    @endslot

    @slot('sidebar')
        <x-author-card
            :name="$author->get('name')"
            :avatar="$author->picture()"
            :profileUrl="Route::url($author->link())"
            :affiliation="$author->get('organization')"
        />

        <!-- Archive by year/month -->
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-sm">Entries by Year</h2>
                <ul class="menu menu-sm p-0">
                    <li><a href="...">2026</a></li>
                </ul>
            </div>
        </div>
    @endslot

    <article>
        <header class="mb-6">
            <h2 class="text-3xl font-bold mb-2">{{ $entry->get('title') }}</h2>
            <div class="entry-meta">
                <time datetime="{{ $entry->get('publish_up') }}">...</time>
                <span>by <a href="...">{{ $author->get('name') }}</a></span>
            </div>
        </header>

        <div class="prose max-w-none mb-6">
            {!! $entry->get('content') !!}
        </div>

        <footer class="entry-footer">
            <nav aria-label="Tags">
                <x-tag-cloud :tags="$entry->tags()" />
            </nav>
        </footer>
    </article>

    <!-- Comments -->
    <section id="comments" class="entry-comments" aria-label="Comments">
        <h3 class="text-xl font-bold mb-4">Comments ({{ $commentCount }})</h3>

        <ul class="comment-list">
            @foreach($comments as $c)
                <x-comment
                    :id="$c->get('id')"
                    :name="$c->creator->get('name')"
                    :avatar="$c->creator->picture()"
                    :datetime="$c->get('created')"
                    date="..."
                    time="..."
                >
                    {!! $c->get('content') !!}

                    @slot('actions')
                        <a class="btn btn-xs btn-ghost"
                           href="?reply={{ $c->get('id') }}">Reply</a>
                    @endslot
                </x-comment>
            @endforeach
        </ul>
    </section>
</x-page-container>
```

## Structure Reference

| Element | Class(es) | Purpose |
|---------|-----------|---------|
| Page wrapper | `<x-page-container>` | Header + body + sidebar (global component) |
| Author card | `<x-author-card>` | Sidebar author info (global component) |
| Tag list | `<x-tag-cloud>` | Tag pills (global component) |
| Comment | `<x-comment>` | Avatar + body + actions (global component) |
| Entry metadata | `.entry-meta` | Date, author, comment count row |
| Body content | `.prose .max-w-none` | Rich text via `@tailwindcss/typography` |
| Article footer | `.entry-footer` | Tags + actions, top border |
| Comments section | `.entry-comments` | Top border + spacing |
| Comment list | `.comment-list` | Container for `<x-comment>` elements |
| Comment form | `.comment-form` | Comment submission form |
| Sidebar cards | `.card .shadow-sm` | Archive by year, popular entries |
| Sidebar links | `.menu .menu-sm` | Year/month navigation |

## Comment Nesting

Comments support configurable depth (default: 3 levels). The nesting pattern:

- **Reply flow**: Server-side `?reply=ID` query parameter. When set, the comment
  form shows a `<blockquote>` with the parent comment and sets
  `comment[parent]` hidden field.
- **Avatar sizing**: Top-level comments use larger avatars; nested replies use
  smaller ones. Controlled by `.comment` vs `.comment-reply` CSS classes (the
  template CSS sets avatar `width`).
- **Nesting container**: `.comment-replies` wraps nested replies with left
  border indentation. Each level adds another `.comment-replies` wrapper.
- **Depth limit**: Beyond `comments_depth`, no reply button is shown and no
  further nesting occurs.

## Guest vs Logged-In

- **Logged in**: Full comment form with textarea, anonymous checkbox, submit
  button. If replying, a blockquote of the parent comment appears above.
- **Guest**: The same `.card .bg-base-200` container shows a login prompt
  instead of the form. The login link includes a `return` parameter that
  redirects back to the entry's comment section after login.

## Pattern Variants

### Faceted results page (com_tags)

Not every "detail" page displays a single record. The com_tags view page shows
search results for one or more tags, with a category sidebar for narrowing
results and sort tabs (title/date). This is a hybrid between browse and detail:
it uses `<x-page-container>` with a sidebar category menu, tag description
cards, and a paginated result list rather than a single-record layout with
author cards or comments.

See `com_tags/site/views/tags/tmpl/view.blade.php` for the reference
implementation.

### Q&A detail page (com_answers)

The com_answers question page is a detail view but does not use `<x-author-card>`,
`<x-tag-cloud>`, or `<x-comment>`. The sidebar has a raw status/resource card
and conditional `<x-stat-card>` widgets for bonus points and max award (when
banking is enabled). Tags are rendered from model HTML. Responses and replies
use custom partials (`_response.blade.php`, `_reply.blade.php`) with
`<x-vote-widget>` for per-answer voting — a pattern that doesn't map to the
generic `<x-comment>` component because responses have voting, acceptance
state, and different metadata.

See `com_answers/site/views/questions/tmpl/question.blade.php` for the
reference implementation.

### Blog detail page (com_blog)

com_blog uses `<x-author-card>` for the sidebar author widget and
`<x-sidebar-card>` for archive and popular entries. It uses model-rendered tag
HTML instead of `<x-tag-cloud>`, and a custom recursive `_comment.blade.php`
partial instead of `<x-comment>` (because blog comments have reply threading,
edit/delete actions, and abuse reporting that don't map to the generic
component).

See `com_blog/site/views/entries/tmpl/entry.blade.php` for the reference
implementation.

### Forum thread detail (com_forum)

The forum thread page is a specialized detail view for threaded discussions. The
sidebar uses `<x-sidebar-card>` for tags (model-rendered cloud), participants
(access-level-aware member links with anonymous coalescing), and attachments
(linked file list). The main content area renders posts via recursive partials:
`_list.blade.php` builds a likes hash map keyed by postId and iterates comments,
rendering each via `_comment.blade.php`. The comment partial handles likes
(heart icon with data attributes for `like.js`), image/file attachments, action
links (delete, edit, reply, report abuse), and inline reply forms (hidden by
default, toggled by `forum.js`). Tree threading recurses `_list` → `_comment` →
`_list` up to a configurable depth. A bespoke comment form at the bottom uses
CKEditor with `@mentions`, tag autocompleter, file upload, and anonymous
checkbox — `<x-auth-gate>` handles the guest state.

See `com_forum/site/views/threads/tmpl/display.blade.php` and the
`_comment.blade.php` / `_list.blade.php` partials for the reference
implementation.

### Collection post detail (com_collections)

The collections post detail is a hybrid media/content page that can render
either as a full page or shell-less (`no_html`) for AJAX/iframe embedding.
The main content area handles three asset types — images (responsive gallery),
files (download links with size/type), and URLs (linked list) — followed by raw
tag badges and a simple flat comment list with inline comment form. There is no
`<x-author-card>`, `<x-tag-cloud>`, or `<x-comment>` usage; the view handles
all of these with bespoke markup because the asset-type branching and
embeddable rendering mode don't map to the generic detail story.

See `com_collections/site/views/posts/tmpl/display.blade.php` for the
reference implementation.

### Article detail page (com_content)

com_content's article view is the most param-driven detail page: nearly every
element — author, category, parent category, dates, hit count — is toggled by
`$item->params->get(...)` flags. The metadata row uses a flat `flex` layout
with `text-base-content/60` rather than an `<x-author-card>` sidebar widget.
Author names are optionally linked to `com_contact` entries. Category and
parent category can be linked or plain text.

The view handles three plugin event slots (`afterDisplayTitle`,
`beforeDisplayContent`, `afterDisplayContent`) and renders a pagebreak-generated
table of contents (`$item->toc`) when present. Article content goes through
`<div class="prose max-w-none">` with optional full-text images (left/right
float or centered) and pagination (before/after content, or relative).

Guest access uses a teaser pattern: when `show_noauth` is enabled and the user
is a guest, the intro text is shown with a "Register to read more" login button
instead of the full article.

The view uses only `<x-page-container>` — no sidebar, no `<x-author-card>`,
`<x-tag-cloud>`, or `<x-comment>`. This is intentional: com_content is a
CMS article engine where all display is param-controlled, not a social content
platform with author profiles and comments.

See `com_content/site/views/article/tmpl/default.blade.php` for the reference
implementation.

### Resource detail with tabbed content and launch area (com_resources)

The resource detail page is a two-pane layout within `<x-page-container>` with
a sidebar slot. The **upper pane** uses a 3-column grid: the left two columns
show the title (with conditional status badges and edit button) and a
contributor listing via `_contributors.blade.php`; the right column renders a
launch area with primary child links, supporting documents, and license info.
An access-restriction alert handles guest, group-only, and unauthorized states.

The **lower pane** renders tabbed content via `_tabs.blade.php` (daisyUI
`tabs tabs-bordered` with plugin-driven category tabs) and
`_sections.blade.php` (tab panel content from plugin events). The active tab
is tracked via a `tab` URL parameter.

The **sidebar** has two sections: a metadata card (`_metadata.blade.php` with
ranking/rating, type, submitter, and custom fields) and plugin-provided extra
content via `Event::trigger('resources.onResourcesSub')`. Module-rendered
extra content appears when the "about" tab is active.

No `<x-author-card>`, `<x-comment>`, `<x-vote-widget>`, or `<x-tag-cloud>` —
the contributor listing, plugin-driven tabs, and launch area are all
domain-specific patterns that don't map to the generic detail components.

See `com_resources/site/views/view/tmpl/default.blade.php`, `_tabs.blade.php`,
`_sections.blade.php`, `_contributors.blade.php`, and `_metadata.blade.php`
for the reference implementation.

### Publication detail with tabbed content and launch area (com_publications)

The publication detail page is a two-pane layout within `<x-page-container>`
with a sidebar slot, closely mirroring the com_resources detail pattern. The
**upper pane** uses a 3-column grid: the left two columns show the title (with
conditional status badges and edit button), a contributor listing via
`Html::showContributors()`, and a truncated abstract; the right column renders
a launch area with primary attachments, supporting documents, version info, and
license details. A download-disabled alert handles dataset restrictions.

The **lower pane** renders tabbed content via `Html::tabs()` (daisyUI
`tabs tabs-bordered` with plugin-driven category tabs) and `Html::sections()`
(tab panel content from plugin events). The active tab is tracked via a `tab`
URL parameter. A fork attribution line appears when the publication was forked
from another version.

The **sidebar** has two sections: a metadata card (`_metadata.blade.php` with
ranking/rating, reviews, sharing, and custom fields) and plugin-provided extra
content via `Event::trigger('publications.onPublicationSub')`. Module-rendered
extra content appears when the "about" tab is active.

An alternative launcher layout is available via `launcher_layout` config,
which renders a different template (`launcher.blade.php`) instead of the
standard two-pane view.

No `<x-author-card>`, `<x-comment>`, `<x-vote-widget>`, or `<x-tag-cloud>` —
the contributor listing, plugin-driven tabs, and launch area are all
domain-specific patterns that don't map to the generic detail components.

See `com_publications/site/views/view/tmpl/default.blade.php`,
`_metadata.blade.php`, `_contributors.blade.php`, and `_primary.blade.php`
for the reference implementation.

### Event detail with sub-tabs (com_events)

The event detail page is a hybrid view combining metadata display with
optional sub-pages and registration. It uses `<x-page-container>` with the
shared period tab bar (Year / Month / Week / Day) and a sidebar containing a
mini calendar card (raw markup, not `<x-sidebar-card>`).

The main content area has its own sub-tab row for the event: Overview,
custom pages (if any exist), and Register (if registration is open). The
overview tab renders event metadata in a `<table class="table">` inside a
card, with rows for category, description (prose-formatted), date/time
(with timezone handling), contact, address, extra info link, custom fields,
author, and tags. Conditional edit/delete buttons appear next to the event
title based on authorization.

No shared detail components (`<x-author-card>`, `<x-comment>`,
`<x-vote-widget>`, `<x-tag-cloud>`) are used — the table-based metadata
layout and sub-tab navigation are specific to the event data shape.

See `com_events/site/views/details/tmpl/default.blade.php` for the reference
implementation.

### Knowledge Base article detail (com_kb)

The KB article detail page pairs a prose article body with community feedback
features (voting and comments). The sidebar uses `<x-sidebar-card>` for a
category navigation menu with nested children and article counts per category,
mirroring the same sidebar used on the category browse page.

Article content renders through `<div class="prose max-w-none">` like other
detail pages. Below the content, the model's `tags('cloud')` method returns
pre-rendered tag HTML — the view outputs it directly rather than using a shared
tag component. A `border-t` divider separates the content from a footer row
containing `<x-vote-widget>` (helpful / not helpful) and a "last modified"
timestamp.

Comments use a custom recursive `_comment.blade.php` partial loaded via
`$__view->view('_comment')->set(...)->loadTemplate()`. Each comment includes
its own `<x-vote-widget>`, reply link (appends `?reply=ID#post-comment`),
and report-abuse link. Nesting is controlled by a `$maxDepth` parameter from
the article's config. The comment form at the bottom supports reply-to context
(shows a blockquote of the parent comment) and an anonymous posting checkbox.

Guest users see a "log in to post comments" message with a return URL that
bounces back to `#post-comment`. When comments are closed, a static message
replaces the form.

See `com_kb/site/views/articles/tmpl/article.blade.php` for the reference
implementation.

### Application detail with tabs (com_developer)

The developer portal application view is a tabbed detail page with three tabs
(Details, Tokens, Personal Access Token) rendered via `tabs tabs-border`. Each
tab loads a sub-template through `$__view->view()` conditional includes, with
the active tab tracked via an `&active=` URL parameter.

The Details sub-view shows client credentials (client_id, client_secret,
redirect_uri) as read-only `<code>` blocks, a description section, and a team
members list rendered via the reusable `_team.blade.php` partial (avatar +
name + remove button with `data-confirm`). The Tokens sub-view is a paginated
list of active access tokens with individual revoke and bulk "Revoke All"
destructive actions. The Personal Access Token sub-view is a one-time display
of a newly created token with a warning alert.

The sidebar uses `<x-sidebar-card>` with a "What's Next?" help menu linking to
related portal pages. The `:actions` slot contains a back-to-apps link; the
Edit/Settings button is rendered inside the custom `:tabs` header content.

See `com_developer/site/views/applications/tmpl/view.blade.php` for the
reference implementation.

### Poll results with sidebar switcher (com_poll)

com_poll's results page (`results.blade.php`) shows a full bar-chart results
table for one poll, with a sidebar containing a poll switcher list and stat
cards (total voters, first/last vote dates). Bar widths use the `data-style-width`
CSP-safe pattern. The sidebar poll list highlights the current poll. The
"latest poll" page (`latest.blade.php`) is a focused single-poll voting form
with `<x-empty-state>` fallback when no active polls exist.

See `com_poll/site/views/polls/tmpl/results.blade.php` for the reference
implementation.

### Product detail with add-to-cart (com_storefront)

The storefront product detail page is a commerce-oriented detail view with a
2-column grid: product image on the left, pricing/options/add-to-cart form on
the right. Product options render as radio groups, quantity as a select
dropdown, and the price adapts (range, single, or "out of stock" in
`text-error`). Option/SKU data is passed via `data-sf-options` for CSP-safe
JavaScript. Uses `<x-alert-list>` for notifications but no sidebar, no
`<x-author-card>`, and no comments — justified by the commerce interaction
pattern.

See `com_storefront/site/views/product/tmpl/display.blade.php` for the
reference implementation. Browse grids are documented in the
[List / Browse](list-browse.md#storefront-category-and-product-grids-com_storefront)
pattern.

### Wishlist wish detail with admin actions and comments (com_wishlist)

The wish detail page is a multi-section admin-capable detail view combining
the wish content, priority ranking, status management, bonus points, move
controls, comments, and an implementation plan — all on one page with
conditional sections based on user role and wish state.

The main content renders in a `card` with author avatar, proposer name/date,
subject heading, prose description, model-rendered tags, and `<x-vote-widget>`
for community voting. Admin action links (change status, move, make
public/private, edit, report abuse, withdraw) appear in a flex toolbar below
the content, each conditionally rendered based on permissions and wish state.

**Admin sections** (rendered below the article card when applicable):
- **Priority ranking** — a `table table-sm` with importance/effort select
  dropdowns, consensus values, and community vote widgets. Uses
  `\Components\Wishlist\Helpers\Html::formSelect()` for dropdowns.
- **Change status** — radio buttons via `<x-form-field type="checkbox">`
  (Pending / Accepted / Rejected / Granted) with a disabled state for
  assigned-to-other-user grants.
- **Add bonus** — points input with available balance display.
- **Move wish** — radio buttons for destination (general / resource / group)
  with transfer option checkboxes using `<x-form-field type="checkbox">` and
  `<x-form-section>`.
- **Delete confirmation** — `alert alert-warning` with yes/no action links.

**Comments** use recursive `_list.blade.php` → `_comment.blade.php` partials
(same pattern as com_blog and com_forum). Each comment has author avatar,
content, attachments (images and files), reply/report actions, and an inline
reply form with CKEditor and anonymous checkbox. Guest users see a login
prompt. The main comment form at the bottom includes file upload and
anonymous checkbox via `<x-form-field type="checkbox">`.

**Implementation plan** shows assignment status, due date, and plan content
(prose-rendered). Admins get an inline edit form with assign select, due date
input, revision checkbox, and CKEditor.

The sidebar uses `<x-sidebar-card>` for a single "Status" card with a
color-coded badge and status description.

See `com_wishlist/site/views/wishlists/tmpl/wish.blade.php`, `_list.blade.php`,
and `_comment.blade.php` for the reference implementation.

### Citation detail with metadata table (com_citations)

The citation detail page is a scholarly reference view displaying a single
citation record with structured metadata. The sidebar uses `<x-sidebar-card>`
for three widgets: an external link button (View Article — no title prop,
conditionally rendered when a URL is resolved), a download card (BibTeX and
EndNote export links with download icons), and a sponsors card (when the
citation has sponsors).

The main content area renders the citation title as an `<h2>`, followed by an
author list (semicolon-delimited, with linked `com_members` profiles when
author IDs are embedded as `{{id}}`), type/year/affiliation badges, optional
abstract in `prose` container, and a comprehensive metadata `<table>` with
conditional rows for journal, book title, publisher, editor, volume, issue,
pages, month, ISBN, DOI (linked), series, edition, school, institution,
address, location, organization, language, keywords, notes, URL, submitter
(linked profile), and submission date.

Below the metadata table, optional sections display associated resources
(linked list) and a "Find this text" block with DOI resolver and Google Scholar
search links.

No `<x-author-card>`, `<x-comment>`, `<x-vote-widget>`, or `<x-tag-cloud>` —
citations are reference records, not social content. The inline count label on
the browse page (`N Citations`) is a small `<span>` alongside tabs, not a
standalone heading, so `<x-results-heading>` is intentionally not used.

See `com_citations/site/views/citations/tmpl/view.blade.php` for the reference
implementation.

### Wiki page with hierarchical navigation and comments (com_wiki)

com_wiki's page detail (`display_default.blade.php`) is a unique detail pattern
combining rendered wiki markup content with hierarchical page navigation and
threaded comments. The sidebar uses a shared `_wikimenu.blade.php` partial that
renders three `<x-sidebar-card>` widgets: a search form, a navigation menu
(Main Page, All Pages, New Page links), and a tools menu (page-specific actions
like Edit, History, Rename, Delete — conditionally shown based on permissions).

The main content area starts with page breadcrumbs (parent page links), an
`_authors` partial showing creator/editor names and dates, and a `_submenu`
partial rendering page-level tabs (Page, Comments, History, Delete). The wiki
content renders through `$revision->get('pagehtml')` inside
`<div class="prose max-w-none">`. Tags are rendered via
`$page->tags('cloud')`.

Comments use recursive `_list.blade.php` / `_comment.blade.php` partials
(same pattern as com_blog and com_forum). Each comment includes an avatar,
name, date, content, and reply/delete/report-abuse actions. The comment form
uses `<x-form-field>` for the textarea input and an anonymous checkbox. Guest
users see a login prompt. The comment form sits on a separate comments tab
page (`comments/display.blade.php`).

The static variant (`display_static.blade.php`) skips the authors partial and
renders a simpler layout for system/help pages.

No `<x-author-card>`, `<x-comment>`, `<x-tag-cloud>`, or `<x-vote-widget>` —
the wiki's authors partial, recursive comments, and tags all use bespoke
markup because the wiki page hierarchy, permissions model, and sub-tab
navigation don't map to the generic components.

See `com_wiki/site/views/pages/tmpl/display_default.blade.php`,
`com_wiki/site/views/comments/tmpl/display.blade.php`, and the
`_comment.blade.php` / `_list.blade.php` partials for the reference
implementation.

### Tool pipeline status detail (com_tools)

The tool status page is a complex detail view combining `<x-step-nav>` (showing
lifecycle progress), a status-dependent content area with action buttons
(update, approve, publish, retire), and "What's Next" guidance sections. The
content changes substantially based on tool state (registered, created,
uploaded, installed, approved, published, retired). It includes admin-only
sections (approve/publish actions, version notes, contribtool admin links) that
conditionally render based on ACL. The page header uses action buttons via
`<x-slot:actions>` on `<x-page-container>`.

See `com_tools/site/views/pipeline/tmpl/status.blade.php` for the reference
implementation.

### Ticket detail with comments and status updates (com_support)

The ticket detail page is the largest single-item view in the codebase (~1200
lines). It combines a ticket header (status, severity, owner, group), a
threaded comment timeline, an attachment list, and a comprehensive update form
(status change, owner reassignment, severity, category, tags, access controls).
The comment list shows changelog entries alongside user comments, with
admin-only metadata (IP, referrer, user agent) conditionally rendered via ACL.

This view is intentionally bespoke — the ticket update form mixes status
selects (with optgroup for open/closed), owner autocomplete, tag management,
file uploads, and access control checkboxes in ways that don't map to
`<x-form-section>` or `<x-form-field>` patterns. Uses `<x-page-container>` for
the page shell.

See `com_support/site/views/tickets/tmpl/ticket.blade.php` for the reference
implementation.

### Job posting with applications board (com_jobs)

The job detail page combines the full job description with metadata cards and
an applications section visible only to job owners/admins. The sidebar uses two
`<x-sidebar-card>` widgets: one for context-aware action buttons (apply, edit,
unpublish — conditionally shown based on job status and user role) and one for
metadata display (category, type, posted/closing dates). The main content
renders the job description inside `<div class="prose max-w-none">`, an
optional contact info card, and a paginated applications list showing applicants
with cover letter excerpts. Uses `<x-page-container>` with sidebar slot.

See `com_jobs/site/views/job/tmpl/default.blade.php` for the reference
implementation.

## Accessibility Notes

- Use `<article>` for both the main entry and individual comments
- `<time datetime="...">` for all dates — machine-readable format
- Tag links use `rel="tag"` for semantic markup
- Comment avatars use empty `alt=""` (decorative; name is in adjacent text)
- Comment form uses `aria-label` since the heading is `<h4>` not the form's label
- Comments section uses `aria-label` on the `<section>` element
- Nested replies are visually indented via left border + padding (`.comment-replies`)
- Comment textarea has `<label>` associated via `.form-control` pattern
- Delete button uses `.btn-error` (red) **plus** text label "Delete" — color is
  never the sole indicator (WCAG 1.4.1)
- Metadata row uses `items-baseline` alignment so mixed font sizes align on
  the text baseline rather than centering vertically
- See [Accessibility Guide](../guides/accessibility.md) for full WCAG 2.2 AA requirements

### Bespoke group view shell (com_groups)

The group view page (`view.blade.php`) is a sanctioned exception to
`<x-page-container>` — it renders a bespoke two-pane layout with a sidebar and
main content area, following the same pattern as com_members' profile view and
com_projects' internal view.

**Sidebar** (`#page_sidebar`, `lg:w-64 shrink-0`) contains:
- Group logo (linked, `rounded-full w-24 h-24`)
- Membership toolbar (rendered by `Helpers\View::displayToolbar()`) — shows
  Join/Leave/Request buttons or a "Group Member" dropdown for members, and
  Invite/Edit/Pages/Delete actions for managers via daisyUI `dropdown`
- Plugin tab navigation (rendered by `Helpers\View::displaySections()`) —
  vertical list of plugin tabs with metadata badges (member count, page count)
  and access-restricted tooltips for non-members
- Group info section (`#page_info`) — discoverability, join policy, created date

**Main area** (`#page_main`, `flex-1 min-w-0`) contains:
- Group title (h2, linked) with a separator and active tab name (h3)
- Tags (on overview tab only)
- Notification alerts
- Plugin content (`{!! $content !!}`)

Supports `$no_html` mode for AJAX requests — outputs only `$content` without
the sidebar/header shell.

The **overview default page** (`_view_default.blade.php`) renders an "About the
Group" section with custom fields, followed by a member grid showing profile
avatars and names. Other page view partials handle component pages, PHP pages,
login-required notices, and unpublished notices.

See `com_groups/site/views/groups/tmpl/view.blade.php` and the
`pages/tmpl/_view*.blade.php` partials for the reference implementation.

### Course detail with plugin tabs and inline edit (com_courses)

The course detail page (`display.blade.php`) is the most complex single-item
view in the CMS. It uses `<x-page-container>` but decomposes the content into
six partials:

- `_intro_edit.blade.php` / `_intro_view.blade.php` — inline title/blurb/tags
  edit form (managers only) vs read-only display with group attribution
- `_plugin_tabs.blade.php` — sub-menu tabs driven by `Event::trigger()` with
  plugin-rendered content panels, plus a conditional "Add Page" tab for managers
- `_summary_edit.blade.php` / `_summary_view.blade.php` — inline length/effort
  editor vs summary table with offering enrollment buttons and section selector
- `_page_form.blade.php` — add/edit page form (managers only)

The **plugin tabs** use bespoke `tabs tabs-border` markup rather than
`<x-filter-tabs>` because: (1) the tab loop has side effects (setting pathway
and document title for the active tab), (2) there is a conditional "Add Page"
tab with an SVG icon that doesn't map to `<x-filter-tabs>`'s simple URL→label
contract, and (3) the tab list is plugin-driven with access control checks.

The **sidebar** contains instructor bios (via `_instructor.blade.php` partials)
and the summary section. The enrollment area handles three states: not enrolled
(enroll button), enrolled (enter course button), and section selection (dropdown
when multiple sections exist).

A draft banner appears for unpublished courses (visible to managers only).

See `com_courses/site/views/course/tmpl/display.blade.php` and its partials
for the reference implementation.

### Offering LMS shell (com_courses)

The offering display is a bespoke two-pane LMS layout that does **not** use
`<x-page-container>`. The left sidebar contains a plugin-driven navigation menu
(with icons, meta counts, and access control), and the main area renders
plugin HTML. This layout is similar to com_projects' internal view — an
application shell rather than a content detail page.

The view handles `no_html` and `tmpl=component` modes for AJAX loading, a
not-enrolled gate, and expired section warnings. The sidebar menu preserves
`data-icon` and `data-title` attributes from plugins.

See `com_courses/site/views/offering/tmpl/display.blade.php` for the reference
implementation.

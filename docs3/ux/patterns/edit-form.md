# Edit / Form

> **Layout note** — We avoid native `<fieldset>`/`<legend>` in favour of
> div-based `.form-section` cards (legends cut through card borders in most
> browsers).

Creating or editing content. Used for blog posts, resource submissions, user
profile editing, project setup, support tickets, and any data entry.

## When to Use

- Creating a new record (blog entry, resource, ticket)
- Editing an existing record
- User registration and profile editing
- Settings and configuration pages

## daisyUI Components Used

- [Input](https://daisyui.com/components/input/) — text inputs
- [Textarea](https://daisyui.com/components/textarea/) — multi-line inputs
- [Select](https://daisyui.com/components/select/) — dropdowns
- [Checkbox](https://daisyui.com/components/checkbox/) — checkboxes
- [Radio](https://daisyui.com/components/radio/) — radio buttons
- [File Input](https://daisyui.com/components/file-input/) — file uploads
- [Label](https://daisyui.com/components/label/) — inline labels inside inputs
- [Validator](https://daisyui.com/components/validator/) — validation feedback
- [Button](https://daisyui.com/components/button/) — submit/cancel actions

## Global Components

Use these [global blade components](../reference/global-components.md) for edit/form pages:

- **`<x-page-container>`** — wraps page header + body + sidebar; use
  `bodyClass="edit-form"` for form-specific input styling
- **`<x-form-section>`** — groups related form fields in a bordered card
- **`<x-form-field>`** — wraps a label + input + hint/error
- **`<x-media-upload>`** — sidebar file upload widget

## Form Sections

Use the `<x-form-section>` component to group related fields. It renders a
bordered card with an optional heading. CSS is provided by the template.

```blade
<x-form-section heading="Details">
    <x-form-field name="field-title" label="Title" required>
        <input type="text" id="field-title" name="entry[title]"
               class="input w-full" required />
    </x-form-field>
</x-form-section>
```

Or using raw HTML (the component generates the same structure):

```html
<div class="form-section">
  <h3 class="form-section-heading">Details</h3>
  <div class="form-section-body">
    <!-- fields go here -->
  </div>
</div>
```

## Form Actions (Save / Cancel)

**Standard: left-aligned, no container box.**

Buttons sit directly below the last form section, separated by a subtle top
border. The primary action (Save) comes first (leftmost), followed by the
secondary action (Cancel). This follows the Filament convention and NNGroup
recommendations for LTR form flows.

```html
<div class="form-actions">
  <button class="btn btn-primary" type="submit">Save</button>
  <a class="btn btn-ghost" href="...">Cancel</a>
</div>
```

### CSS (provided by template)

```css
.form-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding-top: 1.25rem;
  border-top: 1px solid var(--color-base-300);
}
```

### Rules

- **Primary first**: Save (`.btn-primary`) is always the leftmost button
- **No container box**: no background, no border-radius — just a top border
- **Left-aligned**: buttons align with the left edge of form fields
- **No icon on Save**: keep the primary button label-only for clarity
- **Cancel is a link**: use `<a class="btn btn-ghost">` so it navigates away
  without submitting

## New Markup

```blade
<x-page-container title="New Entry" bodyClass="edit-form">
    @slot('actions')
        <a class="btn" href="{{ $cancelUrl }}">Cancel</a>
    @endslot

    @slot('sidebar')
        <x-media-upload
            :action="Route::url('...')"
            accept="image/*"
            maxSize="2MB"
        />
    @endslot

    <form id="hubForm" method="post" action="..." class="space-y-6">

        <x-form-section heading="Details">
            <x-form-field name="field-title" label="Title" required>
                <input type="text" id="field-title" name="entry[title]"
                       class="input w-full" required />
            </x-form-field>

            <x-form-field name="field-content" label="Content">
                <textarea id="field-content" name="entry[content]"
                          class="textarea w-full" rows="15"></textarea>
            </x-form-field>

            <x-form-field name="field-tags" label="Tags"
                          hint="Comma-separated list of tags.">
                <input type="text" id="field-tags" name="tags"
                       class="input w-full" />
            </x-form-field>
        </x-form-section>

        <x-form-section heading="Publishing">
            <x-form-field>
                <label class="checkbox-label">
                    <input type="checkbox" class="checkbox"
                           name="entry[state]" value="1" />
                    <span>Published</span>
                </label>
            </x-form-field>

            <x-form-field name="field-publish-up" label="Publish Date">
                <input type="datetime-local" id="field-publish-up"
                       name="entry[publish_up]" class="input w-full" />
            </x-form-field>
        </x-form-section>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-ghost" href="...">Cancel</a>
        </div>

        <input type="hidden" name="entry[id]" value="0" />
        {!! Html::input('token') !!}
    </form>
</x-page-container>
```

## Field Patterns

Use the `<x-form-field>` component for all fields. It renders the label,
required marker, hint text, and error message around whatever input you
provide in the default slot.

### Text Input

```blade
<x-form-field name="field-name" label="Name" required
              error="{{ $errors->first('name') }}">
    <input type="text" id="field-name" name="name"
           class="input w-full" required />
</x-form-field>
```

### Textarea

```blade
<x-form-field name="field-bio" label="Biography"
              hint="Brief description, up to 500 characters.">
    <textarea id="field-bio" name="bio"
              class="textarea w-full" rows="5"></textarea>
</x-form-field>
```

### Select

```blade
<x-form-field name="field-category" label="Category">
    <select id="field-category" name="category_id" class="select w-full">
        <option value="">-- Select --</option>
        <option value="1">Option One</option>
    </select>
</x-form-field>
```

### Checkbox

```blade
<x-form-field>
    <label class="checkbox-label">
        <input type="checkbox" name="agree" value="1"
               class="checkbox" required />
        <span>I agree to the terms of service</span>
    </label>
</x-form-field>
```

### Radio Group

```blade
<x-form-field label="Priority">
    <div class="space-y-2">
        <label class="checkbox-label">
            <input type="radio" name="priority" value="low" class="radio" />
            <span>Low</span>
        </label>
        <label class="checkbox-label">
            <input type="radio" name="priority" value="high" class="radio" />
            <span>High</span>
        </label>
    </div>
</x-form-field>
```

### File Upload

```blade
<x-form-field name="field-file" label="Attachment"
              hint="PDF or Word document. Max 10 MB.">
    <input type="file" id="field-file" name="file"
           class="file-input w-full" />
</x-form-field>
```

## Structure Reference

| Element | Class / Component | Purpose |
|---------|-------------------|---------|
| Page wrapper | `<x-page-container bodyClass="edit-form">` | Header + body + sidebar |
| Section card | `<x-form-section>` | Groups related fields |
| Field wrapper | `<x-form-field>` | Label + input + hint/error |
| File upload | `<x-media-upload>` | Sidebar upload widget |
| Action bar | `.form-actions` | Left-aligned, top-border only |
| Text input | `.input` | Single-line text |
| Textarea | `.textarea` | Multi-line text |
| Select | `.select` | Dropdown |
| Checkbox/Radio | `.checkbox-label` + `.checkbox`/`.radio` | Toggle inputs |
| File input | `.file-input` | File upload |
| Submit | `.btn .btn-primary` | Save button |
| Cancel | `.btn .btn-ghost` | Cancel link |

## Validation JavaScript

When a field fails validation, update its ARIA state and show the error:

```js
function showError(field, errorEl) {
  field.setAttribute('aria-invalid', 'true');
  field.classList.add('input-error');  // daisyUI red border
  errorEl.classList.remove('hidden');
  errorEl.setAttribute('role', 'alert');  // screen reader announces
}

function clearError(field, errorEl) {
  field.removeAttribute('aria-invalid');
  field.classList.remove('input-error');
  errorEl.classList.add('hidden');
  errorEl.removeAttribute('role');
}
```

## Pattern Variants

### Checkbox toggle rows

For simple inline checkboxes (checkbox + label side by side), use
`<x-form-field>` with `type="checkbox"`. This renders a horizontal layout with
the checkbox slot first and the label text beside it, instead of the default
vertical label-above-input pattern.

```blade
<x-form-field name="saveAddress"
              :label="Lang::txt('COM_CART_SAVE_ADDRESS_FUTURE')"
              type="checkbox">
    <input type="checkbox" class="checkbox checkbox-sm"
           name="saveAddress" id="saveAddress" />
</x-form-field>
```

See `com_cart/site/views/checkout/tmpl/shipping.blade.php` for a reference case.

For more complex checkbox layouts — multi-line labels, checkboxes inside grid
rows, or checkboxes with extra paragraph text — use raw `.form-field` or
`.checkbox-label` markup instead. See `com_tags/site/views/tags/tmpl/edit.blade.php`
and `com_forum/site/views/threads/tmpl/edit.blade.php` for those cases.

### Iframe media manager (com_blog)

com_blog's edit form uses `<x-form-section>` and `<x-form-field>` for all
standard fields, including the "Allow comments" checkbox (`type="checkbox"`).
The one remaining bespoke element is the sidebar iframe file
manager, which manages uploads, inserts, and deletions through a separate
controller — a pattern that `<x-media-upload>` does not cover.

See `com_blog/site/views/entries/tmpl/edit.blade.php` for the reference case.

### Mixed rich-editor form (com_forum)

com_forum's thread edit uses `<x-form-section>` and `<x-form-field>` for
category-level fields (sticky/closed checkboxes, access select, category select
with optgroups, title input). The comment/editor portion below uses raw
`.form-field` markup for the CKEditor with `@mentions` config, tag
autocompleter, file upload with description, and anonymous checkbox toggle.
This split is intentional: the structured fields map well to `<x-form-field>`,
but the editor and its associated widgets (autocompleter, file input pair,
checkbox) need layout flexibility that the generic component constrains.
The category edit form is simpler and uses `<x-form-section>` / `<x-form-field>`
throughout.

See `com_forum/site/views/threads/tmpl/edit.blade.php` (mixed) and
`com_forum/site/views/categories/tmpl/edit.blade.php` (pure) for the reference
cases.

### Checkout step forms (com_cart)

com_cart's checkout steps now use `<x-form-section>` and `<x-form-field>`
for standard inputs. The shipping step wraps all address fields in a single
form section, including a 2-column grid for zip/state and a checkbox via
`type="checkbox"`. The EULA step uses a form section with a scrollable EULA
text box (bespoke bordered div) plus a checkbox acceptance field. The notes
step uses `<x-form-field>` for the generic notes textarea; per-SKU notes
fields use raw `.form-field` markup because their labels contain compound
HTML (bold product name + notes text + required marker). All steps use
`<x-page-container>` for page shell and `<x-step-nav>` for the wizard
progress indicator. The shipping step also has a sidebar with saved addresses.

See `com_cart/site/views/checkout/tmpl/shipping.blade.php`,
`com_cart/site/views/checkout/tmpl/eula.blade.php`, and
`com_cart/site/views/checkout/tmpl/notes.blade.php` for reference cases.

### Media editor with AJAX uploads (com_collections)

com_collections' post editor uses `<x-page-container>` for the shell. The AJAX
file upload area, link-adder widget, and inline asset list with delete buttons
use raw markup — the mixed media interactions need layout flexibility that
`<x-form-section>` constrains. However, several simpler fields use raw
`.form-control` where `<x-form-field>` would fit cleanly: the title input
(`edit.blade.php` line 136), the collection selector/create input
(`edit.blade.php` line 182), and the select/create fields in the collect modal
(`collect.blade.php` line 21). These are candidates for future normalization
without affecting the bespoke upload/media areas.

The separate "collect" (repost) flow in `collect.blade.php` is a compact
modal-style fieldset form with no `<x-page-container>` — it renders inside a
lightbox/iframe and offers a three-column layout: select existing collection,
or create a new one, plus a description editor.

See `com_collections/site/views/posts/tmpl/edit.blade.php` (editor) and
`com_collections/site/views/posts/tmpl/collect.blade.php` (modal) for
reference cases.

### Citation editor with structured sections (com_citations)

com_citations' edit form is a comprehensive example of `<x-form-section>` and
`<x-form-field>` used throughout. The form has seven sections: Details (type
select, cite key, title, year/month/ref_type in a 3-column grid), Authors
(author address, editors), Publication Details (journal, booktitle,
volume/issue/pages, ISBN/DOI, series/edition, publisher/school,
institution/organization, address/location — all in responsive 2- and 3-column
grids), Links (URL, eprint, howpublished), Abstract (abstract, keywords, notes
textareas), Dates (submitted/accepted/published in a 3-column grid), and
conditional Tags & Badges sections.

The **Associations** section is the only bespoke area: a repeating 3-column
grid of select/input/select rows for linking citations to resources or
publications. This pattern doesn't map to `<x-form-field>` because each
"field" is actually a row of three coupled inputs with hidden ID fields.

The **Affiliation** section uses raw checkbox labels (not `type="checkbox"`)
for the affiliated/fundedby toggles.

Action buttons use `.form-actions` with Save and Cancel.

See `com_citations/site/views/citations/tmpl/edit.blade.php` for the reference
implementation.

### XML-driven form editor (com_content)

com_content's frontend editor uses native `<fieldset>` / `<legend>` with
daisyUI's `fieldset` / `fieldset-legend` classes instead of `<x-form-section>`
/ `<x-form-field>`. This is intentional: the form fields come from Hubzero's
`Form` class (`$form->getLabel()` / `$form->getInput()`), which generates its
own label and input markup from XML definitions. Wrapping Form output in
`<x-form-field>` would double-wrap labels and break the generated `for`/`id`
associations.

The form has four fieldsets: Editor (title, alias, WYSIWYG body), Images & URLs
(conditionally shown via `show_urls_images_frontend` param, 2-column grid),
Publishing (category, author alias, state, featured, dates — some fields
conditional on `access-change` permission), and Metadata (meta description,
keywords). Language selection gets its own single-field fieldset.

Action buttons use raw `flex gap-2` instead of `.form-actions` since the form
has no sidebar or complex layout.

See `com_content/site/views/form/tmpl/edit.blade.php` for the reference
implementation.

### Resource contribution steps (com_resources)

The resource contribution wizard splits the edit flow across multiple step
pages (type, compose, attach, authors, tags, review), each rendered as a
separate controller task with `<x-page-container>` and the shared
`steps.blade.php` step navigator partial (`<x-step-nav>`).

The **compose** step uses `<x-form-section>` and `<x-form-field>` for the
title input, abstract WYSIWYG editor, and a nested `<x-form-section>` for the
iframe media manager. A conditional details section renders type-specific
custom fields via `<x-form-section>`. The **attach** step uses
`<x-form-section>` for the attachments area. The **authors** step uses raw
table markup for the current author list (with role selects and reorder/remove
buttons) and a plugin-driven autocomplete for adding new authors — not
`<x-form-field>`, because the author table and drag-to-reorder interaction
don't map to the generic field component. The **tags** step uses raw markup
for tag autocomplete and focus area checkbox groups.

All steps share the same `<x-page-container>` + step-nav shell and use
Previous/Next navigation buttons with form submission.

See `com_resources/site/views/steps/tmpl/compose.blade.php` (form sections),
`com_resources/site/views/steps/tmpl/attach.blade.php` (attachments),
`com_resources/site/views/steps/tmpl/authors.blade.php` (author table), and
`com_resources/site/views/steps/tmpl/tags.blade.php` (tag/focus areas) for
reference implementations.

### Event editor with date/time controls (com_events)

com_events' edit form uses `<x-page-container>` for the shell but raw daisyUI
form controls throughout — no `<x-form-section>` or `<x-form-field>`. This is
intentional: the form has complex nested date/time controls (start/end date
inputs, time inputs with AM/PM radio buttons when `calUseStdTime` is enabled,
timezone select), helper-generated select fields for category and timezone,
WYSIWYG editor for description, dynamic custom fields (checkbox and text
types), and a tag input — a layout that doesn't map cleanly to the generic
form components.

The form renders inside a single `<form>` with a daisyUI card wrapper.
Hidden fields handle CSRF token, honeypot, state, email, restricted, and
created_by. Grid layout (`grid grid-cols-1 md:grid-cols-2`) is used for
multi-column date/time sections.

See `com_events/site/views/edit/tmpl/default.blade.php` for the reference
implementation.

### Event registration form (com_events)

The registration form is a param-driven multi-fieldset page using
`<x-page-container>` with the same period tabs and event sub-tabs as the
detail view (Overview / custom pages / Register). All fieldsets are
conditionally rendered based on `$params->get()` flags:

- **Name**: first/last (required), optional affiliation and title
- **Contact**: address (city/state/zip/country), phone, fax, email (required),
  website
- **Demographics**: position select with custom text, degree radios, gender
  radios, race checkboxes with conditional tribe field
- **Arrival/Departure**: nested fieldsets with day + time inputs
- **Disability/Dietary/Dinner**: checkbox toggles with conditional details
- **Abstract/Comments**: textareas for submission content

Uses raw daisyUI classes with responsive 2-column grid layouts. No
`<x-form-section>` or `<x-form-field>` — the heavy param-driven conditional
rendering and specialized field types (radio groups, nested fieldsets,
conditional follow-up fields) make the generic components a poor fit.

See `com_events/site/views/register/tmpl/default.blade.php` for the reference
implementation.

### Application editor with sidebar actions (com_developer)

com_developer's application editor uses `<x-form-section>` and `<x-form-field>`
for its two sections: Application Details (name, description, redirect URI) and
Team (current member list + plugin-driven multi-entry selector via
`Event::trigger('hubzero.onGetMultiEntry')`). The team autocomplete widget is
rendered as slot content inside `<x-form-field>` — when the plugin returns
HTML, it replaces the fallback text input.

When editing an existing application, the sidebar uses `<x-sidebar-card>` for
two destructive-action cards: Reset Client Secret (yellow `btn-warning` with
`data-confirm`) and Delete Application (red `btn-error` with `data-confirm`).
The sidebar is hidden on new-application forms. The `:actions` slot contains
a back link; Submit and Cancel buttons are a raw `flex gap-2` row inside the
form body.

See `com_developer/site/views/applications/tmpl/edit.blade.php` for the
reference implementation.

### Settings with owner management tables (com_wishlist)

com_wishlist has two edit forms: a settings page and a wish editor.

**Settings** (`settings.blade.php`) uses `<x-form-section>` and
`<x-form-field>` for the information section (title, description, public/private
radio buttons via `type="checkbox"`). The bulk of the form is owner management:
two or three `<x-form-section>` blocks (Owner Groups, Individuals, and
optionally Advisory Committee) each containing a `table.table-sm` listing
current owners with remove links, plus an `<x-form-field>` wrapping a
plugin-driven autocomplete (`Event::trigger('hubzero.onGetMultiEntry')`) for
adding new entries. The public/private radios are disabled when the wishlist
is a resource list or the main general list. A `<x-sidebar-card>` provides
help text. Action buttons use raw `flex gap-2` since `.form-actions` is not
needed for this layout.

**Wish editor** (`editwish.blade.php`) uses a single `<x-form-section>` with
`<x-form-field>` throughout: proposed-by input (edit only), anonymous and
private checkboxes (`type="checkbox"`), subject, WYSIWYG editor for
description, tag autocompleter via `Event::trigger`, and an optional reward
points input (when banking is enabled). A `<x-sidebar-card>` explains the
form and optionally describes the reward system. Error display uses a raw
alert div above the form since errors come from `$__view->getErrors()`.

See `com_wishlist/site/views/wishlists/tmpl/settings.blade.php` (settings) and
`com_wishlist/site/views/wishlists/tmpl/editwish.blade.php` (wish editor) for
reference implementations.

### Story submission form (com_feedback)

com_feedback's story form uses `<x-form-section>` with `<x-form-field>` for
the standard fields (full name, organization, quote textarea) but has two
bespoke elements that sit outside the generic components: a file upload field
for user photos (using `<x-form-field>` as a wrapper but with a raw `<input
type="file">` inside) and a set of raw checkbox rows for authorization options
(e.g., permission to use the story). The form posts to a single controller
action.

See `com_feedback/site/views/feedback/tmpl/story.blade.php` for the reference
implementation.

### Wiki page editor with markup preview (com_wiki)

com_wiki's edit form is the most complex editor in the codebase, combining
structured metadata with wiki-markup editing. It uses `<x-form-section>` and
`<x-form-field>` throughout, organized into three sections: Page (title, tags,
authors, access select, parent page), the editor area (wiki markup textarea
with CKEditor integration, edit summary input), and Metadata (template
select, page parameters). The page hierarchy breadcrumbs and `_authors`
partial appear above the form.

The wiki markup textarea includes help links to `Help:WikiFormatting` and
`Help:WikiMacros` pages. An iframe-based media/file manager allows uploading
and inserting images and files. The template selector dynamically shows/hides
a page parameters field based on the selected template.

The form uses `<x-form-field>` for standard inputs and `type="checkbox"` for
the access checkbox. The "authors" field is a comma-separated text input that
auto-resolves usernames. The rename form (`rename.blade.php`) is a simpler
single-section form with a title input and `<x-form-field>`.

See `com_wiki/site/views/pages/tmpl/edit.blade.php` and
`com_wiki/site/views/pages/tmpl/rename.blade.php` for the reference
implementations.

### Tool registration/edit form (com_tools)

The tool edit form is a large multi-section form using `<x-form-field>` for
structured inputs (title, description, host requirements, team members). It
mixes `<x-form-field>` with raw fieldsets for complex sections like code access
(radio matrix with conditional text inputs) and VNC geometry selectors. The
header includes action buttons (cancel, save) via `<x-slot:header_extra>` on
`<x-page-container>`. Unlike the cart checkout wizard, this is a single-page
form rather than a multi-step flow.

See `com_tools/site/views/pipeline/tmpl/edit.blade.php` for the reference
implementation.

### Curation review form (com_publications)

The curation review form is a curator-facing approval page for an individual
publication. It uses `<x-page-container>` with a "Back to list" action button
in the header (`btn btn-ghost`). The form wraps the entire page body and posts
to the curation controller's save task.

**Publication info header** — a `.pubtitle` heading showing the publication
type icon, truncated title, and version label. Below it, a `.instruct`
paragraph displays the publication thumbnail, submission/resubmission date,
submitter name, and assigned curator (if any). A color-coded legend explains
the checklist states (none, pass, fail, update).

**Curation block checklist** — the `.curation-blocks` section iterates over
the publication's curation model blocks (skipping inactive blocks and the
"review" block). Each block is rendered by `$pub->_curationModel->parseBlock('curator')`,
which outputs the block's checklist items with pass/fail/update toggles. The
curation model controls the markup for each block.

**Action buttons** — a `.submit-curation` section with two submit buttons:
"Looks Good" (`btn btn-success`) to approve and "Looks Bad"
(`btn btn-primary` with kickback class) to reject. Both use the
`btn-curate` class for JavaScript binding.

**Submitter comment** — when a curation history record exists with a comment,
a `.submitter-comment` block renders the submitter's message with an `<h5>`
heading and the comment HTML.

**Notice dialog** — a hidden `#addnotice` div (revealed via fancybox) contains
a secondary form for marking individual checklist items as failed. The dialog
has a `<fieldset>` with `<legend>`, a notice item placeholder, a `<textarea>`
for the review comment, and a "Mark as fail" submit button
(`btn btn-primary`). Hidden fields carry the publication ID, version ID, block
properties, and pass=0.

See `com_publications/site/views/curation/tmpl/view.blade.php` for the
reference implementation.

### Ticket submission form with guest/auth modes (com_support)

The new ticket form has two distinct rendering modes based on authentication
state. Guest users see user information fields (username, name, email) plus a
CAPTCHA section; authenticated users skip those and get auto-populated fields.
Both modes share the problem description textarea, file attachment uploads, and
optional admin-only detail sections (severity, category, group, owner, tags).
Uses `<x-form-field>` for structured inputs within `<fieldset>` groups. The
form includes an info alert (`COM_SUPPORT_TROUBLE_TICKET_TIMES`) rendered with
`{!! !!}` since the lang key contains HTML.

See `com_support/site/views/tickets/tmpl/new.blade.php` for the reference
implementation.

### Job posting editor with date/company fields (com_jobs)

The job editor uses four `<x-form-section>` blocks: Job Overview (title,
location, country, company, website), Job Description (WYSIWYG editor via
`{!! $editor->display(...) !!}`), Job Specifics (category, type, start date,
close date, expire date, external URL, internal application toggle), and
Contact Info (optional name, email, phone). The specifics section uses a
2-column grid for date fields. All inputs use `<x-form-field>`. The form
pre-populates employer defaults on create, existing values on edit. Form
actions include Save (Preview) and Cancel buttons.

The apply form (`apply/tmpl/default.blade.php`) is a simpler single-section
form with a cover letter textarea inside `<x-form-section>`, plus a job info
header card showing the position being applied for.

See `com_jobs/site/views/editjob/tmpl/default.blade.php` for the reference
implementation.

### Multi-section group editor (com_groups)

The group editor (`edit.blade.php`) uses `<x-page-container>` with
`<x-form-section>` and `<x-form-field>` throughout, organized into six
collapsible sections:

1. **Group Details** — group ID (text, required, with help text), title
   (required), tags (via `Event::trigger('hubzero.onGetMultiEntry')`), public
   description (WYSIWYG via `$__view->editor()`), private description (WYSIWYG)
2. **Membership Settings** — join policy radios (Open/Restricted/Invite
   Only/Closed) with `radio radio-sm`, conditional "Credentials" textarea for
   the restricted option
3. **Privacy Settings** — discoverability radios (Visible/Hidden), per-plugin
   access permission selects (Any Visitor/Registered/Members Only/Disabled)
4. **Group Email Settings** — auto-subscribe checkbox (`checkbox checkbox-sm`)
5. **Page Settings** — comments and author details selects
   (`select select-bordered`)
6. **Logo** — on edit, the sidebar shows a media browser iframe for managing
   group files and logo selection

The form uses a single "Save Group" submit button. On create (`task=new`), the
sidebar shows a note that file uploads are available after creation. On edit,
the sidebar embeds the media browser iframe.

See `com_groups/site/views/groups/tmpl/edit.blade.php` for the reference
implementation.

### Course edit and simple action forms (com_courses)

com_courses has four simple form views that all use `<x-page-container>` with
`<x-form-section>` and `<x-form-field>`:

**Course edit** (`edit.blade.php`) — single form section with alias, title,
blurb (textarea), tags (tag autocompleter), and "Allow forks" checkbox.

**Copy / Fork** (`copy.blade.php`, `fork.blade.php`) — nearly identical
two-field forms (alias + title) with hidden fields for the source course and
task. These are minimal "name the clone" forms.

**New offering** (`newoffering.blade.php`) — alias + title fields for creating
a new offering under a course. Supports `no_html` mode for rendering inside a
modal/iframe.

All four forms follow the standard edit pattern with no bespoke elements.

See `com_courses/site/views/course/tmpl/edit.blade.php`,
`com_courses/site/views/course/tmpl/copy.blade.php`,
`com_courses/site/views/course/tmpl/fork.blade.php`, and
`com_courses/site/views/course/tmpl/newoffering.blade.php` for the reference
implementations.

### Restricted enrollment form (com_courses)

The restricted enrollment page (`enroll_restricted.blade.php`) is a coupon-code
gate: users enter a code to unlock enrollment in a restricted offering. It uses
`<x-page-container>` with `<x-form-section>` and `<x-form-field>` for the
coupon code input, plus an explanation sidebar. This is a single-field form
that conditionally appears only for offerings with restricted enrollment.

See `com_courses/site/views/offering/tmpl/enroll_restricted.blade.php` for the
reference implementation.

## Accessibility Notes

- Every input has a corresponding `<label>` with matching `for`/`id`
- Required fields use both `required` attribute and visible `*` marker
- Error messages use `role="alert"` so screen readers announce them immediately
- Error/hint text linked to input via `aria-describedby`
- Invalid fields get `aria-invalid="true"` + `.input-error` (daisyUI red border)
- Validation errors are `hidden` by default, shown with JS on invalid input
- Checkbox/radio labels use `.checkbox-label` for interactive bordered rows
- Use `autocomplete` attributes on personal data fields (WCAG 1.3.5)
- See [Accessibility Guide](../guides/accessibility.md) for full WCAG 2.2 AA requirements

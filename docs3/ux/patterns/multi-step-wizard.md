# Multi-step Wizard

Guided flow that breaks a complex process into sequential steps with a progress
indicator. Used for project setup, resource submission, user registration, and
any multi-stage workflow.

## When to Use

- Project creation (describe, team, files, publish)
- Resource/publication submission
- Citation import (upload, review, saved)
- Cart checkout (items, shipping, payment, confirm)
- Complex registration flows
- Any process where users need guidance through ordered steps

## Real-World Examples

### Citation Import (com_citations)

Three-step import: Upload → Review → Saved. Each step is a separate controller
task (`displayTask`, `reviewTask`, `savedTask`), so the step indicator is
rendered fresh on each page load (not client-side navigation).

```blade
{{-- Step indicator at top of each import view --}}
<x-step-nav
    :steps="[
        Lang::txt('COM_CITATIONS_IMPORT_STEP1'),
        Lang::txt('COM_CITATIONS_IMPORT_STEP2'),
        Lang::txt('COM_CITATIONS_IMPORT_STEP3'),
    ]"
    :current="0"
/>
```

The review step uses Alpine.js for expandable citation details (see
[Alpine.js](#alpinejs-for-interactive-details) below).

### Cart Checkout (com_cart)

Dynamic multi-step checkout where the steps depend on what's in the cart.
Possible steps: Agreement (EULA, per-SKU, collapsed to one visual step) → Notes
→ Shipping (if physical product) → Review. Each step is a separate controller
task; the controller calls `$cart->getAllCheckoutSteps()` to get the full step
list with completion status, then passes `$checkoutSteps` (array of label maps)
and `$currentStepIndex` (int) to the view. Each checkout template renders
`<x-step-nav>` at the top of the page, guarded by `@if(!empty($checkoutSteps))`
so legacy PHP views are unaffected.

Payment and confirmation happen after the wizard completes (post-Review) and do
not show the step indicator.

```blade
{{-- At top of each checkout step view (eula, notes, shipping, summary) --}}
@if(!empty($checkoutSteps))
  <x-step-nav :steps="$checkoutSteps" :current="$currentStepIndex" />
@endif
```

See `com_cart/site/views/checkout/tmpl/shipping.blade.php` for a reference
checkout step with sidebar and step indicator.

## daisyUI Components Used

- [Steps](https://daisyui.com/components/steps/) — progress indicator
- [Fieldset](https://daisyui.com/components/fieldset/) — form sections
- [Input](https://daisyui.com/components/input/) — text fields
- [Select](https://daisyui.com/components/select/) — dropdowns
- [Table](https://daisyui.com/components/table/) — team member list
- [Avatar](https://daisyui.com/components/avatar/) — member photos
- [Button](https://daisyui.com/components/button/) — navigation actions
- [Card](https://daisyui.com/components/card/) — sidebar help panels

## Global Component

Use `<x-step-nav>` ([docs](../reference/global-components.md#x-step-nav)) for the step
indicator. It renders a daisyUI `.steps .steps-horizontal` list with
`step-primary` for completed and current steps, and `aria-current="step"` on
the active step. Steps can be plain strings or arrays with `label` and optional
`url` (completed steps with a URL become clickable links):

```blade
<x-step-nav
    :steps="[
        ['label' => 'Describe', 'url' => '?step=0'],
        ['label' => 'Team'],
        'Files',
        'Finalize',
    ]"
    :current="1"
/>
```

## Full Markup Example

```html
<section class="py-8">
  <div class="max-w-7xl mx-auto px-4">

    <!-- Step indicator -->
    <ul class="steps steps-horizontal w-full mb-8">
      <li class="step step-primary">
        <a href="...">Describe</a>
      </li>
      <li class="step step-primary" aria-current="step">
        Team
      </li>
      <li class="step">Files</li>
      <li class="step">Finalize</li>
    </ul>

    <!-- Step content -->
    <div class="lg:grid lg:grid-cols-[1fr_280px] lg:gap-8">
      <div class="min-w-0">

        <form id="hubForm" method="post" action="..." novalidate>

          <header class="mb-6">
            <h2 class="text-xl font-bold mb-1">Step 2: Add Team Members</h2>
            <p class="text-sm text-base-content/60">
              Invite collaborators to your project. You can change the team
              later from the project settings.
            </p>
          </header>

          <!-- Search members -->
          <fieldset class="fieldset bg-base-100 border border-base-300 p-6 rounded-box mb-6">
            <legend class="fieldset-legend">Search Members</legend>

            <div class="form-control">
              <label class="label" for="field-member">
                <span class="label-text">Find a member</span>
              </label>
              <input type="search" id="field-member" name="member"
                     class="input input-bordered w-full"
                     autocomplete="off"
                     aria-describedby="field-member-hint"
                     aria-controls="member-results" />
              <label class="label" id="field-member-hint">
                <span class="label-text-alt">Type a name or username to search.</span>
              </label>
              <ul id="member-results" role="listbox" hidden>
                <!-- JS-populated suggestions -->
              </ul>
            </div>
          </fieldset>

          <!-- Current team -->
          <fieldset class="fieldset bg-base-100 border border-base-300 p-6 rounded-box mb-6">
            <legend class="fieldset-legend">Current Team</legend>

            <div class="overflow-x-auto">
              <table class="table">
                <thead>
                  <tr>
                    <th>Member</th>
                    <th>Role</th>
                    <th><span class="sr-only">Actions</span></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div class="flex items-center gap-3">
                        <div class="avatar">
                          <div class="w-8 rounded-full">
                            <img src="..." alt="" />
                          </div>
                        </div>
                        John Doe
                      </div>
                    </td>
                    <td>
                      <select class="select select-bordered select-sm"
                              name="team[1][role]"
                              aria-label="Role for John Doe">
                        <option value="manager" selected>Manager</option>
                        <option value="collaborator">Collaborator</option>
                        <option value="viewer">Viewer</option>
                      </select>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-error btn-outline"
                              type="button"
                              aria-label="Remove John Doe">Remove</button>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="flex items-center gap-3">
                        <div class="avatar">
                          <div class="w-8 rounded-full">
                            <img src="..." alt="" />
                          </div>
                        </div>
                        Jane Smith
                      </div>
                    </td>
                    <td>
                      <select class="select select-bordered select-sm"
                              name="team[2][role]"
                              aria-label="Role for Jane Smith">
                        <option value="manager">Manager</option>
                        <option value="collaborator" selected>Collaborator</option>
                        <option value="viewer">Viewer</option>
                      </select>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-error btn-outline"
                              type="button"
                              aria-label="Remove Jane Smith">Remove</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </fieldset>

          <!-- Step navigation -->
          <div class="flex items-center justify-between pt-6 border-t border-base-300">
            <a class="btn btn-ghost" href="...">&larr; Previous</a>
            <div class="flex gap-3">
              <button class="btn btn-ghost" type="submit"
                      name="action" value="save">Save &amp; Close</button>
              <button class="btn btn-primary" type="submit"
                      name="action" value="next">Continue &rarr;</button>
            </div>
          </div>

          <input type="hidden" name="step" value="2" />
          <input type="hidden" name="id" value="42" />
          <!-- CSRF token -->
        </form>

      </div>

      <aside class="space-y-6">
        <div class="card bg-base-100 shadow-sm">
          <div class="card-body">
            <h4 class="card-title text-sm">About This Step</h4>
            <p class="text-sm mb-3">Team members will be able to access project
            files and contribute based on their assigned role.</p>
            <dl class="text-sm">
              <dt class="font-medium mt-2">Manager</dt>
              <dd class="text-base-content/60">Full control, can manage team and settings.</dd>
              <dt class="font-medium mt-2">Collaborator</dt>
              <dd class="text-base-content/60">Can upload files and edit content.</dd>
              <dt class="font-medium mt-2">Viewer</dt>
              <dd class="text-base-content/60">Read-only access to project materials.</dd>
            </dl>
          </div>
        </div>
      </aside>
    </div>

  </div>
</section>
```

## Step Indicator

daisyUI [Steps](https://daisyui.com/components/steps/):

```html
<ul class="steps steps-horizontal w-full">
  <li class="step step-primary">Describe</li>
  <li class="step step-primary" aria-current="step">Team</li>
  <li class="step">Files</li>
  <li class="step">Finalize</li>
</ul>
```

- Completed steps get `step-primary` (filled) and can be `<a>` links
- Current step gets `step-primary` + `aria-current="step"`
- Future steps have no color modifier (grayed out)

## Step Navigation

Previous on the left, save + next on the right:

```html
<div class="flex items-center justify-between pt-6 border-t border-base-300">
  <a class="btn btn-ghost" href="...">&larr; Previous</a>
  <div class="flex gap-3">
    <button class="btn btn-ghost" type="submit"
            name="action" value="save">Save &amp; Close</button>
    <button class="btn btn-primary" type="submit"
            name="action" value="next">Continue &rarr;</button>
  </div>
</div>
```

First step omits "Previous". Last step changes "Continue" to "Finish":

```html
<!-- First step -->
<div class="flex items-center justify-end pt-6 border-t border-base-300">
  <button class="btn btn-primary" type="submit"
          name="action" value="next">Continue &rarr;</button>
</div>

<!-- Last step -->
<div class="flex items-center justify-between pt-6 border-t border-base-300">
  <a class="btn btn-ghost" href="...">&larr; Previous</a>
  <button class="btn btn-primary" type="submit"
          name="action" value="finish">Finish Setup</button>
</div>
```

## Structure Reference

| Element | daisyUI Class | Purpose |
|---------|---------------|---------|
| Step indicator | `.steps .steps-horizontal` | Progress bar |
| Single step | `.step` | One step |
| Completed step | `.step .step-primary` | Done step (colored) |
| Current step | `.step .step-primary` + `aria-current` | Active step |
| Form sections | `.fieldset .fieldset-legend` | Grouped fields |
| Team table | `.table` | Member list |
| Member avatar | `.avatar` | Profile photo |
| Role select | `.select .select-bordered .select-sm` | Role dropdown |
| Remove button | `.btn .btn-sm .btn-error .btn-outline` | Remove member |
| Previous link | `.btn .btn-ghost` | Go back |
| Continue button | `.btn .btn-primary` | Go forward |
| Sidebar help | `.card .card-body` | Contextual tips |

### Resource Contribution Wizard (com_resources)

The resource contribution wizard is a 6-step flow: Type Selection → Compose →
Attach → Authors → Tags → Review. Each step is a separate controller task
(`draftTask` with a `step` parameter), and the step indicator is rendered by
the shared `steps.blade.php` partial which uses `<x-step-nav>` (line 172).

The step-nav data is built dynamically: completed steps and previously visited
steps get clickable URLs; the current step has no URL; future steps on
unsubmitted resources are not linked. A "Start" step is prepended that links
either to the resource detail (if submitted) or to the new-resource page.

Above the step indicator, a summary `table table-sm` shows the resource's
current state: type, title, attachment/author/tag counts, status badge
(draft/pending/published/unpublished), and a discard button for unsubmitted
resources.

The **type** step uses `<x-card-grid cols="3">` for resource type selection
cards with a `<x-sidebar-card>` for help text. The **compose** step uses
`<x-form-section>` and `<x-form-field>` for title, abstract, and custom
fields. The **attach** step uses `<x-form-section>` for the file attachment
area. The **authors** step uses a raw table for the author list with role
selects and reorder controls. The **tags** step uses raw markup for tag
autocomplete and focus area checkbox groups. The **review** step shows a
read-only summary with a submit button.

See `com_resources/site/views/steps/tmpl/steps.blade.php` (step nav partial),
`com_resources/site/views/steps/tmpl/type.blade.php` (type selection),
`com_resources/site/views/steps/tmpl/compose.blade.php` (compose form),
`com_resources/site/views/steps/tmpl/attach.blade.php` (attachments),
`com_resources/site/views/steps/tmpl/authors.blade.php` (authors),
`com_resources/site/views/steps/tmpl/tags.blade.php` (tags), and
`com_resources/site/views/steps/tmpl/review.blade.php` (review) for the
reference implementation.

### Tool Pipeline Status (com_tools)

The tool status page uses `<x-step-nav>` to show the tool's lifecycle progress
through states: Registered → Created → Uploaded → Installed → Approved →
Published. The step list is dynamic — a "Retired" step appends for retired
tools, and "Updated" replaces "Uploaded" when a tool has been updated. The
current state determines the active step index, and all prior steps show as
completed (`step-primary`).

Unlike the cart checkout wizard, this is **not a user-driven flow** — the steps
reflect backend status changes rather than user navigation. The step-nav is
read-only (no clickable links) and purely informational.

See `com_tools/site/views/pipeline/tmpl/status.blade.php` for the reference
implementation.

## Accessibility Notes

- daisyUI `.steps` uses `<ul>` (inherently ordered as a list)
- `aria-current="step"` marks the active step for screen readers
- Completed steps are `<a>` links (navigable); future steps are plain text
- Autocomplete results use `role="listbox"` with `aria-controls`
- Each role select has `aria-label` identifying which team member it controls
- Remove buttons have `aria-label` naming the affected member
- Table uses `<th scope="col">` for column headers; action column header is
  `<span class="sr-only">Actions</span>`
- Navigation buttons use visible text arrows (`←` / `→`) plus descriptive labels
- Step indicator communicates progress without relying on color alone — text
  labels are always visible alongside the colored circles
- See [Accessibility Guide](../guides/accessibility.md) for full WCAG 2.2 AA requirements

---

## Alpine.js for Interactive Details

Wizard review steps often need expandable/collapsible detail sections — e.g.,
showing the full metadata of each imported citation. Use
[Alpine.js](https://alpinejs.dev/) for lightweight, declarative toggling:

```blade
@foreach($citations as $citation)
    <div class="card bg-base-100 shadow-sm" x-data="{ open: false }">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <h3 class="card-title text-base">{{ $citation->title }}</h3>
                <button class="btn btn-ghost btn-xs" type="button"
                        @click="open = !open"
                        :aria-expanded="open.toString()">
                    <span x-show="!open">Show Details</span>
                    <span x-show="open" x-cloak>Hide Details</span>
                </button>
            </div>

            <div x-show="open" x-cloak class="mt-4">
                {{-- Detail table or comparison grid --}}
            </div>
        </div>
    </div>
@endforeach
```

### Key patterns

- **`x-data="{ open: false }"`** — scoped state per card, no global JS
- **`x-show`** — toggles visibility (uses `display: none`, no DOM removal)
- **`x-cloak`** — hides content until Alpine initializes (prevents flash).
  Requires a CSS rule: `[x-cloak] { display: none !important; }`
- **`:aria-expanded`** — dynamic ARIA attribute for screen readers
- **No `id` needed** — each `x-data` scope is independent, so the pattern
  works inside `@foreach` loops without unique IDs

### When to use Alpine.js vs plain JS

| Use Alpine.js | Use plain JS (site.js / admin.js) |
|---------------|-----------------------------------|
| Show/hide toggling scoped to one element | Page-wide behaviors (modals, keepalive) |
| Simple conditional rendering in loops | Event delegation across many elements |
| Form state that doesn't leave the page | AJAX/fetch calls |
| Expand/collapse in review/comparison views | Complex state machines |

Views that use Alpine.js should load it via `@push('scripts')`:

```blade
@push('scripts')
    <script src="/core/assets/js/alpine.min.js" defer></script>
@endpush
```

The `[x-cloak]` rule should be in `site.src.css`. Admin views use `admin.js`
delegation instead — prefer data-attribute delegation over Alpine in admin.

# WCAG AA 2.1 — Common Issues in Admin Blade Views

Patterns found during axe-core audits of admin Blade templates.
Use this as a checklist when writing or reviewing views that can't be
easily tested in the browser.

---

## 1. Row Checkboxes Need `aria-label`

Row-selection checkboxes in admin list tables have no visible label text.
Without an `aria-label` they fail the **label** rule (critical).

**Bad:**
```blade
<input type="checkbox"
       name="id[]"
       id="cb{{ $i }}"
       value="{{ $row->get('id') }}"
       class="checkbox checkbox-sm"
       data-check-item />
```

**Good:**
```blade
<input type="checkbox"
       name="id[]"
       id="cb{{ $i }}"
       value="{{ $row->get('id') }}"
       class="checkbox checkbox-sm"
       aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
       data-check-item />
```

The "Check All" checkbox in `<thead>` already has this via the
`data-check-all` convention — just make sure `aria-label` is present:

```blade
<input type="checkbox"
       class="checkbox checkbox-sm"
       data-check-all
       aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
```

---

## 2. Radio Buttons Need Labels

Standalone radio buttons (e.g., poll preview) must have either a
wrapping `<label>` or explicit `for`/`id` pairing.

**Good:**
```blade
<input type="radio"
       name="poll"
       id="poll-option-{{ $option->get('id') }}"
       value="{{ $option->get('text') }}"
       class="radio radio-sm radio-primary"
       aria-label="{{ $option->get('text') }}" />
...
<label for="poll-option-{{ $option->get('id') }}">{{ $option->get('text') }}</label>
```

When the label is in a different table cell, use `for`/`id` pairing
plus `aria-label` as a belt-and-suspenders approach.

---

## 3. Hint/Helper Text Contrast — Use `/70`, Not `/60`

Tailwind opacity modifiers on `text-base-content` produce computed
colors against a white background:

| Class                   | Computed color | Contrast ratio | Passes AA? |
|-------------------------|---------------|----------------|------------|
| `text-base-content/40`  | ~`#a5abb5`    | ~2.4:1         | No         |
| `text-base-content/50`  | ~`#8f95a1`    | ~3.2:1         | No         |
| `text-base-content/60`  | ~`#7a8290`    | ~4.0:1         | No         |
| `text-base-content/65`  | ~`#636c7a`    | ~4.3:1         | No         |
| `text-base-content/70`  | ~`#5b6472`    | ~5.2:1         | Yes        |

**Rule:** Always use `text-base-content/70` (minimum) for any text
that must be readable. This applies to:

- Form field hints: `<p class="text-xs text-base-content/70 mt-1">`
- Footer text
- Sidebar metadata
- Pagination result counts
- Any "muted" informational text

---

## 4. Primary Color on Light Backgrounds

The theme primary `#0a7b72` against sidebar background `#e2e8f0`
gives only 4.16:1 (below 4.5:1). Use the custom utility class
`.text-primary-dark` (`#076b63`) for primary-colored text on the
sidebar or other light non-white backgrounds.

Against pure white (`#ffffff`), `#0a7b72` gives 4.68:1 — passes,
but barely. Prefer `text-primary-dark` when in doubt.

---

## 5. Toolbar Icon-Only Buttons

The admin toolbar renders buttons with an SVG icon and a visually
hidden `<span>` label. The `<a>` tag itself needs an `aria-label`
so screen readers can announce it.

This is handled globally in `mod_toolbar/tmpl/default.blade.php`.
If you add custom toolbar buttons, ensure they include:

```php
'<a ... aria-label="' . e($text) . '">'
```

---

## 6. `<select>` Elements in Shared Components

The pagination `<select>` for per-page limit had no label. Fixed
globally in `paginator.blade.php` with:

```php
'aria-label="' . Lang::txt('JGLOBAL_DISPLAY_NUM') . '"'
```

Filter `<select>` elements should use a visible `<label>` with `for`
matching the select's `id`, or an inline label pattern:

```blade
<label for="filter-state" class="text-sm">{{ Lang::txt('State') }}:</label>
<select name="state" id="filter-state" class="select select-bordered select-sm">
```

---

## 7. `text-success` Contrast Failure

The daisyUI `text-success` color (`#16a34a`) only gives 3.3:1 against
white — well below 4.5:1. Use `.text-success-dark` (`#15803d`,
Tailwind green-700, 5.0:1) for success-colored text on white/light
backgrounds.

| Class              | Color     | Contrast vs white | Passes AA? |
|--------------------|-----------|-------------------|------------|
| `text-success`     | `#16a34a` | 3.3:1             | No         |
| `text-success-dark`| `#15803d` | 5.0:1             | Yes        |
| `text-error`       | `#dc2626` | 4.8:1             | Yes        |

`text-error` passes at 4.8:1 and does not need a dark variant.

### `text-accent` Contrast Failure

The daisyUI `text-accent` color (`#f59e0b`, amber-500) only gives 2.0:1
against white — fails even the 3:1 large-text threshold. Use
`.text-accent-dark` (`#b45309`, amber-700, 4.7:1) for accent-colored
text on white/light backgrounds.

| Class              | Color     | Contrast vs white | Passes AA? |
|--------------------|-----------|-------------------|------------|
| `text-accent`      | `#f59e0b` | 2.0:1             | No         |
| `text-accent-dark` | `#b45309` | 4.7:1             | Yes        |

---

## 8. Parameter Renderer `label[for]`

`Hubzero\Html\Parameter::renderBlade()` generates `<label>` and
`<select>` pairs for component params (from XML). The `for` attribute
is now extracted from the rendered element's `id` automatically —
no action needed in Blade views. If you render params manually, ensure
`<label for="...">` matches the select/input `id`.

---

## 9. Blade Variable Syntax — No `$this->` in Blade

Legacy PHP view templates use `$this->varName`. Blade templates
receive variables directly as `$varName`. Using `$this->` in a Blade
template causes a fatal error ("Using $this when not in object
context").

**Bad:** `$this->poll->get('title')`
**Good:** `$poll->get('title')`

---

## 10. `<select>` in Table Rows — Use `aria-label`

When a `<select>` and its `<label>` are in different `<td>` cells of the
same table row, axe-core cannot associate them. The `<label for="...">` in
a `<th>` targets the text input in the next cell, but a *second* form
element (the select dropdown) in a third cell has no label association.

**Fix:** Add `aria-label` to the select, reusing the same language key
as the `<label>`:

```blade
<tr>
  <th><label for="field-minute-c">{{ Lang::txt('COM_CRON_FIELD_MINUTE') }}</label></th>
  <td><input ... id="field-minute-c" /></td>
  <td>
    <select ... id="field-minute-s"
            aria-label="{{ Lang::txt('COM_CRON_FIELD_MINUTE') }}">
    </select>
  </td>
</tr>
```

This pattern appears in com_cron's custom recurrence table (5 selects)
and anywhere a table row has paired text-input + select controls.

---

## 11. `<iframe>` Elements Need a `title`

Embedded `<iframe>` elements (e.g., sub-lists loaded inside edit forms)
must have a `title` attribute so screen readers can announce their purpose.
Without it, axe-core flags a **frame-title** violation (serious).

**Bad:**
```blade
<iframe height="500"
        name="grouper"
        id="grouper"
        class="w-full border border-base-300 rounded-box"
        src="{{ $iframeSrc }}"></iframe>
```

**Good:**
```blade
<iframe height="500"
        name="grouper"
        id="grouper"
        title="{{ Lang::txt('COM_COLLECTIONS_POSTS') }}"
        class="w-full border border-base-300 rounded-box"
        src="{{ $iframeSrc }}"></iframe>
```

This pattern appears in com_collections items/edit (posts iframe) and
anywhere a view embeds a sub-view via `<iframe>`.

---

## 12. Labels Inside Bare `<div>` Wrappers — Use `admin-field`

The daisyUI `.label` class applies `opacity: 0.6` to text color by
default, producing a contrast ratio of ~4.0:1 (fails 4.5:1). The
`.admin-field` CSS class overrides this to full opacity.

If a `<label class="label">` sits inside a bare `<div>`, the label text
will fail contrast. Always use `<div class="admin-field">` as the wrapper.

**Bad:**
```blade
<div>
  <label class="label" for="field-status">
    {{ Lang::txt('COM_WISHLIST_STATUS') }}
  </label>
  <select ...>
</div>
```

**Good:**
```blade
<div class="admin-field">
  <label class="label" for="field-status">
    {{ Lang::txt('COM_WISHLIST_STATUS') }}
  </label>
  <select ...>
</div>
```

This pattern is common in sidebar fieldsets where labels wrap selects,
text inputs, or other form controls.

---

## 13. CSS `color-mix()` Opacity Thresholds

Several admin CSS classes in `admin.src.css` use `color-mix(in oklch, var(--color-base-content) N%, transparent)` for muted text. The same contrast thresholds apply as Tailwind `/N` modifiers:

| Opacity | Approx ratio vs white | Passes AA? |
|---------|----------------------|------------|
| 45%     | ~2.8:1               | No         |
| 50%     | ~3.0:1               | No         |
| 55%     | ~3.5:1               | No         |
| 60%     | ~4.0:1               | No         |
| 70%     | ~5.2:1               | Yes        |

Classes that were fixed from failing thresholds:
- `.config-tab` (inactive tab text): 55% → 70%
- `.config-tab-desc` (tab description): 55% → 70%
- `.config-perm-notes` (permissions notes): 45% → 70%
- `.batch-summary` (batch operation summary): 60% → 70%
- `.order-num`, `.order-group-label`: 50% → 70%

When adding new CSS classes with muted text, always use **70% minimum**.

---

## 14. Ordering Inputs in List Tables

Small `<input type="text">` fields for row ordering in admin list tables
have no visible label. Without an `aria-label`, axe-core flags them as
a **label** violation (critical).

**Bad:**
```blade
<input type="text"
       name="order[]"
       size="5"
       value="{{ $item->ordering }}"
       class="input input-bordered input-xs w-14 text-center" />
```

**Good:**
```blade
<input type="text"
       name="order[]"
       size="5"
       value="{{ $item->ordering }}"
       aria-label="{{ Lang::txt('JGRID_HEADING_ORDERING') }}"
       class="input input-bordered input-xs w-14 text-center" />
```

---

## 15. Form Field `for` Attribute in Config Views

The component options view (`com_config/component/default.blade.php`)
renders HubZero Form fields inside its own `<label>` wrapper. When using
`strip_tags($field->label)` the original `<label for="...">` is removed,
breaking the label-input association.

**Fix:** Add `for="{{ $field->id }}"` to the wrapper label:

```blade
<label class="config-field-label" for="{{ $field->id }}">
  {!! strip_tags($field->label, '<span>') !!}
</label>
```

The `$field->id` property is available via `__get()` on all
`Hubzero\Form\Field` subclasses.

---

## 16. Status Toggle Links Need `aria-label`

Icon-only links used for toggling published/unpublished/trashed state in
list tables have no text content — only an SVG icon. Without an
`aria-label`, axe-core flags a **link-name** violation (serious).

These links use `data-list-item-task` and typically have a `data-title`
attribute, but `data-title` is not an accessible name.

**Bad:**
```blade
<a href="#" data-list-item-task data-cb="cb{{ $i }}"
   data-task="{{ $stateTask }}" data-title="{{ $stateAction }}"
   class="status-icon {{ $btnCls }}">
  <svg ...>{!! $stateIcon !!}</svg>
</a>
```

**Good:**
```blade
<a href="#" data-list-item-task data-cb="cb{{ $i }}"
   data-task="{{ $stateTask }}" data-title="{{ $stateAction }}"
   aria-label="{{ $stateAction }}"
   class="status-icon {{ $btnCls }}">
  <svg ...>{!! $stateIcon !!}</svg>
</a>
```

Similarly, "set default" star links need `aria-label`:

```blade
<a href="#" data-list-item-task data-cb="cb{{ $i }}" data-task="items.setDefault"
   title="{{ Lang::txt('JLIB_HTML_SETDEFAULT_ITEM') }}"
   aria-label="{{ Lang::txt('JLIB_HTML_SETDEFAULT_ITEM') }}"
   class="...">
```

---

## 17. Modal Picker Display Inputs

Disabled text inputs used as display fields in modal pickers (e.g., the
article selector in `com_content/models/fields/modal/article.php`) have
no label association. The form field's `<label>` targets the hidden
`<input>` with the actual value, not the visible disabled display input.

**Fix:** Add `aria-label` to the display input:

```php
$html[] = '<input type="text" id="' . $this->id . '_name"'
    . ' value="' . $title . '" disabled="disabled" size="35"'
    . ' aria-label="' . Lang::txt('COM_CONTENT_SELECT_AN_ARTICLE') . '" />';
```

This pattern applies to any modal picker field that has a separate
display input and hidden value input.

---

## 18. Radio/Checkbox Group Headings — Use `<fieldset>/<legend>`

When a group of radio buttons or checkboxes has a visual heading
(e.g., "Show relationships"), do NOT use `<span class="label">` — the
daisyUI `.label` class applies muted color (~4.04:1) which fails contrast
outside of `.admin-field` wrappers.

**Bad:**
```blade
<div class="admin-field">
  <span class="label">{{ Lang::txt('SHOW_RELATIONSHIPS') }}</span>
  <div class="flex gap-4 mt-1">
    <label class="label cursor-pointer justify-start gap-2">
      <input type="radio" ... />
      <span>Option A</span>
    </label>
    ...
  </div>
</div>
```

**Good:**
```blade
<fieldset class="admin-field">
  <legend class="label text-base-content">{{ Lang::txt('SHOW_RELATIONSHIPS') }}</legend>
  <div class="flex gap-4 mt-1">
    <label class="label cursor-pointer justify-start gap-2">
      <input type="radio" ... />
      <span>Option A</span>
    </label>
    ...
  </div>
</fieldset>
```

This fixes two issues at once:
1. **Contrast:** `text-base-content` overrides the muted `.label` color
2. **Semantics:** `<fieldset>/<legend>` properly groups the controls for
   screen readers, which is the correct ARIA pattern for radio groups

---

## 19. Search Inputs and Filter Selects Need Labels

Search `<input>` elements that rely solely on `placeholder` text for
identification fail the **label** rule. Add a visually hidden label:

```blade
<label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
<input type="text"
       name="search"
       id="filter_search"
       placeholder="{{ Lang::txt('SEARCH_PLACEHOLDER') }}" />
```

Similarly, filter `<select>` elements in the search slot that lack a
visible `<label>` need a `sr-only` label:

```blade
<label for="filter-tbl" class="sr-only">{{ Lang::txt('FILTER_TYPE') }}</label>
<select name="tbl" id="filter-tbl" class="select select-bordered select-sm">
```

The `sr-only` class (Tailwind utility) visually hides the label while
keeping it accessible to screen readers.

---

## 20. `$form->getLabel()` for/id Mismatch

When using `$form->getLabel('fieldname')`, the generated `<label>` has
`for="fieldname"`. If the input uses `id="field-fieldname"`, the label
association breaks and the **label** rule fires (critical).

**Bad:**
```blade
{!! $form->getLabel('url') !!}
<input type="text" name="fields[url]" id="field-url" />
{{-- Label has for="url", input has id="field-url" — no match! --}}
```

**Good:**
```blade
{!! $form->getLabel('url') !!}
<input type="text" name="fields[url]" id="url" />
{{-- Label for="url" matches input id="url" --}}
```

Alternatively, if you can't change the input ID, use a manual label
instead of `$form->getLabel()`.

---

## 21. Row-Level `opacity-*` Reduces All Child Contrast

Applying `opacity-60` (or similar) to an entire `<tr>` reduces the
contrast of every child element, causing mass **color-contrast** failures.

**Bad:**
```blade
<tr class="{{ $isProtected ? 'opacity-60' : '' }}">
  <td>{{ $item->get('name') }}</td>  {{-- ALL cells fail contrast --}}
```

**Good:**
```blade
@php $muted = $isProtected ? ' text-base-content/70' : ''; @endphp
<tr>
  <td><span class="{{ $muted }}">{{ $item->get('name') }}</span></td>
  <td class="text-sm{{ $muted }}">{{ $item->get('type') }}</td>
```

Apply `text-base-content/70` to individual cells/spans that need dimming,
not `opacity-*` to the entire row.

---

## Quick Grep Checklist

Run these to find likely violations across all admin Blade views:

```bash
# Missing aria-label on row checkboxes
grep -rn 'data-check-item' core/components/*/admin/views/**/tmpl/*.blade.php | grep -v 'aria-label'

# Low-contrast hint text (/40 through /65 all fail)
grep -rn 'text-base-content/[3456][05]' core/components/*/admin/views/**/tmpl/*.blade.php

# text-success (3.3:1) — should be text-success-dark (5.0:1)
grep -rn 'text-success[^-]' core/components/*/admin/views/**/tmpl/*.blade.php

# text-accent (2.0:1) — should be text-accent-dark (4.7:1)
grep -rn 'text-accent[^-]' core/components/*/admin/views/**/tmpl/*.blade.php

# Low opacity on text elements (opacity-35 through opacity-60 all fail 4.5:1)
grep -rn 'opacity-[3456][05]' core/components/*/admin/views/**/tmpl/*.blade.php

# Legacy $this-> usage in Blade
grep -rn '\$this->' core/components/*/admin/views/**/tmpl/*.blade.php

# Unlabeled selects (no aria-label and no matching label)
grep -rn '<select' core/components/*/admin/views/**/tmpl/*.blade.php | grep -v 'aria-label'

# Ordering inputs missing aria-label
grep -rn 'name="order\[\]"' core/components/*/admin/views/**/tmpl/*.blade.php | grep -v 'aria-label'

# Iframes missing title attribute
grep -rn '<iframe' core/components/*/admin/views/**/tmpl/*.blade.php | grep -v 'title='

# Status toggle links missing aria-label
grep -rn 'data-list-item-task' core/components/*/admin/views/**/tmpl/*.blade.php | grep -v 'aria-label'

# Labels in bare <div> wrappers (missing admin-field class)
grep -B1 'class="label"' core/components/*/admin/views/**/tmpl/*.blade.php | grep '<div>'

# <span class="label"> used as group heading (should be <legend>)
grep -rn '<span class="label">' core/components/*/admin/views/**/tmpl/*.blade.php

# Search inputs relying on placeholder only (missing sr-only label)
grep -rn 'placeholder=.*SEARCH\|placeholder=.*FILTER' core/components/*/admin/views/**/tmpl/*.blade.php | grep -v 'sr-only' | grep -v 'label'
```

# $form->getLabel() for/id mismatch (label generates for="name", but input has id="field-name")
grep -rn 'id="field-' core/components/*/admin/views/**/tmpl/*.blade.php | grep -v 'label'

# .label-text outside .admin-field (daisyUI mutes to ~60% opacity, fails contrast)
grep -rn 'class="label-text"' core/components/*/admin/views/**/tmpl/*.blade.php | grep -v 'text-base-content'

# Row-level opacity classes (reduces contrast of all children)
grep -rn "class=.*opacity-[3456][05]" core/components/*/admin/views/**/tmpl/*.blade.php
```

Note: the `<select>` grep will have many false positives (selects with
visible `<label for="">` are fine). Cross-reference with the preceding
line to check for a `<label>`.

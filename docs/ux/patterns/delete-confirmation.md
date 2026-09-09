# Delete Confirmation

Destructive action gate. Shows the user exactly what will be deleted and requires
explicit confirmation before proceeding.

## When to Use

- Deleting a blog entry, resource, project, or any record
- Removing a user from a group
- Revoking access or permissions
- Any irreversible action

## daisyUI Components Used

- [Card](https://daisyui.com/components/card/) — confirmation panel
- [Button](https://daisyui.com/components/button/) — delete/cancel actions
- [Alert](https://daisyui.com/components/alert/) — warning context

## New Markup

```html
<section class="py-8">
  <div class="max-w-lg mx-auto px-4">

    <div class="card bg-base-100 shadow-md" role="alertdialog"
         aria-labelledby="confirm-title" aria-describedby="confirm-desc">
      <div class="card-body text-center">

        <!-- Warning icon -->
        <div class="text-warning text-4xl mb-2" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>

        <!-- Title -->
        <h2 class="card-title justify-center text-xl" id="confirm-title">
          Delete "My Blog Entry"?
        </h2>

        <!-- Description -->
        <p class="text-muted-foreground" id="confirm-desc">
          This will permanently delete the blog entry and all associated
          comments. This action cannot be undone.
        </p>

        <!-- What will be affected -->
        <div class="alert alert-warning text-left mt-4">
          <div>
            <h3 class="font-medium text-sm mb-1">This will remove:</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
              <li>The blog entry "My Blog Entry"</li>
              <li>3 comments</li>
              <li>2 file attachments</li>
            </ul>
          </div>
        </div>

        <!-- Actions -->
        <form method="post" action="...">
          <div class="card-actions justify-center mt-4">
            <button class="btn btn-error" type="submit">Delete Entry</button>
            <a class="btn btn-ghost" href="...">Cancel</a>
          </div>
          <input type="hidden" name="task" value="delete" />
          <input type="hidden" name="id" value="42" />
          <!-- CSRF token -->
        </form>

      </div>
    </div>

  </div>
</section>
```

### Bulk Delete Variant

When deleting multiple items selected from a list:

```html
<section class="py-8">
  <div class="max-w-lg mx-auto px-4">

    <div class="card bg-base-100 shadow-md" role="alertdialog"
         aria-labelledby="confirm-title" aria-describedby="confirm-desc">
      <div class="card-body text-center">

        <div class="text-warning text-4xl mb-2" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>

        <h2 class="card-title justify-center text-xl" id="confirm-title">
          Delete 5 entries?
        </h2>

        <p class="text-muted-foreground" id="confirm-desc">
          The following entries will be permanently deleted.
          This action cannot be undone.
        </p>

        <div class="alert alert-warning text-left mt-4">
          <div>
            <h3 class="font-medium text-sm mb-1">Entries to delete:</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
              <li>Getting Started with Hubzero</li>
              <li>Research Data Management</li>
              <li>Summer Workshop Notes</li>
              <li>Conference Report 2025</li>
              <li>Lab Protocol v2</li>
            </ul>
          </div>
        </div>

        <form method="post" action="...">
          <div class="card-actions justify-center mt-4">
            <button class="btn btn-error" type="submit">Delete 5 Entries</button>
            <a class="btn btn-ghost" href="...">Cancel</a>
          </div>
          <input type="hidden" name="task" value="delete" />
          <input type="hidden" name="id[]" value="42" />
          <input type="hidden" name="id[]" value="43" />
          <input type="hidden" name="id[]" value="44" />
          <input type="hidden" name="id[]" value="45" />
          <input type="hidden" name="id[]" value="46" />
          <!-- CSRF token -->
        </form>

      </div>
    </div>

  </div>
</section>
```

## Structure Reference

| Element | daisyUI Class | Purpose |
|---------|---------------|---------|
| Panel | `.card .card-body .shadow-md` | Centered confirmation card |
| Title | `.card-title` | Names what is being deleted |
| Description | Tailwind utility | Explains consequences |
| Affected items | `.alert .alert-warning` | Lists what will be removed |
| Delete button | `.btn .btn-error` | Destructive action |
| Cancel | `.btn .btn-ghost` | Safe exit |
| Actions | `.card-actions` | Button container |

## Design Guidelines

- **Be specific**: Name the item being deleted in the title
- **Show consequences**: List everything that will be affected
- **State irreversibility**: "This action cannot be undone"
- **Red button**: `.btn-error` for destructive action
- **No double negatives**: "Delete Entry" not "Confirm deletion"
- **Bulk actions**: List affected items by name so users can verify

## Pattern Variants

com_blog uses `<x-confirm-dialog>` with the default slot for an impact list and
the `hiddenFields` slot for form data. See
`com_blog/site/views/entries/tmpl/delete.blade.php` for a reference case.

### Checkbox-gated delete with page context (com_wiki)

com_wiki's delete page is an inline form within the page context, not a
centered confirmation dialog. It renders inside `<x-page-container>` with the
wiki sidebar and submenu, showing the page breadcrumbs and author info above
the form. The form uses `<x-form-section>` with a single
`<x-form-field type="checkbox">` for explicit confirmation, followed by an
`alert alert-warning` explaining the consequences. The submit button uses
`btn-error`. The form is wrapped in a lock check (`page->isLocked()`) that
shows a warning instead of the form when the user lacks permissions.

This pattern is appropriate for wiki pages because the delete action is
contextual (the user is already viewing the page) and the checkbox gate
provides friction without pulling the user out of the page flow into a
modal-style dialog.

See `com_wiki/site/views/pages/tmpl/delete.blade.php` for the reference
implementation.

### Checkbox-gated discard with step context (com_resources)

com_resources' delete page (`create/tmpl/delete.blade.php`) is a contribution
discard flow rendered inside `<x-page-container>` with the step-nav partial
(showing the current wizard progress above the form). The form uses a 3-column
grid: a sidebar `alert alert-warning` explaining the consequences on the left,
and a `<fieldset>` on the right with the resource title/type, a
`checkbox checkbox-error` confirmation gate, and a `btn-error` submit button.

This pattern is appropriate for the contribution wizard because the delete
action occurs within the multi-step context — the step indicator shows users
where they are in the flow, and the checkbox gate provides explicit
confirmation friction. Unlike `<x-confirm-dialog>`, this is an in-page form
rather than a centered modal-style card.

See `com_resources/site/views/create/tmpl/delete.blade.php` for the reference
implementation.

### Checkbox-gated delete with explanation sidebar (com_courses)

com_courses' delete page uses `<x-page-container>` with `<x-confirm-dialog>`.
The dialog includes a message textarea (for logging the deletion reason), a
confirmation checkbox, and an "alternative to deleting" explanation in a
sidebar card that suggests unpublishing as a reversible option. The impact list
in the default slot names the course and warns about cascading deletion of
offerings, sections, and enrolled student data.

This pattern is appropriate because course deletion has significant downstream
effects (enrollment data, certificates, grades) and the "alternative to
deleting" sidebar helps prevent unnecessary data loss.

See `com_courses/site/views/course/tmpl/delete.blade.php` for the reference
implementation.

## Accessibility Notes

- `role="alertdialog"` tells assistive technology this requires immediate attention
- `aria-labelledby` points to the title, `aria-describedby` to the description
- Warning icon uses `aria-hidden="true"` since the text conveys the meaning
- Delete button uses `.btn-error` (red) **plus** text label — color is never the
  sole indicator (WCAG 1.4.1)
- Cancel link is a visible escape route — not hidden or de-emphasized
- Button text states the action clearly: "Delete Entry" not "Yes" or "Confirm"
- Focus should move to this card when navigated to (via `autofocus` on the
  delete button or programmatic focus)
- See [Accessibility Guide](../guides/accessibility.md) for full WCAG 2.2 AA requirements

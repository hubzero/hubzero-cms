<!--
status: rewritten
reviewed-against: 2.4-main @ 123ea53b14
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/users/usernotes, https://help.hubzero.org/documentation/240/managers/users/usernotecateg
imported: 2026-09-09
-->
# User notes

A user note is a record an administrator keeps about a member account:
what a support case was about, why an account was suspended, what was agreed
in a phone call. Notes are filed under categories and can carry a review
date.

> **Note:** Users do not write these. There is no site-facing notes feature
> in 2.4 — the model and its screens exist only in the administrator
> interface, and a note is written *about* a member, not *by* one.

Notes live in `com_members`, at **Users** → **User Notes**, or under
**Users** → **Members** → **Notes**. Categories are ordinary content
categories belonging to `com_members`, at **Users** → **User Note
Categories**, which opens `com_categories`.

## The note list

Each row shows the **User** the note is about, its **Subject**, its
**Category**, a status icon, the **Review date** and the note **ID**.
Selecting the user's name opens the note. A note someone else has open shows
a checked-out marker.

Filter with:

- The search box, which matches the note's subject and body. Enter
  `uid:123` instead to list every note about one account by its member ID.
- The category menu.
- The status menu: Published, Unpublished, Archived, Trashed, All.

The toolbar carries **New**, **Edit**, **Publish**, **Unpublish**,
**Archive**, **Check-in**, **Trash** and **Options**. When the status filter
is set to Trashed, **Trash** is replaced by **Empty Trash**.

> **Warning:** Most of that toolbar does nothing in 2.4. Only **New** and
> **Options** work. **Publish**, **Unpublish**, **Archive**, **Check-in** and
> **Empty Trash** submit tasks the notes controller does not implement, and
> the status icon in the list is inert for the same reason. **Edit** and
> **Trash** reach real code but look for the selected rows under the wrong
> request name, so they always behave as though nothing were ticked: **Edit**
> opens a blank new note and **Trash** reports *No rows selected*. Selecting
> anything else just redraws the list.
>
> What does work: open a note by selecting the user's name in its row, and
> change its status with the **State** menu on the form. Deleting a note has
> to be done in the database.

> **Note:** The sort links in the column headings also fail. They set a sort
> column qualified with a table alias the query does not use, which the
> database rejects. Leave the list on its default order, newest review date
> first.

## Writing a note

1. Go to **Users** → **User Notes**.
2. Select **New**.
3. Fill in the form:

| Field | Meaning |
|---|---|
| **Subject** | A one-line summary. Required |
| **Body** | The note itself, in the editor |
| **Category** | One of the note categories, or none |
| **User** | The member the note is about. Required |
| **State** | Published, Unpublished or Trashed |
| **Review time** | A date to look at this note again. Shown in the list's Review date column and left blank if you do not need one |

4. Select **Save**.

**Apply** saves and stays on the form; **Save** saves and returns to the
list.

> **Warning:** Do not use **Trashed**. The form stores it as state 2, which
> is the platform's value for *archived*: the list then shows the note as
> **Archived**, and the list's own **Trashed** filter, which looks for state
> -2, never finds it. Use **Unpublished** to take a note out of circulation.

> **Warning:** **Save & New** and **Save as Copy** on the note form are not
> implemented either. Both discard what you typed and return you to the
> list. Use **Save**, then **New**.

## Note categories

Categories organise notes; a hub with a handful of notes does not need them,
one that logs every support contact does.

Go to **Users** → **User Note Categories**. The screen is `com_categories`
scoped to `com_members`, the same manager used for article categories, and it
behaves the same way: create, edit, publish, reorder, nest, batch-process and
trash. The categories it shows belong to notes alone; they do not appear
anywhere else on the hub.

A note does not have to have a category, and the note list's category filter
is the only thing categories change.

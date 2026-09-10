<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
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

## Whether to use this screen at all

Decide that before you start, because the screen is only half-built and
switching to something else later means retyping.

It works for one thing: writing a note against an account and reading it back
later. That is genuinely useful on a hub where more than one person answers
support mail and the next person needs to know why an account was suspended in
March. If that is your problem, the screen solves it.

Almost everything else on it does not work. Seven of the toolbar's actions —
**Publish**, **Unpublish**, **Archive**, **Check-in** and **Empty Trash** on
the list, **Save & New** and **Save as Copy** on the form — submit tasks the
notes controller does not implement, and pressing one simply redraws the
screen. **Edit** and **Trash** reach real code but read the selected rows under
a name the list never posts, so they behave as though you had ticked nothing.
The column sort links fail. Deleting a note cannot be done from the interface
at all.

So: use it if you want an append-mostly log against an account and can live
with notes accumulating. Do not use it if you need to retract, tidy or archive
what you write, or if the notes have to be got rid of on request — a
data-protection erasure request cannot be honoured from this screen.

> **Caution:** A user note is personal information about a real person, kept
> where they cannot see it and cannot correct it, and in this release cannot be
> deleted without database access. Write what a support record needs and no
> more.

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

Notes are written one at a time and only from the **New** button. Everything
else on the toolbar is either inert or misreads the selection, so this is the
whole of what the screen does. Nothing here is sent to anyone and nothing is
visible on the site.

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
one that logs every support contact does. Decide early whether you want them,
because the note list's category filter is the only way to narrow a long list —
the sort links do not work and the search box matches subject and body only.

Go to **Users** → **User Note Categories**. The screen is `com_categories`
scoped to `com_members`, the same manager used for article categories, and it
behaves the same way: create, edit, publish, reorder, nest, batch-process and
trash. The categories it shows belong to notes alone; they do not appear
anywhere else on the hub.

A note does not have to have a category, and the note list's category filter
is the only thing categories change.

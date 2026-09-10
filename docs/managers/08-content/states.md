<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
-->
# States, deleting and check-out

Every list screen in the administrator interface has a state column and a
state filter, and almost every one of them offers some combination of
**Published**, **Unpublished**, **Archived**, **Trashed** and **All**. This
chapter says what those words mean, what number each one stores, what
happens when you delete something, and why a record sometimes refuses to
open because it is "checked out". It is for anyone who administers a hub and
has to decide whether a record is safe to remove.

Read it before you press a delete button anywhere on this hub. It is not a
chapter about articles; it describes behaviour shared by nearly every screen
in the administrator interface, and it explains a set of defects a manager
will otherwise spend an afternoon assuming are their own mistake.

The short version: **the labels on screen do not map one-to-one onto the
values in the database, and the same word means different numbers on
different screens.** The rest of this chapter is the detail.

## What this costs you in practice

Four things follow from that, and every one of them has been mistaken for
user error by somebody:

- A record you trashed shows in the list as **Archived**, or the other way
  round, because the editor and the list next to it disagree about which word
  goes with which number.
- A **Trashed** filter finds nothing, because on that screen it asks for a
  number the component never writes.
- A **Delete** button leaves the record in the database for ever, because it
  only sets a state and no screen offers the second step.
- A **Trash** button erases the record outright, with no undo, because the
  component wired that button to a destroy method.

The last one is the one to be careful about. **Trash** and **Delete** are not
reliable words on this hub. Before pressing either on a screen you have not
used before, read [Deleting](#deleting) below.

## The stored values

The hub has two model base classes, and both define the same three numbers.

[`Relational`](../../../core/libraries/Hubzero/Database/Relational.php), which
the newer components use:

<!--include: core/libraries/Hubzero/Database/Relational.php:43-45-->

[`Base\Model`](../../../core/libraries/Hubzero/Base/Model.php), which the
older ones use, adds a fourth:

<!--include: core/libraries/Hubzero/Base/Model.php:22-43-->

So at the platform level there are three states a record can be in —
unpublished, published, deleted — plus a flagged value used for reported
content. **There is no archived state and no trashed state in either base
class.** `2` means deleted.

## The filter labels

The filter dropdown on the older administrator lists is built by
`publishedOptions()` in
[`Html\Builder\Grid`](../../../core/libraries/Hubzero/Html/Builder/Grid.php),
which `Html::grid('publishedOptions')` calls. It offers five entries:

| Label on screen | Value it submits |
|---|---|
| **Published** | `1` |
| **Unpublished** | `0` |
| **Archived** | `2` |
| **Trashed** | `-2` |
| **All** | `*` |

The same file's `published()` helper draws the state icon in a row using the
same four numbers, so a row holding `2` is drawn as **Archived** and a row
holding `-2` as **Trashed**.

Compare that with the list above and the collision is obvious: `2` is
**Archived** to the grid helper and **deleted** to both model base classes.
And `-2` — the value behind the word **Trashed** on those screens — is not a
state either base class knows about at all.

## Which vocabulary a screen uses

There is no way to tell from the label, so use the screen itself:

**Screens using the inherited four-value scheme** store `1`, `0`, `2`
(archived) and `-2` (trashed). The Article Manager is the clearest example:
its state task maps exactly those four tasks onto exactly those four values,
in
[`com_content/admin/controllers/articles.php`](../../../core/components/com_content/admin/controllers/articles.php).
The module manager and the language manager present the same four words.

**Screens using the platform scheme** store `1`, `0` and `2`, where `2` is a
soft delete — the record is kept, but it is gone as far as the site is
concerned. Deleting a blog entry on the site sets `state` to `2`; the blog
administrator list then offers three states and one all-states entry, and it
labels `2` **Trashed**:

| Label in the blog list | Value |
|---|---|
| **- All States -** | `-1` |
| **Unpublished** | `0` |
| **Published** | `1` |
| **Trashed** | `2` |

Both screens show you a **Trashed** filter. On one it means `-2`; on the
other it means `2`. Both are internally consistent. Neither is wrong. They
are simply two different conventions living in one administrator interface.

The front end is not confused by any of this, because it asks only for
`state = 1`. Every module that lists content — see
[`mod_articles_latest`](../../../core/modules/mod_articles_latest/helper.php),
for instance — filters on the published value and nothing else. Anything
that is not published is invisible to visitors, whichever of the other
numbers it holds.

> **Note:** Some tables call the column `published` rather than `state` —
> modules and languages both do. The values are the same; only the column
> name differs.

## State is not access, and it is not deletion

Three separate things get confused here, and keeping them apart saves most of
the trouble on this page.

- **State** decides whether the record exists for the site at all. An
  unpublished article answers 404, even to an administrator following a menu
  item straight at it — unless they hold edit rights on the component.
- **Access** decides who may read a record that does exist. See
  [Access levels](../06-users/07-accesslevels.md).
- **Check-out** decides who may edit it in the administrator interface. It
  has no effect on the site at all.

So taking a page off the site is a state change, restricting it to members is
an access change, and neither of them removes anything. If you want a page
gone from the site tonight and the decision reversible tomorrow, unpublish
it. That is the safe move, on every screen, in every component.

## A worked example: member notes

**Members → Notes** is the screen that shows what happens when the two
vocabularies meet in one component, and it is worth walking through because
every symptom here is one you could otherwise waste an afternoon on.

The note editor offers three states, in
[`admin/views/notes/tmpl/edit.php`](../../../core/components/com_members/admin/views/notes/tmpl/edit.php):
**Unpublished** (`0`), **Published** (`1`) and **Trashed** (`2`). That is the
platform scheme, and by the platform's own definition `2` is deleted, not
trashed.

The list screen next door uses the inherited helper. Its state column calls
`Html::grid('published')`, so a note saved as **Trashed** in the editor is
drawn in the list as **Archived**. The same record, the same number, two
labels, one screen apart.

Its filter dropdown is built by `Html::grid('publishedOptions')`, so it
offers **Trashed** as `-2` — a value the note editor cannot produce and
nothing in the component ever writes. Selecting it could not match a note
even if the filter worked.

It does not work. The dropdown is named `filter_published` in
[`display.php`](../../../core/components/com_members/admin/views/notes/tmpl/display.php),
while the controller reads a request variable called `state` in
[`notes.php`](../../../core/components/com_members/admin/controllers/notes.php).
Nothing connects the two, so the filter defaults to `-1` on every request,
the controller's `if ($filters['state'] >= 0)` test never passes, and the
list always shows every note regardless of what the dropdown says.

The same screen's toolbar draws **Publish**, **Unpublish**, **Archive** and
**Check-in** buttons. The notes controller implements none of those tasks,
and an unrecognised task falls through to the default task, which is to
redraw the list. So those four buttons load the page again and change
nothing — no error, no message.

One button on that toolbar does work, and it is the dangerous one. See below.

## Deleting

There is no single delete behaviour to learn. There are three, and which one
a screen gives you is not stated anywhere on the screen.

**Some screens have no permanent delete at all.** The Article Manager is
one. Its **Delete** button issues the `trash` task, which sets `state` to
`-2` and nothing else; the controller has no task that removes a row. Once
an article is trashed it stays in the database until somebody removes it by
hand.

**Some screens have the full pair.** The module manager is the model to
follow: **Trash** sets `published` to `-2`, and the toolbar swaps that button
for **Empty Trash** — a genuine delete — only while the trashed filter is
active. You cannot destroy a module you have not first trashed and then gone
looking for.

**And some screens delete outright from a button labelled Trash.** This is
the case to know about. `Toolbar::trash()` defaults to the task `remove`, and
whether that means "set the trashed state" or "erase the row" depends
entirely on what the component's `removeTask()` does. Two verified cases
where it erases:

- **Members → Notes.** `removeTask()` calls `destroy()` on each selected
  note. The button says **Trash**. The note is gone.
- **Languages.** `removeTask()` calls `destroy()` on each selected language.
  Its state task only ever writes `1` or `0`, so no language can reach `-2`,
  which means the **Empty Trash** button its list screen is coded to show can
  never appear, and its **Trashed** filter can never match anything. The only
  removal that screen offers is the permanent one.

> **Warning:** On some screens the button labelled **Trash** permanently
> destroys the record. There is no confirmation, no undo, and nothing on the
> screen distinguishes it from the screens where **Trash** is reversible.
> Before you press it on a screen you have not used before, select one
> expendable record, press it, then set the state filter to **Trashed** and
> look for the record. If it is not there, that button is a permanent delete
> and your only undo is a database backup.

The same caution applies in reverse to **Delete**. On the Article Manager and
the Category Manager it is safe: it trashes, and you can bring the record
back. On other screens the same word erases. The wording tells you nothing —
only the test above does.

A permanent delete is thorough. `destroy()` removes the record's access
asset row first, fires a `system.onContentDestroy` event so that other
extensions can clean up after it, and then removes the row. Nothing keeps a
copy.

## Check-out

Separate from state, and easily mistaken for it: a record can be locked.

The situation is ordinary. A colleague opens the terms of use article on
Friday afternoon to fix a sentence, gets called away, and closes the laptop.
On Monday nobody can edit that article, and the list shows a padlock next to
it with their name on the tooltip. Nothing is broken and nothing is lost —
the article is still published and visitors still see it — but the lock will
sit there until a person clears it.

Several of the hub's tables carry `checked_out` and `checked_out_time`
columns. Opening a record for editing writes your user ID and the current
time into them, and the list screen then draws a padlock beside the row and
refuses the edit form to anybody else. Saving or cancelling clears the pair.

Nothing else does. Closing the tab, letting the session time out, or hitting
an error mid-request all leave the lock in place, and **nothing expires it** —
there is no timeout and no scheduled job that sweeps locks up. A record left
checked out stays checked out until a person releases it.

That is what **Site → Maintenance → Global Check-in** is for, and it is
described in [Check-in](../09-components/07-checkin.md). To clear the
Friday-afternoon lock above:

1. Go to **Site → Maintenance → Global Check-in**.
2. Find the row for the table the record is in — articles are `#__content`.
   The count beside it is how many records that table has locked.
3. Tick that row and select **Check-in**.

Releasing a lock does not save the abandoned edit; whatever your colleague
had typed and not saved is gone either way. It only makes the record openable
again. Checking in a table somebody is genuinely working in right now costs
them nothing worse than a second lock the next time they save, so this is a
safe button.

The Article Manager and most other list screens also carry a **Check In**
button that does the same thing for the records you have selected, which is
usually quicker when you know which record is stuck.

> **Note:** A checked-out record is still published. Visitors see it exactly
> as before. Check-out only affects who may edit in the administrator
> interface.

## See also

- [Article Manager](articlemanager.md) — the four-state scheme in the screen
  where a manager most often meets it.
- [Categories](categories.md) — categories carry their own state, and a
  published article in an unpublished category is not reachable.
- [Check-in](../09-components/07-checkin.md) — the screen that releases stuck
  records.

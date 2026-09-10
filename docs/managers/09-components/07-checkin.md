<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
-->
# Check-in

Global Check-in releases records that an editor left locked. It is one screen,
under **Site → Maintenance → Global Check-in**, and on a healthy hub it is
empty. You open it when someone reports that they cannot edit a record because
the interface says it is already being edited by somebody who is no longer
editing it.

The report arrives in a hurry, and it is always the same shape. A staff member
opens the front-page article on Friday afternoon to fix a date, shuts the
laptop, and goes home. On Monday the communications officer needs that article
and cannot have it: the list draws a padlock beside the row and refuses the
edit form. Nothing on the hub clears that by itself. This screen is how you
clear it.

## Clearing a stuck record in a hurry

If you already know the record is stuck and you only want it back:

1. Go to **Site → Maintenance → Global Check-in**.
2. Find the table the record lives in. Articles and categories are
   `#__content` and `#__categories`, menu items `#__menu`, modules
   `#__modules`, resources `#__resources`, publications `#__publications`.
   The search box filters the list on the table name.
3. Tick that table and press **Check-in**.
4. Tell whoever was editing that the form they left open is now worthless.
   Anything they had typed and not saved is gone, and if they go back to that
   tab and press **Save**, they will overwrite whatever anyone else does in
   the meantime.

That is the whole task. The rest of this chapter explains what it did, and the
one case where you should ask before doing it.

## What it is not

Check-in is not a save, a publish, or a permissions setting. It does not
recover a draft, it does not change who may edit a record, and it has nothing
to do with the cache. If a record refuses to open and there is no padlock on
its row, this screen is not the answer — look at the member's group
permissions or at the record's own **Access** setting instead.

## What being checked out means

Several of the hub's tables carry a pair of columns, `checked_out` and
`checked_out_time`. When an administrator opens a record for editing, the
component writes their user ID and the current time into those two columns.
That is the lock. While it is set, the list screen shows a padlock beside the
row and other administrators are refused the edit form.

Pressing **Save**, **Save & Close** or **Cancel** clears the pair again.
Anything else does not. A record stays locked when the editor:

- closes the browser tab, or navigates away with the browser's back button;
- loses the session to a timeout while the form is open;
- hits an error that abandons the request before the save;
- is logged out by an administrator mid-edit.

Nothing expires the lock on its own. There is no timeout and no cron job that
sweeps them up — the row stays checked out until someone checks it in, which
is what this screen is for.

## The screen

The list has one row per **database table**, not per record:

| Column | Meaning |
|---|---|
| **Database Table** | The table name, with the hub's table prefix. |
| **Items to check-in** | How many rows in it are currently checked out. |

A table appears only when all three of these are true: its name starts with
the prefix this installation owns, it has both `checked_out` and
`checked_out_time` columns, and at least one of its rows is checked out. So
**an empty list is the normal state** and means nothing is stuck.

Both columns sort, the list pages, and the search box filters on the table
name — it is a plain substring match against the name, so `content` finds
`#__content` and `#__content_frontpage`.

Tick the tables you want released and press **Check-in**. Every checked-out
row in each ticked table is released at once: `checked_out` goes back to `0`
and `checked_out_time` to the column's default, and nothing else in the row is
touched. This screen has no way to release one record and leave another in the
same table locked.

That bluntness is the reason to look at the count first. Ticking `#__content`
on a hub where one article is stuck releases one lock. Doing it at eleven in
the morning on a hub where six people are mid-edit releases all six, and none
of them will be told. If the count is higher than the number of complaints you
have received, release the single record from its own component instead.

For one record, go to the component that owns it instead. The older
administrator lists — articles, categories, menu items, modules, plugins, and
the Extension Manager — draw a padlock on a locked row, naming who has it and
since when, and clicking the padlock checks in that one record. The padlock is
only clickable for someone who holds *Access Administration Interface* on
`com_checkin`, so this component's permissions decide who can break a lock
anywhere on the hub.

> **Warning:** Checking in a record does not save anything. Whatever the
> original editor had typed into the form is gone; the lock is simply
> released so that somebody else can open the record. If the editor is still
> sitting on the form and then presses **Save**, their version wins over
> anything saved in between.

The toolbar carries **Check-in**, **Options** and **Help**. **Options** opens
the component's permissions grid, which is the only setting it has. There is
nothing to tune: the component ships no configuration, and the grid's default —
everything inherited — is the right setting on almost every hub.

> **Note:** The check-in resets the two lock columns and nothing else. The
> model carries a third branch, meant to blank an `editor` column at the same
> time, whose test can never be true, so it never runs. It should be deleted
> rather than repaired: the only `editor` column on a stock schema belongs to
> `#__citations` and holds a citation's bibliographic editor, which is not a
> lock and must not be erased by a check-in. Recorded in
> a record kept with the project.

> **Note:** The screen itself is available to anyone with *Access
> Administration Interface* on the component, but the **Check-in** and
> **Options** buttons — and the menu entry that leads here — are drawn only
> for someone with *Configure*. In practice check-in is a Super User action.

## Doing it without the screen

There is no muse command for check-in and nothing schedules one, but the
component has a REST equivalent — `GET /api/checkin/list` and
`DELETE /api/checkin/checkin`, the second taking one or more table names —
documented in the [check-in API reference](../../reference/api/checkin.md).

Failing that, the query is short enough to run by hand:

```sql
UPDATE `#__content` SET checked_out = 0, checked_out_time = '0000-00-00 00:00:00'
WHERE checked_out > 0;
```

That is exactly what the screen does, one table at a time.

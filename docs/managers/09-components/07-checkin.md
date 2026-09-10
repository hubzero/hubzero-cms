<!--
status: rewritten
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
-->
# Check-in

Global Check-in releases records that an editor left locked. It is one screen,
under **Site → Maintenance → Global Check-in**, and on a healthy hub it is
empty. You open it when someone reports that they cannot edit a record because
the interface says it is already being edited by somebody who is no longer
editing it.

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
and `checked_out_time` to the column's default. This screen has no way to
release one record and leave another in the same table locked.

For one record, go to the component that owns it instead. The older
administrator lists — articles, categories, menu items, plugins — draw a
padlock on a locked row, naming who has it and since when, and clicking the
padlock checks in that one record. The padlock is only clickable for someone
who holds *Access Administration Interface* on `com_checkin`, so this
component's permissions decide who can break a lock anywhere on the hub.

> **Warning:** Checking in a record does not save anything. Whatever the
> original editor had typed into the form is gone; the lock is simply
> released so that somebody else can open the record. If the editor is still
> sitting on the form and then presses **Save**, their version wins over
> anything saved in between.

The toolbar carries **Check-in**, **Options** and **Help**. **Options** opens
the component's permissions grid, which is the only setting it has.

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

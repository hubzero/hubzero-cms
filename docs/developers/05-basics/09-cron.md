<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/basics/cron
-->
# Scheduled tasks

Work that has to happen on a schedule — closing stale tickets, sending
digests, releasing instrument bookings nobody confirmed — is written as a
**cron plugin**. Two halves:

- **Yours.** A plugin declares the tasks it can perform and implements them.
  That is this page.
- **The administrator's.** A job names one of those tasks, says how often to
  run it, and fills in its parameters. The runner ticks and fires it. That is
  [scheduled tasks](../../managers/03-maintenance/05-cron.md) in the managers
  book.

The split is the point. Nothing in the plugin decides when it runs, so the
same task can back several jobs with different parameters and different
schedules, and an administrator can change any of that without touching your
code.

## The smallest working plugin

A plugin in the `cron` group with two methods: one that lists the tasks, one
that is the task.

```php
<?php
defined('_HZEXEC_') or die();

class plgCronBookings extends \Hubzero\Plugin\Plugin
{
    public function onCronEvents()
    {
        $this->loadLanguage();

        $obj = new stdClass();
        $obj->plugin = 'bookings';
        $obj->events = array(
            array(
                'name'   => 'releaseExpiredHolds',
                'label'  => Lang::txt('PLG_CRON_BOOKINGS_RELEASE_HOLDS'),
                'params' => ''
            )
        );

        return $obj;
    }

    public function releaseExpiredHolds(\Components\Cron\Models\Job $job)
    {
        // ... do the work ...
    }
}
```

Install it, create a job naming **Release expired holds**, and it runs.

## Where the plugin goes

In the `cron` group, one directory per plugin:

```
core/plugins/cron/support/support.php
core/plugins/cron/support/support.xml
core/plugins/cron/support/language/en-GB/en-GB.plg_cron_support.ini
```

A hub's own plugins go under `app/plugins/cron/` instead, and are found the
same way. Put yours there unless you are changing the distribution.

## Declaring tasks

The plugin answers `onCronEvents` with a `stdClass` carrying a `plugin`
name and an `events` array. The support plugin is the fullest example in the
tree:

<!--include: core/plugins/cron/support/support.php:16-52-->

Each entry has three keys:

| Key | Notes |
|---|---|
| `name` | The method to call. It must match a public method on the plugin **exactly** |
| `label` | What the administrator sees in the job's **Event** list. Translate it |
| `params` | Optional. The name of a fieldset in the plugin's manifest whose fields become the job's own parameters |

> **Warning:** A `name` that does not match a method is the failure to watch
> for. The runner triggers the event, no listener answers, and it prints
> *Finished event* and advances the job's `next_run` exactly as if the work
> had been done. The job's history shows a clean run every time and nothing
> happens. Check the spelling and the case against the method.

`loadLanguage()` is the first line for a reason. Cron plugins do not set
`$_autoloadLanguage`, so without it the labels come back as raw keys in the
administrator's job form — see [languages](04-languages.md).

## Job parameters

`params` is what lets one task back several jobs. The support plugin's
`sendTicketsReminder` names the `ticketreminder` fieldset in its manifest:

```xml
<fieldset group="ticketreminder">
    <field name="support_ticketreminder_severity" type="list" default="all"
        label="Tickets with severity" description="Ticket severity to message users about.">
        <option value="all">PLG_CRON_SUPPORT_ALL</option>
        <option value="critical,major">PLG_CRON_SUPPORT_HIGH</option>
        <option value="normal">PLG_CRON_SUPPORT_NORMAL</option>
        <option value="minor">PLG_CRON_SUPPORT_LOW</option>
    </field>
    <field name="support_ticketreminder_group" type="text" menu="hide"
        label="For users in group" default="" description="Only users within the group specified will be messaged." />
</fieldset>
```

An administrator can then have one job mailing reminders for all open
tickets weekly and another mailing only the critical ones daily, both
running the same method.

The fieldset is optional. Leave `params` empty where the task has nothing to
configure, as the booking plugin above does.

## Writing the task

The runner triggers `cron.<event name>` with the job as its only argument,
so the method signature is fixed:

```php
/**
 * Release instrument holds nobody confirmed
 *
 * @param   object   $job  \Components\Cron\Models\Job
 * @return  void
 */
public function releaseExpiredHolds(\Components\Cron\Models\Job $job)
{
    $params = $job->params;

    $hours = intval($params->get('bookings_hold_hours', '24'));

    $cutoff = Date::of('now')->modify('-' . $hours . ' hours')->toSql();

    foreach (Booking::all()->whereEquals('state', 'held')
                 ->where('created', '<', $cutoff)->rows() as $booking)
    {
        $booking->set('state', 'free')->save();
    }
}
```

Three things to know about that method.

**`$job->params` is a `Registry`** of the values the administrator entered on
that job — not the plugin's own parameters, which are still on
`$this->params`. This is why two jobs on one method behave differently.

**The return value is discarded.** The runner calls `Event::trigger()` and
ignores what comes back, so returning `false` does not mark the job failed
and returning `true` does not mark it succeeded. The tree's cron plugins
return `true` out of habit; nothing reads it. To signal a failure, throw.

**The cutoff is UTC.** `next_run`, `created` and every other stored datetime
are UTC, and so is `Date::of('now')`. Never build a cutoff from a local
time — see [dates](10-dates.md).

> **Note:** Because the group prefix on the trigger is `cron`, the whole
> `cron` plugin group is loaded when the runner fires — including plugins
> whose tasks no job uses. Keep the constructor and `onCronEvents()` cheap;
> do the work in the task method.

## Testing it

Force the job, ignoring its schedule:

```bash
muse cron:jobs run --job=12
```

That is the loop you want while writing a task. The runner claims the job,
triggers the event, prints *Starting* and *Finished* lines, and releases the
job whether the task returned, threw, or fataled — so a broken task does not
wedge the job or stop the others in the run. An exception is caught, its
message printed against the job, and the run recorded.

Print nothing from a task. It may run inside a web request rather than on
the command line, and output there lands in the page.

`muse cron:jobs list` shows what is due. The full set of runner tasks, and
how the tick is delivered, is in
[scheduled tasks](../../managers/03-maintenance/05-cron.md).

## Registering the job

Until a job exists, a declared task never runs. Rather than asking every
hub's administrator to create one, ship a
[migration](../06-database.md#migrations) that inserts the row — check for the
job first so re-running the migration does not duplicate it:

<!--include: core/plugins/cron/groups/migrations/Migration20260810000000PlgCronGroups.php:19-56-->

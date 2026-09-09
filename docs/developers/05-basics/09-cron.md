<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/basics/cron
-->
# Scheduled tasks

Work that has to happen on a schedule — closing stale tickets, sending
digests, cleaning up temporary uploads — is written as a **cron plugin**.
The plugin declares the tasks it can perform; an administrator creates a
job that names one of them and says how often to run it; the cron runner
triggers it.

Nothing in the plugin decides when it runs. That is the job's business, and
the same task can back several jobs with different parameters.

## Where the plugin goes

In the `cron` group, one directory per plugin:

```
core/plugins/cron/support/support.php
core/plugins/cron/support/support.xml
core/plugins/cron/support/language/en-GB/en-GB.plg_cron_support.ini
```

A hub's own plugins go under `app/plugins/cron/` instead, and are found the
same way.

## Declaring tasks

The plugin answers `onCronEvents` with a `stdClass` carrying a `plugin`
name and an `events` array:

<!--include: core/plugins/cron/support/support.php:16-52-->

Each entry has three keys:

| Key | Notes |
|---|---|
| `name` | The method to call. It must match a public method on the plugin **exactly** |
| `label` | What the administrator sees in the job's **Event** list. Translate it |
| `params` | Optional. The name of a fieldset in the plugin's manifest whose fields become the job's own parameters |

`loadLanguage()` is the first line for a reason. Cron plugins do not set
`$_autoloadLanguage`, so without it the labels come back as raw keys in the
administrator's job form.

`params` is what lets one task back several jobs. The support plugin's
`sendTicketsReminder` names the `ticketreminder` fieldset:

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

## Writing the task

The runner triggers `cron.<event name>` with the job as its only argument,
so the method signature is fixed:

```php
/**
 * Delete temporary ticket uploads older than the configured age
 *
 * @param   object   $job  \Components\Cron\Models\Job
 * @return  boolean
 */
public function cleanTempUploads(\Components\Cron\Models\Job $job)
{
    $params = $job->params;

    $days = intval($params->get('support_tickettemp_age', '7'));

    // ... do the work ...

    return true;
}
```

`$job->params` is a `Registry` of the values the administrator entered on
that job, which is why two jobs on one method can behave differently. Return
`true` on completion.

> **Note:** Because the group prefix on the trigger is `cron`, the whole
> `cron` plugin group is loaded when the runner fires — including plugins
> whose tasks no job uses. Keep the constructor and `onCronEvents()` cheap;
> do the work in the task method.

## How it runs

The runner is a [muse](../muse/README.md) command:

```bash
muse cron:jobs run
```

It selects published jobs whose `next_run` has passed and which are inside
their publish window, claims each one atomically so two runners cannot take
the same job, triggers the event, and records the run:

```php
Event::trigger('cron.' . $job->get('event'), array($job));
```

The claim is what makes it safe to run the command on a short interval. A
job left `active` by a process that died is reclaimed once its pid is gone,
and a job whose recurrence cannot be parsed keeps its `next_run` and is
retried rather than wedging. `--job=<id>` force-runs one job regardless of
its schedule, which is how you test a new task.

A thrown exception is caught and logged against the job; the job is released
either way, so one failing task does not stop the rest of the run.

## Registering the job

Administrators create jobs in **Components → Cron**, choosing the event from
the list your `onCronEvents` produced, setting a recurrence, and filling in
whatever fields your `params` fieldset declared. See the
[managers documentation](../../managers/03-maintenance/05-cron.md) for that
side of it.

Until a job exists, a declared task never runs. Rather than asking every
hub's administrator to create one, ship a
[migration](../database/migrations.md) that inserts the row — check for the
job first so re-running the migration does not duplicate it:

<!--include: core/plugins/cron/groups/migrations/Migration20260810000000PlgCronGroups.php:19-56-->

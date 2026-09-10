<!--
status: imported
source: core/components/com_cron/admin/help/en-GB/job.phtml
imported: 2026-09-09
-->
# Cron Manager: Job

## Overview

A cron job is tied to a specific plugin event. Plugins will generally be named after the component whose data said plugin processes. For example, "plg_cron_support" will process or manipulate various content found in the support component (com_support).

## Parameters

Each plugin event may specify parameters or settings that can be specified for the chosen event. Parameters are stored on a per-job basis, allowing you to have multiple jobs that utilize the same event but are passed different parameters. For example, one could have a cron job that sends out an email reminder once a week for support tickets that are marked "high prioerity" and another job that sends out a reminder once a month for tickets marked as "normal priority".

Event parameters are generally optional and some events may not even have parameters associated with them.

## Recurrence

Several common recurrences are specified for ease of use but, if desired, one can specify a custom recurrence by choosing "Custom" from the select list.

## Cron Expression

```
# * * * * *
# ┬ ┬ ┬ ┬ ┬
# │ │ │ │ │
# │ │ │ │ │
# │ │ │ │ └───── day of week (0 - 7) (0 to 6 are Sunday to Saturday; 7 is Sunday, the same as 0)
# │ │ │ └────────── month (1 - 12)
# │ │ └─────────────── day of month (1 - 31)
# │ └──────────────────── hour (0 - 23)
# └───────────────────────── min (0 - 59)
```

## Publishing

The following options control various aspects of the published state (when and if a job should be run).

- **State**  
  The published status of the job. Unpublished and trashed jobs will never be run.
- **Start running**  
  A timestamp (YYYY-MM-DD hh:mm:ss) for when a cron job should **start** being run. This allows for a cron job to be set up and published in advance of when one wishes the job to start running. When used with "Stop running", this gives the option of creating a window of time one wants a cron job to be run. For instance, a job can be scheduled to run every week between March and September.
- **Stop running**  
  A timestamp (YYYY-MM-DD hh:mm:ss) for when a cron job should **stop** being run. This allows for a cron job stop being run after a given date/time. When used with "Start running", this gives the option of creating a window of time one wants a cron job to be run. For instance, a job can be scheduled to run every week between March and September.

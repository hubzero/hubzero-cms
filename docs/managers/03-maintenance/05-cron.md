<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/maintenance/cron
source-id: 3344
modified: 2013-11-01
imported: 2026-09-09
-->
# Scheduled Tasks

## Overview

A cron component is available for managing and running scheduled tasks for your Hub.

The Cron Job Manager can be found by logging into the **/administrator** interface. Once logged in, hover over **Components** and clicking on **Cron** from the drop-down.

![](../media/cron-cron-manager-01.png)

### What is cron?

Cron is the name of program that enables unix users to execute commands or scripts (groups of commands) automatically at a specified time/date. It is normally used for sys admin commands.

A hub has a specific cron job that calls to the CMS. From there the code executes a list of jobs that can be created, published/unpublished, modified, and deleted via the administrator interface.

## Create/Edit Jobs

When you create a new job, you will select the event to trigger. Each event calls to a specific plugin and performs its own set of tasks.

All jobs have two sections that are the same: Details and Recurrence. The Parameters are different for each event.

### <a id="editfields-details"></a>Details

![](../media/cron-cron-manager-03.png)

- **Event**  
  This displays the system name of the module. No entry is allowed.
- **Title**  
  The Title of the Module.
- **State**  
  Whether or not the job is enabled. If "No", the job will not be run.
- **Recurrence**  
  This is how often a job should be run. Several common values are provided--such as "run once a day, midnight"--with the option to specify your own by selecting "custom" front he drop-down list.

### <a id="editfields-recurrence"></a>Recurrence

![](../media/cron-cron-manager-04.png)

### <a id="editfields-parameters"></a>Parameters

![](../media/cron-cron-manager-05.png)

This will vary between events.

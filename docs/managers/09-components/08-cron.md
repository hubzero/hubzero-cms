<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/components/cron
source-id: 3378
modified: 2016-08-24
imported: 2026-09-09
-->
# Cron

## Cron Jobs

A complete list of available Cron jobs that can be set up that work with other components and sections of a Hub.

**Cron- Activity**: Cron events for activity feed. Sends email digests at designated intervals to users containing their recent activity on the Hub.

**Cron – Cache Handler**: Cron events for Hub cache. Removes old system CSS files and trashes expired cache data at designated intervals.

**Cron – Courses**: Cron events for courses. Runs a specific job(s) at a set interval linked to the Courses component. Syncs passport badge statuses and can email instructors with a digested update at designated intervals.

**Cron – Forum**: Cron events for forums. Runs a specific job(s) at a set interval linked to the Forum component. Sends group forum email digests at designated intervals.

**Cron – Groups**: Cron events for groups. Runs a specific job(s) at a set interval linked to the Hub groups. Can remove group asset folders that were abandoned and send group announcements at intervals.

**Cron – Members**: Cron events for members. Runs a specific job(s) at a set interval linked to the Hub members. Can calculate point royalties.

**Cron – Newsletter**: Newsletter: Cron events for newsletter. Runs a specific job(s) at a set interval linked to the Newsletter component related to mailing out newsletters. Can process any newsletter mailings in the queue and process IP addresses to location data for newsletter email actions.

**Cron – Publications**: Cron events for publications. Runs a specific job(s) at a set interval linked to the Publications component. Set to email monthly publication stats to authors, compute unique user stats from text logs, issue master DOI and achieve publications (via mkAIP) when grace period expired – PURR ONLY.

**Cron – Resources**: Cron events for resources. Runs a specific job(s) at a set interval linked to the Resources component. Issues master DOI and audit resource data.

**Cron – Search**: Cron events for Search. Runs a specific job(s) at a set interval linked to the Search component. Set to process queues at set intervals.

**Cron – Support**: Cron events for support. Runs a specific job(s) at a set interval linked to the Support component that processes the support queue based on predetermined parameters. Has the ability to close tickets, email reminders for open tickets, email for tickets with specified parameters and remove temporary upload files and directories at set intervals.

## Manually Running a Cron Job

1. Log into the **/administrator** interface
2. Hover over the **Components** tab and from the drop-down select **Cron**
3. Search for the cron job and check the box beside the cron job's title
4. Select the **Run** icon on the top of the Cron main page
5. The Cron job will automatically run the predetermined job

## Deleting a Cron Job

1. Log into the **/administrator** interface
2. Hover over the **Components** tab and from the drop-down select **Cron**
3. Search for the cron job and check the box beside the cron job's title
4. Select the **Delete** icon on the top of the Cron main page
5. The Cron job will be deleted

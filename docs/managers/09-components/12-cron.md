<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/cron
source-id: 3378
modified: 2016-08-24
imported: 2026-09-09
-->
# Cron

The Cron component runs the hub's scheduled work. This chapter is a catalogue
of the jobs a hub can schedule and the settings each one takes.

How the scheduler works — the tick, the `muse cron:jobs` command, the job
list, the edit form, and the IP whitelist — is covered in
[Scheduled tasks](../03-maintenance/05-cron.md). Read that first. Nothing in
this chapter is a substitute for it, and in particular that chapter carries
the fact that makes this one matter: **a new hub has three jobs and only one
of them is published**, so every feature listed below is doing nothing until
somebody adds a job for it.

Use this chapter the other way round from most of the book. You do not read
it through. You arrive at it having noticed that something on the hub never
happens — the digest email nobody gets, the group membership that never
expires, the DOI that is never issued — find the event in the tables below,
check that its plugin is enabled, and then go to
[Scheduled tasks](../03-maintenance/05-cron.md) to create the job.

> **Note:** Adding a job is reversible and cheap: unpublish it and it stops.
> The button to be careful with is **Run**, which fires the selected job
> immediately — on any of the mail-sending events below, that means real
> messages to real addresses the moment you press it.

## Where the jobs come from

The component owns no work of its own. Every job is a plugin event.

When you open a job's edit form, the component fires `cron.onCronEvents` and
each enabled plugin in `core/plugins/cron/` answers with the events it
offers. Those answers fill the **Event** drop-down, grouped by plugin. A
plugin that is disabled contributes nothing, so its events disappear from the
drop-down — and any job already pointing at one of them stops running.

Each event may also declare a group of parameters. Those come from a
`<params group="...">` block in the plugin's manifest, and the edit form
swaps the matching block in when you choose the event. An event with no such
block shows *No parameters found*.

> **Note:** Do not confuse a job's parameters with the plugin's own settings.
> The plugin's settings live under **Extensions → Plugins** and apply to every
> job that uses it; the job's parameters live on the job.

The component's submenu has two entries: **Jobs**, and a **Plugins** link that
jumps straight to the cron plugin group in the Plugin Manager. The second
appears only for someone who can manage plugins.

The [cron events reference](../../reference/events/cron.md) lists every event
with the plugins that listen for it.

## The jobs

Fourteen cron plugins ship with the hub. The **Event** column gives the name
as it appears in the drop-down; the identifier in parentheses is what is
stored on the job.

### Cron - Activity

| Event | What it does | Parameters |
|---|---|---|
| Members Email Digest (`emailMemberDigest`) | Mails each member a digest of recent activity on the hub | none |

The plugin's own **Email Transport** setting chooses how digests are
delivered.

### Cron - Cache Handler

| Event | What it does | Parameters |
|---|---|---|
| Remove old system CSS files (`cleanSystemCss`) | Deletes stale generated CSS from the cache directory | none |
| Trash expired cache data (`trashExpiredData`) | Runs cache garbage collection | none |

### Cron - Courses

| Event | What it does | Parameters |
|---|---|---|
| Sync passport badge statuses (`syncPassportBadgeStatus`) | Reconciles badge state with the badge provider | none |
| Email instructor digest (`emailInstructorDigest`) | Mails course instructors a digest | **Select course to receive emails** — a course, or *All* |

### Cron - Forum

**Ships disabled.** Its event is not on the **Event** menu until you enable
the plugin at **Extensions → Plugins**. It is the only cron plugin in this
state; the other thirteen are enabled on a new hub.

| Event | What it does | Parameters |
|---|---|---|
| Group forum email digest (`emailGroupForumDigest`) | Mails group members a digest of new forum posts | none |

### Cron - Groups

| Event | What it does | Parameters |
|---|---|---|
| Remove group asset folders that were abandoned. (`cleanGroupFolders`) | Deletes files left behind by deleted groups | none |
| Send Group Announcements (`sendGroupAnnouncements`) | Mails pending group announcements | none |
| Revoke group memberships whose end date has passed (`expireGroupMemberships`) | Removes members past their membership end date | none |
| Warn members whose group membership is about to end (`notifyExpiringMemberships`) | Mails a warning before a membership expires | none |

### Cron - Members

| Event | What it does | Parameters |
|---|---|---|
| Calculate point royalties (`onPointRoyalties`) | Distributes point royalties on answers, reviews and resources | none |

### Cron - Newsletter

| Event | What it does | Parameters |
|---|---|---|
| Process any newsletter mailings in the queue. (`processMailings`) | Sends queued newsletter mail | **Queued Emails Limit:** — how many to send per run. Default 25 |
| Process IP addresses to location data for newsletter email actions. (`processIps`) | Resolves recorded IP addresses to locations | **IPs Limit:** — how many to resolve per run. Default 100 |

### Cron - Projects

| Event | What it does | Parameters |
|---|---|---|
| Compute and log overall projects usage stats (`computeStats`) | Rolls up project usage figures | none |
| Auto sync project repositories connected with GDrive (`googleSync`) | Syncs projects connected to Google Drive | none |
| Run git gc for project repos to optimize disk usage (`gitGc`) | Garbage-collects project Git repositories | none |

### Cron - Publications

| Event | What it does | Parameters |
|---|---|---|
| Email monthly publication stats to authors (`sendAuthorStats`) | Mails authors their monthly figures | **List of user ids of authors** — a comma-separated allow-list; empty means everyone |
| Compute unique user stats from text logs (`rollUserStats`) | Rolls raw log lines into unique-user counts | none |
| Archive publications (via mkAIP) when grace period expired (`runMkAip`) | Hands expired publications to the archive tool | none |
| Issue master DOI (`issueMasterDoi`) | Registers the DOI for publications that lack one | none |
| Update FTP file links (`updateFtpLinks`) | Refreshes links to files served over FTP | **Start Date** — how far back to look. Defaults to yesterday |
| Build publication download bundles (async) (`buildPublicationBundles`) | Pre-builds the zip bundles a publication offers | none |

`runMkAip` does nothing unless `com_publications` has an archive script
configured and a non-zero grace period. With no grace period, publications are
archived on approval and the job is unnecessary.

### Cron - Resources

| Event | What it does | Parameters |
|---|---|---|
| Issue master DOI (`issueResourceMasterDoi`) | Registers the DOI for resources that lack one | none |
| Audit resource data (`auditResourceData`) | Checks resource records for problems | **Batch number** (default 500) and **How often to recalculate** (default *Once a month*) |
| Email member latest releveant entries (`emailMemberResources`) | Mails members newly published resources matching their interests | **Batch number** (300), **Items to display** (3), **How often to recalculate** (*Once a week*), **Email subject** |
| Calculate Ranking (`updateResourceRanking`) | Recalculates resource ranking scores | **Batch number** (100) and **How often to recalculate** (*Once a week*) |

### Cron - Search

Both events matter only on a hub whose **Engine** is **Apache Solr**. On a
Basic hub there is no index, and neither event does anything.

| Event | What it does | Parameters |
|---|---|---|
| Process Queue (`processQueue`) | Nothing. See the warning below | none |
| Run Full Index (`runFullIndex`) | Re-indexes every component in the **Indexed** state, in batches | none |

**Run Full Index** is the one worth scheduling, and only as a safety net:
indexing on a Solr hub is immediate, so a healthy hub does not need it.
Nightly or weekly is right; anything more frequent will not finish before it
starts again.

> **Warning:** **Process Queue** does nothing on any hub. It reads
> `#__search_queue`, and nothing in this tree ever writes to that table, so
> the event finds an empty queue and returns. It is the last live piece of a
> retired design in which saves were queued and indexed later. Scheduling it
> is harmless and pointless. See
> [Maintaining the index](31-search/05-index.md).

### Cron - Storefront

| Event | What it does | Parameters |
|---|---|---|
| Email SKUs and Products Publish Down Notifications (`emailPublishDownNotifications`) | Warns before a product or SKU unpublishes | **Publish Down Notification 1** and **2** — days before the publish-down date |

### Cron - Support

| Event | What it does | Parameters |
|---|---|---|
| Close tickets (`onClosePending`) | Closes tickets matching a filter, with a closing message | A large filter: up to three statuses, severity, last activity, group, assignment, owners, submitters, who to notify, tags to include or exclude, and the closing message |
| Email reminder for open tickets (`sendTicketsReminder`) | Reminds owners about open tickets | **Tickets with severity**, **For users in group** |
| Email for tickets with specified params (`sendTicketList`) | Mails a list of tickets matching a filter | The same filter as *Close tickets*, plus **Open/Closed** and **Created** |
| Remove temporary upload files and directories (`cleanTempUploads`) | Deletes abandoned ticket attachments | **Older than** — 1 day to 1 year |

### Cron - Users

| Event | What it does | Parameters |
|---|---|---|
| Remove temporary acounts that have been created and abandoned during the third party authentication process. (`cleanAuthTempAccounts`) | Deletes half-finished third-party sign-ups | none |

> **Note:** Several labels above are misspelled in the shipped language files
> — *releveant*, *acounts*. They are quoted as they appear on screen.

## Running and deleting jobs

The job list's toolbar carries **Run**, **Publish**, **Unpublish**,
**Deactivate**, **New**, **Delete** and **Options**.

To run a job immediately, tick it and press **Run**. The job executes in that
request, and its next run time is recalculated from its recurrence. Because
jobs claim themselves before running, a manual run cannot collide with a
scheduled tick.

To remove a job, tick it and press **Delete**. Deleting is permanent; there is
no trash for cron jobs. Unpublishing a job stops it running while keeping the
record, which is usually what you want.

> **Warning:** Deleting a job deletes its parameters with it. If a job carries
> a filter you spent time tuning — the Support ticket jobs especially —
> unpublish it rather than delete it.

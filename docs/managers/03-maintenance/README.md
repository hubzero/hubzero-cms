<!--
status: rewritten
reviewed-against: 2.4-main @ 123ea53b14
reviewed: 2026-09-09
screenshots: stale
source: https://help.hubzero.org/documentation/240/managers/maintenance
source-id: 3339
imported: 2026-09-09
-->
# Daily Maintenance

The work a hub needs every day: approving what users submit, moving tools
through the publishing pipeline, answering support tickets, posting site
notices, and keeping the scheduled jobs running.

## The dashboard

The first screen after you log in to `/administrator` is the **Control Panel**,
served by `com_cpanel`. It has no content of its own. It renders every
administrator module published in the **cpanel** position, one collapsible
panel per module:

![The administrative dashboard, showing one collapsible panel per module published in the cpanel position](../media/maintenance-dashboard.png)

<!--include: core/components/com_cpanel/admin/views/cpanel/tmpl/default.php:15-40-->

So what the dashboard shows depends entirely on which modules the hub
publishes there. A fresh install ships only two panels — **Popular Articles**
and **Recently Added Articles**. The panels a working hub usually adds are:

| Module | What its panel shows |
|---|---|
| `mod_resources` | Resources by state: published, unpublished, pending, draft, removed |
| `mod_tools` | Tool contributions by pipeline state |
| `mod_supporttickets` | Open, new, and unassigned support tickets |
| `mod_supportactivity` | Recent support activity |
| `mod_answers` | Questions asked and closed |
| `mod_wishlist` | Wishes pending, accepted, granted, rejected, withdrawn, removed |
| `mod_groups` | Groups by type and groups pending approval |
| `mod_members` | New, confirmed, and approved members |
| `mod_users` | Pending new users awaiting approval |
| `mod_courses` | Course activity needing attention |

Each count links to the matching list in the component, so the dashboard is a
set of jump-off points rather than a report.

To add or remove a panel, go to **Extensions → Module Manager**, set the
**Client** filter to **Administrator**, then edit the module and set its
**Position** to `cpanel`. The position and type filters only list what the
selected client offers, so administrator modules are invisible while the
filter is on **Site**.

> **Note:** `mod_tools` counts tools in the **Installed** state but has no
> panel cell for it, so that count is never displayed. Its links also use a
> `status` query parameter where the pipeline screen reads `state`, so
> clicking a count opens the full list unfiltered. Both are recorded in
> a record kept with the project.

## In this section

- [Approving Content](01-approvingcontent.md)
- [Tools](02-tools.md)
- [Support Tickets](03-tickets.md)
- [Site Notices](04-notices.md)
- [Scheduled Tasks](05-cron.md)

<!--
status: generated
source: Event::trigger('cron.*') call sites and core/plugins/cron/
-->

# Cron events

Events in the `cron` group. A plugin in `core/plugins/cron/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `cron.onClosePending`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_cron_support` — [`onClosePending(\Components\Cron\Models\Job $job)`](../../../core/plugins/cron/support/support.php)

## `cron.onCronEvents`

Fired from:

- [`core/components/com_cron/admin/controllers/jobs.php:145`](../../../core/components/com_cron/admin/controllers/jobs.php#L145)

Listeners:

- `plg_cron_activity` — [`onCronEvents()`](../../../core/plugins/cron/activity/activity.php)
- `plg_cron_cache` — [`onCronEvents()`](../../../core/plugins/cron/cache/cache.php)
- `plg_cron_courses` — [`onCronEvents()`](../../../core/plugins/cron/courses/courses.php)
- `plg_cron_forum` — [`onCronEvents()`](../../../core/plugins/cron/forum/forum.php)
- `plg_cron_groups` — [`onCronEvents()`](../../../core/plugins/cron/groups/groups.php)
- `plg_cron_members` — [`onCronEvents()`](../../../core/plugins/cron/members/members.php)
- `plg_cron_newsletter` — [`onCronEvents()`](../../../core/plugins/cron/newsletter/newsletter.php)
- `plg_cron_projects` — [`onCronEvents()`](../../../core/plugins/cron/projects/projects.php)
- `plg_cron_publications` — [`onCronEvents()`](../../../core/plugins/cron/publications/publications.php)
- `plg_cron_resources` — [`onCronEvents()`](../../../core/plugins/cron/resources/resources.php)
- `plg_cron_search` — [`onCronEvents()`](../../../core/plugins/cron/search/search.php)
- `plg_cron_storefront` — [`onCronEvents()`](../../../core/plugins/cron/storefront/storefront.php)
- `plg_cron_support` — [`onCronEvents()`](../../../core/plugins/cron/support/support.php)
- `plg_cron_users` — [`onCronEvents()`](../../../core/plugins/cron/users/users.php)

## `cron.onPointRoyalties`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_cron_members` — [`onPointRoyalties(\Components\Cron\Models\Job $job)`](../../../core/plugins/cron/members/members.php)

<!--
status: generated
source: Event::trigger('newsletter.*') call sites and core/plugins/newsletter/
-->

# Newsletter events

Events in the `newsletter` group. A plugin in `core/plugins/newsletter/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `newsletter.onGetEnabledDigests`

Fired from:

- [`core/components/com_newsletter/admin/controllers/stories.php:148`](../../../core/components/com_newsletter/admin/controllers/stories.php#L148)
- [`core/components/com_newsletter/models/newsletter.php:451`](../../../core/components/com_newsletter/models/newsletter.php#L451)

Listeners:

- `plg_newsletter_event` — [`onGetEnabledDigests()`](../../../core/plugins/newsletter/event/event.php)
- `plg_newsletter_jobs` — [`onGetEnabledDigests()`](../../../core/plugins/newsletter/jobs/jobs.php)
- `plg_newsletter_resource` — [`onGetEnabledDigests()`](../../../core/plugins/newsletter/resource/resource.php)

## `newsletter.onGetLatest`

Fired from:

- [`core/components/com_newsletter/admin/controllers/stories.php:156`](../../../core/components/com_newsletter/admin/controllers/stories.php#L156) with `[$itemCount]`
- [`core/components/com_newsletter/models/newsletter.php:457`](../../../core/components/com_newsletter/models/newsletter.php#L457) with `[$parts[2]))[$key]; // Apply the view template $view = new \Hubzero\Component\View(array(]`

Listeners:

- `plg_newsletter_event` — [`onGetLatest($num = 5, $dateField = 'created', $sort = 'DESC')`](../../../core/plugins/newsletter/event/event.php)
- `plg_newsletter_jobs` — [`onGetLatest($num = 5, $dateField = 'created', $sort = 'DESC')`](../../../core/plugins/newsletter/jobs/jobs.php)
- `plg_newsletter_resource` — [`onGetLatest($num = 5, $dateField = 'created', $sort = 'DESC')`](../../../core/plugins/newsletter/resource/resource.php)

<!--
status: generated
source: Event::trigger('newsletter.*') call sites and core/plugins/newsletter/
-->

# Newsletter events

Events in the `newsletter` group. A plugin in `core/plugins/newsletter/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `newsletter.onGetEnabledDigests`

Fired from:

- [`core/components/com_newsletter/admin/controllers/stories.php:102`](../../../core/components/com_newsletter/admin/controllers/stories.php#L102)
- [`core/components/com_newsletter/admin/controllers/stories.php:138`](../../../core/components/com_newsletter/admin/controllers/stories.php#L138)
- [`core/components/com_newsletter/models/newsletter.php:444`](../../../core/components/com_newsletter/models/newsletter.php#L444)

Listeners:

- `plg_newsletter_event` — [`onGetEnabledDigests()`](../../../core/plugins/newsletter/event/event.php)
- `plg_newsletter_resource` — [`onGetEnabledDigests()`](../../../core/plugins/newsletter/resource/resource.php)

## `newsletter.onGetLatest`

Fired from:

- [`core/components/com_newsletter/admin/controllers/stories.php:146`](../../../core/components/com_newsletter/admin/controllers/stories.php#L146) with `[$itemCount]`
- [`core/components/com_newsletter/models/newsletter.php:450`](../../../core/components/com_newsletter/models/newsletter.php#L450) with `[$parts[2]]`

Listeners:

- `plg_newsletter_event` — [`onGetLatest($num = 5, $dateField = 'created', $sort = 'DESC')`](../../../core/plugins/newsletter/event/event.php)
- `plg_newsletter_resource` — [`onGetLatest($num = 5, $dateField = 'created', $sort = 'DESC')`](../../../core/plugins/newsletter/resource/resource.php)

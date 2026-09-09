<!--
status: generated
source: Event::trigger('update.*') call sites and core/plugins/update/
-->

# Update events

Events in the `update` group. A plugin in `core/plugins/update/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `update.onAfterRepositoryUpdate`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_update_cache` — [`onAfterRepositoryUpdate()`](../../../core/plugins/update/cache/cache.php)
- `plg_update_support` — [`onAfterRepositoryUpdate()`](../../../core/plugins/update/support/support.php)

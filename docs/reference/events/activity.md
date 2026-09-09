<!--
status: generated
source: Event::trigger('activity.*') call sites and core/plugins/activity/
-->

# Activity events

Events in the `activity` group. A plugin in `core/plugins/activity/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `activity.onLogDelete`

Fired from:

- [`core/libraries/Hubzero/Activity/Log.php:180`](../../../core/libraries/Hubzero/Activity/Log.php#L180) with `[$this]`

No plugin in the source tree listens for this event.

## `activity.onLogSave`

Fired from:

- [`core/libraries/Hubzero/Activity/Log.php:209`](../../../core/libraries/Hubzero/Activity/Log.php#L209) with `[$this, $isNew]`

No plugin in the source tree listens for this event.

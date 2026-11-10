<!--
status: generated
source: Event::trigger('mw.*') call sites and core/plugins/mw/
-->

# Mw events

Events in the `mw` group. A plugin in `core/plugins/mw/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `mw.onAfterSessionInvoke`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1r0.php:696`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L696) with `[$app->toolname, $app->version]`
- [`core/components/com_tools/site/controllers/sessions.php:708`](../../../core/components/com_tools/site/controllers/sessions.php#L708) with `[$app->toolname, $app->version]`

No plugin in the source tree listens for this event.

## `mw.onAfterSessionStart`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1r0.php:1194`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1194) with `[$toolname, $tv->revision]`
- [`core/components/com_tools/site/controllers/sessions.php:1355`](../../../core/components/com_tools/site/controllers/sessions.php#L1355) with `[$toolname, $tv->revision]`

No plugin in the source tree listens for this event.

## `mw.onAfterSessionStop`

Fired from:

- [`core/components/com_tools/admin/controllers/sessions.php:164`](../../../core/components/com_tools/admin/controllers/sessions.php#L164) with `[$row->appname]`
- [`core/components/com_tools/api/controllers/sessionsv1r0.php:1500`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1500) with `[$ms->get('appname')]`
- [`core/components/com_tools/site/controllers/sessions.php:1519`](../../../core/components/com_tools/site/controllers/sessions.php#L1519) with `[$ms->appname]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionInvoke`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1r0.php:670`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L670) with `[$app->toolname, $app->version]`
- [`core/components/com_tools/site/controllers/sessions.php:664`](../../../core/components/com_tools/site/controllers/sessions.php#L664) with `[$app->toolname, $app->version]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionStart`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1r0.php:1176`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1176) with `[$toolname, $tv->revision]`
- [`core/components/com_tools/site/controllers/sessions.php:1183`](../../../core/components/com_tools/site/controllers/sessions.php#L1183) with `[$toolname, $tv->revision]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionStop`

Fired from:

- [`core/components/com_tools/admin/controllers/sessions.php:149`](../../../core/components/com_tools/admin/controllers/sessions.php#L149) with `[$row->appname]`
- [`core/components/com_tools/api/controllers/sessionsv1r0.php:1494`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1494) with `[$ms->get('appname')]`
- [`core/components/com_tools/site/controllers/sessions.php:1506`](../../../core/components/com_tools/site/controllers/sessions.php#L1506) with `[$ms->appname]`

No plugin in the source tree listens for this event.

## `mw.onSessionView`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:453`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L453) with `[ $this->option, $this->toolname, $this->app->sess, ],`

No plugin in the source tree listens for this event.

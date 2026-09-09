<!--
status: generated
source: Event::trigger('mw.*') call sites and core/plugins/mw/
-->

# Mw events

Events in the `mw` group. A plugin in `core/plugins/mw/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `mw.onAfterSessionInvoke`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:750`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L750) with `[$app->toolname, $app->version]`
- [`core/components/com_tools/site/controllers/sessions.php:670`](../../../core/components/com_tools/site/controllers/sessions.php#L670) with `[$app->toolname, $app->version]`

No plugin in the source tree listens for this event.

## `mw.onAfterSessionStart`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1288`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1288) with `[$toolname, $tv->revision]`
- [`core/components/com_tools/site/controllers/sessions.php:1387`](../../../core/components/com_tools/site/controllers/sessions.php#L1387) with `[$toolname, $tv->revision]`

No plugin in the source tree listens for this event.

## `mw.onAfterSessionStop`

Fired from:

- [`core/components/com_tools/admin/controllers/sessions.php:176`](../../../core/components/com_tools/admin/controllers/sessions.php#L176) with `[$row->appname]`
- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1355`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1355) with `[$ms->get('appname')]`
- [`core/components/com_tools/site/controllers/sessions.php:1528`](../../../core/components/com_tools/site/controllers/sessions.php#L1528) with `[$ms->appname]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionInvoke`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:729`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L729) with `[$app->toolname, $app->version]`
- [`core/components/com_tools/site/controllers/sessions.php:621`](../../../core/components/com_tools/site/controllers/sessions.php#L621) with `[$app->toolname, $app->version]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionStart`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1274`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1274) with `[$toolname, $tv->revision]`
- [`core/components/com_tools/site/controllers/sessions.php:1189`](../../../core/components/com_tools/site/controllers/sessions.php#L1189) with `[$toolname, $tv->revision]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionStop`

Fired from:

- [`core/components/com_tools/admin/controllers/sessions.php:159`](../../../core/components/com_tools/admin/controllers/sessions.php#L159) with `[$row->appname]`
- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1349`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1349) with `[$ms->get('appname')]`
- [`core/components/com_tools/site/controllers/sessions.php:1513`](../../../core/components/com_tools/site/controllers/sessions.php#L1513) with `[$ms->appname]`

No plugin in the source tree listens for this event.

## `mw.onSessionView`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:381`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L381) with `[ $this->option, $this->toolname, $this->app->sess ]`

No plugin in the source tree listens for this event.

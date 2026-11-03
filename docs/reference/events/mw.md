<!--
status: generated
source: Event::trigger('mw.*') call sites and core/plugins/mw/
-->

# Mw events

Events in the `mw` group. A plugin in `core/plugins/mw/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `mw.onAfterSessionInvoke`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:773`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L773) with `[$app->toolname, $app->version]`
- [`core/components/com_tools/site/controllers/sessions.php:798`](../../../core/components/com_tools/site/controllers/sessions.php#L798) with `[$app->toolname, $app->version]`

No plugin in the source tree listens for this event.

## `mw.onAfterSessionStart`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1316`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1316) with `[$toolname, $tv->revision]`
- [`core/components/com_tools/site/controllers/sessions.php:1531`](../../../core/components/com_tools/site/controllers/sessions.php#L1531) with `[$toolname, $tv->revision]`

No plugin in the source tree listens for this event.

## `mw.onAfterSessionStop`

Fired from:

- [`core/components/com_tools/admin/controllers/sessions.php:176`](../../../core/components/com_tools/admin/controllers/sessions.php#L176) with `[$row->appname]`
- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1653`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1653) with `[$ms->get('appname')]`
- [`core/components/com_tools/site/controllers/sessions.php:1709`](../../../core/components/com_tools/site/controllers/sessions.php#L1709) with `[$ms->appname]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionInvoke`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:746`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L746) with `[$app->toolname, $app->version]`
- [`core/components/com_tools/site/controllers/sessions.php:749`](../../../core/components/com_tools/site/controllers/sessions.php#L749) with `[$app->toolname, $app->version]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionStart`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1302`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1302) with `[$toolname, $tv->revision]`
- [`core/components/com_tools/site/controllers/sessions.php:1321`](../../../core/components/com_tools/site/controllers/sessions.php#L1321) with `[$toolname, $tv->revision]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionStop`

Fired from:

- [`core/components/com_tools/admin/controllers/sessions.php:159`](../../../core/components/com_tools/admin/controllers/sessions.php#L159) with `[$row->appname]`
- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1647`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1647) with `[$ms->get('appname')]`
- [`core/components/com_tools/site/controllers/sessions.php:1694`](../../../core/components/com_tools/site/controllers/sessions.php#L1694) with `[$ms->appname]`

No plugin in the source tree listens for this event.

## `mw.onSessionView`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:453`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L453) with `[ $this->option, $this->toolname, $this->app->sess ]`

No plugin in the source tree listens for this event.

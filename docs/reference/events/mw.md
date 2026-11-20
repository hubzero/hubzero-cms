<!--
status: generated
source: Event::trigger('mw.*') call sites and core/plugins/mw/
-->

# Mw events

Events in the `mw` group. A plugin in `core/plugins/mw/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `mw.onAfterSessionInvoke`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1r0.php:735`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L735) with `[$app->toolname, $app->version]`
- [`core/components/com_tools/site/controllers/sessions.php:726`](../../../core/components/com_tools/site/controllers/sessions.php#L726) with `[$app->toolname, $app->version]`

No plugin in the source tree listens for this event.

## `mw.onAfterSessionStart`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1r0.php:1254`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1254) with `[$toolname, $tv->revision]`
- [`core/components/com_tools/site/controllers/sessions.php:1430`](../../../core/components/com_tools/site/controllers/sessions.php#L1430) with `[$toolname, $tv->revision]`

No plugin in the source tree listens for this event.

## `mw.onAfterSessionStop`

Fired from:

- [`core/components/com_tools/admin/controllers/sessions.php:167`](../../../core/components/com_tools/admin/controllers/sessions.php#L167) with `[$row->appname]`
- [`core/components/com_tools/api/controllers/sessionsv1r0.php:1568`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1568) with `[$ms->get('appname')]`
- [`core/components/com_tools/site/controllers/sessions.php:1598`](../../../core/components/com_tools/site/controllers/sessions.php#L1598) with `[$ms->appname]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionInvoke`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1r0.php:708`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L708) with `[$app->toolname, $app->version]`
- [`core/components/com_tools/site/controllers/sessions.php:681`](../../../core/components/com_tools/site/controllers/sessions.php#L681) with `[$app->toolname, $app->version]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionStart`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1r0.php:1235`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1235) with `[$toolname, $tv->revision]`
- [`core/components/com_tools/site/controllers/sessions.php:1251`](../../../core/components/com_tools/site/controllers/sessions.php#L1251) with `[$toolname, $tv->revision]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionStop`

Fired from:

- [`core/components/com_tools/admin/controllers/sessions.php:152`](../../../core/components/com_tools/admin/controllers/sessions.php#L152) with `[$row->appname]`
- [`core/components/com_tools/api/controllers/sessionsv1r0.php:1561`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1561) with `[$ms->get('appname')]`
- [`core/components/com_tools/site/controllers/sessions.php:1584`](../../../core/components/com_tools/site/controllers/sessions.php#L1584) with `[$ms->appname]`

No plugin in the source tree listens for this event.

## `mw.onSessionView`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:602`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L602) with `[ $this->option, $this->toolname, $this->app->sess, ]`

No plugin in the source tree listens for this event.

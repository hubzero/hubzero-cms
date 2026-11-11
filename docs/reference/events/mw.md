<!--
status: generated
source: Event::trigger('mw.*') call sites and core/plugins/mw/
-->

# Mw events

Events in the `mw` group. A plugin in `core/plugins/mw/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `mw.onAfterSessionInvoke`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1r0.php:725`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L725) with `[$app->toolname, $app->version]`
- [`core/components/com_tools/site/controllers/sessions.php:723`](../../../core/components/com_tools/site/controllers/sessions.php#L723) with `[$app->toolname, $app->version]`

No plugin in the source tree listens for this event.

## `mw.onAfterSessionStart`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1r0.php:1243`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1243) with `[$toolname, $tv->revision]`
- [`core/components/com_tools/site/controllers/sessions.php:1439`](../../../core/components/com_tools/site/controllers/sessions.php#L1439) with `[$toolname, $tv->revision]`

No plugin in the source tree listens for this event.

## `mw.onAfterSessionStop`

Fired from:

- [`core/components/com_tools/admin/controllers/sessions.php:166`](../../../core/components/com_tools/admin/controllers/sessions.php#L166) with `[$row->appname]`
- [`core/components/com_tools/api/controllers/sessionsv1r0.php:1556`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1556) with `[$ms->get('appname')]`
- [`core/components/com_tools/site/controllers/sessions.php:1610`](../../../core/components/com_tools/site/controllers/sessions.php#L1610) with `[$ms->appname]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionInvoke`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1r0.php:699`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L699) with `[$app->toolname, $app->version]`
- [`core/components/com_tools/site/controllers/sessions.php:677`](../../../core/components/com_tools/site/controllers/sessions.php#L677) with `[$app->toolname, $app->version]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionStart`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1r0.php:1225`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1225) with `[$toolname, $tv->revision]`
- [`core/components/com_tools/site/controllers/sessions.php:1259`](../../../core/components/com_tools/site/controllers/sessions.php#L1259) with `[$toolname, $tv->revision]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionStop`

Fired from:

- [`core/components/com_tools/admin/controllers/sessions.php:151`](../../../core/components/com_tools/admin/controllers/sessions.php#L151) with `[$row->appname]`
- [`core/components/com_tools/api/controllers/sessionsv1r0.php:1550`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1550) with `[$ms->get('appname')]`
- [`core/components/com_tools/site/controllers/sessions.php:1597`](../../../core/components/com_tools/site/controllers/sessions.php#L1597) with `[$ms->appname]`

No plugin in the source tree listens for this event.

## `mw.onSessionView`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:581`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L581) with `[ $this->option, $this->toolname, $this->app->sess, ]`

No plugin in the source tree listens for this event.

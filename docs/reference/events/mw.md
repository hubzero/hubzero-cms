<!--
status: generated
source: Event::trigger('mw.*') call sites and core/plugins/mw/
-->

# Mw events

Events in the `mw` group. A plugin in `core/plugins/mw/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `mw.onAfterSessionInvoke`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:762`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L762) with `[$app->toolname, $app->version]`
- [`core/components/com_tools/site/controllers/sessions.php:674`](../../../core/components/com_tools/site/controllers/sessions.php#L674) with `[$app->toolname, $app->version]`

No plugin in the source tree listens for this event.

## `mw.onAfterSessionStart`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1302`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1302) with `[$toolname, $tv->revision]`
- [`core/components/com_tools/site/controllers/sessions.php:1391`](../../../core/components/com_tools/site/controllers/sessions.php#L1391) with `[$toolname, $tv->revision]`

No plugin in the source tree listens for this event.

## `mw.onAfterSessionStop`

Fired from:

- [`core/components/com_tools/admin/controllers/sessions.php:176`](../../../core/components/com_tools/admin/controllers/sessions.php#L176) with `[$row->appname]`
- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1369`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1369) with `[$ms->get('appname')]`
- [`core/components/com_tools/site/controllers/sessions.php:1538`](../../../core/components/com_tools/site/controllers/sessions.php#L1538) with `[$ms->appname]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionInvoke`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:741`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L741) with `[$app->toolname, $app->version]`
- [`core/components/com_tools/site/controllers/sessions.php:625`](../../../core/components/com_tools/site/controllers/sessions.php#L625) with `[$app->toolname, $app->version]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionStart`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1288`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1288) with `[$toolname, $tv->revision]`
- [`core/components/com_tools/site/controllers/sessions.php:1193`](../../../core/components/com_tools/site/controllers/sessions.php#L1193) with `[$toolname, $tv->revision]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionStop`

Fired from:

- [`core/components/com_tools/admin/controllers/sessions.php:159`](../../../core/components/com_tools/admin/controllers/sessions.php#L159) with `[$row->appname]`
- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1363`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1363) with `[$ms->get('appname')]`
- [`core/components/com_tools/site/controllers/sessions.php:1523`](../../../core/components/com_tools/site/controllers/sessions.php#L1523) with `[$ms->appname]`

No plugin in the source tree listens for this event.

## `mw.onSessionView`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:381`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L381) with `[ $this->option, $this->toolname, $this->app->sess ]`

No plugin in the source tree listens for this event.

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

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1306`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1306) with `[$toolname, $tv->revision]`
- [`core/components/com_tools/site/controllers/sessions.php:1392`](../../../core/components/com_tools/site/controllers/sessions.php#L1392) with `[$toolname, $tv->revision]`

No plugin in the source tree listens for this event.

## `mw.onAfterSessionStop`

Fired from:

- [`core/components/com_tools/admin/controllers/sessions.php:176`](../../../core/components/com_tools/admin/controllers/sessions.php#L176) with `[$row->appname]`
- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1373`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1373) with `[$ms->get('appname')]`
- [`core/components/com_tools/site/controllers/sessions.php:1539`](../../../core/components/com_tools/site/controllers/sessions.php#L1539) with `[$ms->appname]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionInvoke`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:741`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L741) with `[$app->toolname, $app->version]`
- [`core/components/com_tools/site/controllers/sessions.php:625`](../../../core/components/com_tools/site/controllers/sessions.php#L625) with `[$app->toolname, $app->version]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionStart`

Fired from:

- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1292`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1292) with `[$toolname, $tv->revision]`
- [`core/components/com_tools/site/controllers/sessions.php:1194`](../../../core/components/com_tools/site/controllers/sessions.php#L1194) with `[$toolname, $tv->revision]`

No plugin in the source tree listens for this event.

## `mw.onBeforeSessionStop`

Fired from:

- [`core/components/com_tools/admin/controllers/sessions.php:159`](../../../core/components/com_tools/admin/controllers/sessions.php#L159) with `[$row->appname]`
- [`core/components/com_tools/api/controllers/sessionsv1_0.php:1367`](../../../core/components/com_tools/api/controllers/sessionsv1_0.php#L1367) with `[$ms->get('appname')]`
- [`core/components/com_tools/site/controllers/sessions.php:1524`](../../../core/components/com_tools/site/controllers/sessions.php#L1524) with `[$ms->appname]`

No plugin in the source tree listens for this event.

## `mw.onSessionView`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:381`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L381) with `[ $this->option, $this->toolname, $this->app->sess ]`

No plugin in the source tree listens for this event.

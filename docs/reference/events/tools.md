<!--
status: generated
source: Event::trigger('tools.*') call sites and core/plugins/tools/
-->

# Tools events

Events in the `tools` group. A plugin in `core/plugins/tools/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `tools.onToolSessionIdentify`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:49`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L49)

Listeners:

- `plg_tools_novnc` — [`onToolSessionIdentify()`](../../../core/plugins/tools/novnc/novnc.php)

## `tools.onToolSessionView`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:48`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L48) with `[$this->app, $this->output, $readOnly]`

Listeners:

- `plg_tools_novnc` — [`onToolSessionView($tool, $session, $readOnly=false)`](../../../core/plugins/tools/novnc/novnc.php)

## `tools.onToolSessionViewAfter`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:227`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L227) with `[$this->app, $this->output, $readOnly)]`

No plugin in the source tree listens for this event.

## `tools.onToolSessionViewBefore`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:67`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L67) with `[$this->app, $this->output, $readOnly)]`

No plugin in the source tree listens for this event.

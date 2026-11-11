<!--
status: generated
source: Event::trigger('tools.*') call sites and core/plugins/tools/
-->

# Tools events

Events in the `tools` group. A plugin in `core/plugins/tools/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `tools.onToolSessionIdentify`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:70`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L70)

Listeners:

- `plg_tools_novnc` — [`onToolSessionIdentify()`](../../../core/plugins/tools/novnc/novnc.php)

## `tools.onToolSessionView`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:69`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L69) with `[$this->app, $this->output, $readOnly]`

Listeners:

- `plg_tools_novnc` — [`onToolSessionView($tool, $session, $readOnly = false)`](../../../core/plugins/tools/novnc/novnc.php)

## `tools.onToolSessionViewAfter`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:422`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L422) with `[$this->app, $this->output, $readOnly]`

No plugin in the source tree listens for this event.

## `tools.onToolSessionViewBefore`

Fired from:

- [`core/components/com_tools/site/views/sessions/tmpl/session.php:90`](../../../core/components/com_tools/site/views/sessions/tmpl/session.php#L90) with `[$this->app, $this->output, $readOnly]`

No plugin in the source tree listens for this event.

<!--
status: generated
source: Event::trigger('oaipmh.*') call sites and core/plugins/oaipmh/
-->

# Oaipmh events

Events in the `oaipmh` group. A plugin in `core/plugins/oaipmh/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `oaipmh.onOaipmhProvider`

Fired from:

- [`core/components/com_oaipmh/models/service.php:246`](../../../core/components/com_oaipmh/models/service.php#L246) with `[&$this]`

Listeners:

- `plg_oaipmh_publications` — [`onOaipmhProvider(&$service)`](../../../core/plugins/oaipmh/publications/publications.php)
- `plg_oaipmh_resources` — [`onOaipmhProvider(&$service)`](../../../core/plugins/oaipmh/resources/resources.php)

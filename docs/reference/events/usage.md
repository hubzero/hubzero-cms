<!--
status: generated
source: Event::trigger('usage.*') call sites and core/plugins/usage/
-->

# Usage events

Events in the `usage` group. A plugin in `core/plugins/usage/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `usage.onUsageAreas`

Fired from:

- [`core/components/com_usage/site/controllers/results.php:76`](../../../core/components/com_usage/site/controllers/results.php#L76)

Listeners:

- `plg_usage_domainclass` — [`onUsageAreas()`](../../../core/plugins/usage/domainclass/domainclass.php)
- `plg_usage_domains` — [`onUsageAreas()`](../../../core/plugins/usage/domains/domains.php)
- `plg_usage_maps` — [`onUsageAreas()`](../../../core/plugins/usage/maps/maps.php)
- `plg_usage_overview` — [`onUsageAreas()`](../../../core/plugins/usage/overview/overview.php)
- `plg_usage_partners` — [`onUsageAreas()`](../../../core/plugins/usage/partners/partners.php)
- `plg_usage_region` — [`onUsageAreas()`](../../../core/plugins/usage/region/region.php)
- `plg_usage_tools` — [`onUsageAreas()`](../../../core/plugins/usage/tools/tools.php)

## `usage.onUsageDisplay`

Fired from:

- [`core/components/com_usage/site/controllers/results.php:105`](../../../core/components/com_usage/site/controllers/results.php#L105) with `[ $this->_option, $this->_task, $udb, $months, $monthsReverse, $enddate ]`

Listeners:

- `plg_usage_domainclass` — [`onUsageDisplay($option, $task, $db, $months, $monthsReverse, $enddate)`](../../../core/plugins/usage/domainclass/domainclass.php)
- `plg_usage_domains` — [`onUsageDisplay($option, $task, $db, $months, $monthsReverse, $enddate)`](../../../core/plugins/usage/domains/domains.php)
- `plg_usage_maps` — [`onUsageDisplay($option, $task, $db, $months, $monthsReverse, $enddate)`](../../../core/plugins/usage/maps/maps.php)
- `plg_usage_overview` — [`onUsageDisplay($option, $task, $db, $months, $monthsReverse, $enddate)`](../../../core/plugins/usage/overview/overview.php)
- `plg_usage_partners` — [`onUsageDisplay($option, $task, $db, $months, $monthsReverse, $enddate)`](../../../core/plugins/usage/partners/partners.php)
- `plg_usage_region` — [`onUsageDisplay($option, $task, $db, $months, $monthsReverse, $enddate)`](../../../core/plugins/usage/region/region.php)
- `plg_usage_tools` — [`onUsageDisplay($option, $task, $db, $months, $monthsReverse, $enddate)`](../../../core/plugins/usage/tools/tools.php)

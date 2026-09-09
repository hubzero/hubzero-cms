<!--
status: generated
source: Event::trigger('whatsnew.*') call sites and core/plugins/whatsnew/
-->

# Whatsnew events

Events in the `whatsnew` group. A plugin in `core/plugins/whatsnew/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `whatsnew.onWhatsNewAreas`

Fired from:

- [`core/components/com_whatsnew/api/controllers/entriesv1_0.php:79`](../../../core/components/com_whatsnew/api/controllers/entriesv1_0.php#L79)
- [`core/components/com_whatsnew/helpers/finder.php:32`](../../../core/components/com_whatsnew/helpers/finder.php#L32)

No plugin in the source tree listens for this event.

## `whatsnew.onWhatsnew`

Fired from:

- [`core/components/com_whatsnew/api/controllers/entriesv1_0.php:105`](../../../core/components/com_whatsnew/api/controllers/entriesv1_0.php#L105) with `[ $p, 999, 0, $areas ]`
- [`core/components/com_whatsnew/site/controllers/results.php:121`](../../../core/components/com_whatsnew/site/controllers/results.php#L121) with `[ $p, 0, 0, $activeareas ]`
- [`core/components/com_whatsnew/site/controllers/results.php:134`](../../../core/components/com_whatsnew/site/controllers/results.php#L134) with `[ $p, $limit, $start, $activeareas ]`
- [`core/components/com_whatsnew/site/controllers/results.php:333`](../../../core/components/com_whatsnew/site/controllers/results.php#L333) with `[ $p, $limit, $start, $activeareas ]`
- [`core/modules/mod_whatsnew/helper.php:179`](../../../core/modules/mod_whatsnew/helper.php#L179) with `[ $p, $count, 0, $activeareas, array() ]`

Listeners:

- `plg_whatsnew_content` — [`onWhatsnew($period, $limit=0, $limitstart=0, $areas=null, $tagids=array()`](../../../core/plugins/whatsnew/content/content.php)
- `plg_whatsnew_events` — [`onWhatsnew($period, $limit=0, $limitstart=0, $areas=null, $tagids=array()`](../../../core/plugins/whatsnew/events/events.php)
- `plg_whatsnew_kb` — [`onWhatsnew($period, $limit=0, $limitstart=0, $areas=null, $tagids=array()`](../../../core/plugins/whatsnew/kb/kb.php)
- `plg_whatsnew_publications` — [`onWhatsnew($period, $limit=0, $limitstart=0, $areas=null, $tagids=array()`](../../../core/plugins/whatsnew/publications/publications.php)
- `plg_whatsnew_resources` — [`onWhatsnew($period, $limit=0, $limitstart=0, $areas=null, $tagids=array()`](../../../core/plugins/whatsnew/resources/resources.php)
- `plg_whatsnew_wiki` — [`onWhatsnew($period, $limit=0, $limitstart=0, $areas=null, $tagids=array()`](../../../core/plugins/whatsnew/wiki/wiki.php)

## `whatsnew.onWhatsnewAreas`

Fired from:

- [`core/components/com_whatsnew/site/controllers/results.php:480`](../../../core/components/com_whatsnew/site/controllers/results.php#L480)
- [`core/modules/mod_whatsnew/helper.php:38`](../../../core/modules/mod_whatsnew/helper.php#L38)

Listeners:

- `plg_whatsnew_content` — [`onWhatsnewAreas()`](../../../core/plugins/whatsnew/content/content.php)
- `plg_whatsnew_events` — [`onWhatsnewAreas()`](../../../core/plugins/whatsnew/events/events.php)
- `plg_whatsnew_kb` — [`onWhatsnewAreas()`](../../../core/plugins/whatsnew/kb/kb.php)
- `plg_whatsnew_publications` — [`onWhatsnewAreas()`](../../../core/plugins/whatsnew/publications/publications.php)
- `plg_whatsnew_resources` — [`onWhatsnewAreas()`](../../../core/plugins/whatsnew/resources/resources.php)
- `plg_whatsnew_wiki` — [`onWhatsnewAreas()`](../../../core/plugins/whatsnew/wiki/wiki.php)

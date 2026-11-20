<!--
status: generated
source: Event::trigger('whatsnew.*') call sites and core/plugins/whatsnew/
-->

# Whatsnew events

Events in the `whatsnew` group. A plugin in `core/plugins/whatsnew/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `whatsnew.onWhatsNewAreas`

Fired from:

- [`core/components/com_whatsnew/api/controllers/entriesv1r0.php:77`](../../../core/components/com_whatsnew/api/controllers/entriesv1r0.php#L77)
- [`core/components/com_whatsnew/helpers/finder.php:33`](../../../core/components/com_whatsnew/helpers/finder.php#L33)

No plugin in the source tree listens for this event.

## `whatsnew.onWhatsnew`

Fired from:

- [`core/components/com_whatsnew/api/controllers/entriesv1r0.php:99`](../../../core/components/com_whatsnew/api/controllers/entriesv1r0.php#L99) with `[ $p, 999, 0, $areas, ]`
- [`core/components/com_whatsnew/site/controllers/results.php:117`](../../../core/components/com_whatsnew/site/controllers/results.php#L117) with `[ $p, 0, 0, $activeareas, ]`
- [`core/components/com_whatsnew/site/controllers/results.php:130`](../../../core/components/com_whatsnew/site/controllers/results.php#L130) with `[ $p, $limit, $start, $activeareas, ]`
- [`core/components/com_whatsnew/site/controllers/results.php:314`](../../../core/components/com_whatsnew/site/controllers/results.php#L314) with `[ $p, $limit, $start, $activeareas, ]`
- [`core/modules/mod_whatsnew/mod_whatsnew.php:203`](../../../core/modules/mod_whatsnew/mod_whatsnew.php#L203) with `[ $p, $count, 0, $activeareas, [], ]`

Listeners:

- `plg_whatsnew_content` — [`onWhatsnew($period, $limit = 0, $limitstart = 0, $areas = null, $tagids = [])`](../../../core/plugins/whatsnew/content/content.php)
- `plg_whatsnew_events` — [`onWhatsnew($period, $limit = 0, $limitstart = 0, $areas = null, $tagids = [])`](../../../core/plugins/whatsnew/events/events.php)
- `plg_whatsnew_kb` — [`onWhatsnew($period, $limit = 0, $limitstart = 0, $areas = null, $tagids = [])`](../../../core/plugins/whatsnew/kb/kb.php)
- `plg_whatsnew_publications` — [`onWhatsnew($period, $limit = 0, $limitstart = 0, $areas = null, $tagids = [])`](../../../core/plugins/whatsnew/publications/publications.php)
- `plg_whatsnew_resources` — [`onWhatsnew($period, $limit = 0, $limitstart = 0, $areas = null, $tagids = [])`](../../../core/plugins/whatsnew/resources/resources.php)
- `plg_whatsnew_wiki` — [`onWhatsnew($period, $limit = 0, $limitstart = 0, $areas = null, $tagids = [])`](../../../core/plugins/whatsnew/wiki/wiki.php)

## `whatsnew.onWhatsnewAreas`

Fired from:

- [`core/components/com_whatsnew/site/controllers/results.php:443`](../../../core/components/com_whatsnew/site/controllers/results.php#L443)
- [`core/modules/mod_whatsnew/mod_whatsnew.php:75`](../../../core/modules/mod_whatsnew/mod_whatsnew.php#L75)

Listeners:

- `plg_whatsnew_content` — [`onWhatsnewAreas()`](../../../core/plugins/whatsnew/content/content.php)
- `plg_whatsnew_events` — [`onWhatsnewAreas()`](../../../core/plugins/whatsnew/events/events.php)
- `plg_whatsnew_kb` — [`onWhatsnewAreas()`](../../../core/plugins/whatsnew/kb/kb.php)
- `plg_whatsnew_publications` — [`onWhatsnewAreas()`](../../../core/plugins/whatsnew/publications/publications.php)
- `plg_whatsnew_resources` — [`onWhatsnewAreas()`](../../../core/plugins/whatsnew/resources/resources.php)
- `plg_whatsnew_wiki` — [`onWhatsnewAreas()`](../../../core/plugins/whatsnew/wiki/wiki.php)

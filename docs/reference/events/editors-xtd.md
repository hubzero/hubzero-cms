<!--
status: generated
source: Event::trigger('editors-xtd.*') call sites and core/plugins/editors-xtd/
-->

# Editors-xtd events

Events in the `editors-xtd` group. A plugin in `core/plugins/editors-xtd/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `editors-xtd.onDisplay`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_editors-xtd_article` — [`onDisplay($name)`](../../../core/plugins/editors-xtd/article/article.php)
- `plg_editors-xtd_image` — [`onDisplay($name, $asset, $author)`](../../../core/plugins/editors-xtd/image/image.php)
- `plg_editors-xtd_pagebreak` — [`onDisplay($name)`](../../../core/plugins/editors-xtd/pagebreak/pagebreak.php)
- `plg_editors-xtd_readmore` — [`onDisplay($name)`](../../../core/plugins/editors-xtd/readmore/readmore.php)

<!--
status: generated
source: Event::trigger('resources.*') call sites and core/plugins/resources/
-->

# Resources events

Events in the `resources` group. A plugin in `core/plugins/resources/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `resources.onCanManage`

Fired from:

- [`core/components/com_resources/admin/controllers/plugins.php:178`](../../../core/components/com_resources/admin/controllers/plugins.php#L178)

Listeners:

- `plg_resources_sponsors` — [`onCanManage()`](../../../core/plugins/resources/sponsors/sponsors.php)

## `resources.onManage`

Fired from:

- [`core/components/com_resources/admin/controllers/plugins.php:205`](../../../core/components/com_resources/admin/controllers/plugins.php#L205) with `[ $this->_option, $this->_controller, Request::getString('action', 'default') ]`

Listeners:

- `plg_resources_sponsors` — [`onManage($option, $controller='plugins', $task='default')`](../../../core/plugins/resources/sponsors/sponsors.php)

## `resources.onResourceAfterSubmit`

Fired from:

- [`core/components/com_resources/site/controllers/create.php:1489`](../../../core/components/com_resources/site/controllers/create.php#L1489) with `[$resource]`

No plugin in the source tree listens for this event.

## `resources.onResourceBeforeSubmit`

Fired from:

- [`core/components/com_resources/site/controllers/create.php:1305`](../../../core/components/com_resources/site/controllers/create.php#L1305) with `[$resource]`

Listeners:

- `plg_resources_hipaacompliant` — [`onResourceBeforeSubmit(&$resource)`](../../../core/plugins/resources/hipaacompliant/hipaacompliant.php)

## `resources.onResources`

Fired from:

- [`core/components/com_resources/site/controllers/resources.php:658`](../../../core/components/com_resources/site/controllers/resources.php#L658) with `[$model, $this->_option, array('about'), 'metadata']`
- [`core/components/com_resources/site/controllers/resources.php:1497`](../../../core/components/com_resources/site/controllers/resources.php#L1497) with `[ $this->model, $this->_option, array($tab), 'all', ]`

Listeners:

- `plg_resources_about` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/about/about.php)
- `plg_resources_citations` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/citations/citations.php)
- `plg_resources_coins` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/coins/coins.php)
- `plg_resources_dublincore` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/dublincore/dublincore.php)
- `plg_resources_findthistext` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/findthistext/findthistext.php)
- `plg_resources_googlescholar` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/googlescholar/googlescholar.php)
- `plg_resources_opengraph` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/opengraph/opengraph.php)
- `plg_resources_questions` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/questions/questions.php)
- `plg_resources_reviews` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/reviews/reviews.php)
- `plg_resources_share` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/share/share.php)
- `plg_resources_supportingdocs` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/supportingdocs/supportingdocs.php)
- `plg_resources_usage` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/usage/usage.php)
- `plg_resources_versions` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/versions/versions.php)
- `plg_resources_windowstools` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/windowstools/windowstools.php)
- `plg_resources_wishlist` — [`onResources($model, $option, $areas, $rtrn='all')`](../../../core/plugins/resources/wishlist/wishlist.php)

## `resources.onResourcesAreas`

Fired from:

- [`core/components/com_resources/site/controllers/resources.php:1468`](../../../core/components/com_resources/site/controllers/resources.php#L1468) with `[ $this->model ]`

No plugin in the source tree listens for this event.

## `resources.onResourcesList`

Fired from:

- [`core/components/com_resources/site/views/browse/tmpl/item.php:41`](../../../core/components/com_resources/site/views/browse/tmpl/item.php#L41) with `[$this->line]`
- [`core/components/com_resources/site/views/search/tmpl/solr.php:9`](../../../core/components/com_resources/site/views/search/tmpl/solr.php#L9) with `[$id]`

Listeners:

- `plg_resources_coins` — [`onResourcesList($model)`](../../../core/plugins/resources/coins/coins.php)

## `resources.onResourcesRateItem`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_resources_reviews` — [`onResourcesRateItem($option)`](../../../core/plugins/resources/reviews/reviews.php)

## `resources.onResourcesSub`

Fired from:

- [`core/components/com_resources/site/views/view/tmpl/courses.php:212`](../../../core/components/com_resources/site/views/view/tmpl/courses.php#L212) with `[$this->model, $this->option, 1]`
- [`core/components/com_resources/site/views/view/tmpl/default.php:186`](../../../core/components/com_resources/site/views/view/tmpl/default.php#L186) with `[$this->model, $this->option, 1]`
- [`core/components/com_resources/site/views/view/tmpl/series.php:250`](../../../core/components/com_resources/site/views/view/tmpl/series.php#L250) with `[$this->model, $this->option, 1]`
- [`core/components/com_resources/site/views/view/tmpl/tools.php:286`](../../../core/components/com_resources/site/views/view/tmpl/tools.php#L286) with `[$this->model, $this->option, 1]`
- [`core/components/com_resources/site/views/view/tmpl/windowstools.php:209`](../../../core/components/com_resources/site/views/view/tmpl/windowstools.php#L209) with `[$this->model, $this->option, 1]`
- [`core/components/com_resources/site/views/view/tmpl/workshops.php:250`](../../../core/components/com_resources/site/views/view/tmpl/workshops.php#L250) with `[$this->model, $this->option, 1]`

Listeners:

- `plg_resources_collections` — [`onResourcesSub($resource, $option, $miniview=0)`](../../../core/plugins/resources/collections/collections.php)
- `plg_resources_groups` — [`onResourcesSub($resource, $option, $miniview=0)`](../../../core/plugins/resources/groups/groups.php)
- `plg_resources_recommendations` — [`onResourcesSub($resource, $option, $miniview=0)`](../../../core/plugins/resources/recommendations/recommendations.php)
- `plg_resources_related` — [`onResourcesSub($resource, $option, $miniview=0)`](../../../core/plugins/resources/related/related.php)
- `plg_resources_sponsors` — [`onResourcesSub($resource, $option, $miniview=0)`](../../../core/plugins/resources/sponsors/sponsors.php)
- `plg_resources_watch` — [`onResourcesSub($resource, $option, $miniview=0)`](../../../core/plugins/resources/watch/watch.php)

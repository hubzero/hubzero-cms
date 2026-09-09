<!--
status: generated
source: Event::trigger('publications.*') call sites and core/plugins/publications/
-->

# Publications events

Events in the `publications` group. A plugin in `core/plugins/publications/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `publications.onAfterSave`

Fired from:

- [`core/plugins/projects/publications/publications.php:856`](../../../core/plugins/projects/publications/publications.php#L856) with `$pub`

No plugin in the source tree listens for this event.

## `publications.onBeforeSave`

Fired from:

- [`core/plugins/projects/publications/publications.php:791`](../../../core/plugins/projects/publications/publications.php#L791) with `$pub`

No plugin in the source tree listens for this event.

## `publications.onPublication`

Fired from:

- [`core/components/com_publications/site/controllers/publications.php:655`](../../../core/components/com_publications/site/controllers/publications.php#L655) with `[ $this->model, $this->_option, array($tab), 'all', $this->model->versionAlias, $extended]`

Listeners:

- `plg_publications_citations` — [`onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)`](../../../core/plugins/publications/citations/citations.php)
- `plg_publications_dublincore` — [`onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)`](../../../core/plugins/publications/dublincore/dublincore.php)
- `plg_publications_forks` — [`onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)`](../../../core/plugins/publications/forks/forks.php)
- `plg_publications_googlescholar` — [`onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)`](../../../core/plugins/publications/googlescholar/googlescholar.php)
- `plg_publications_jsonld` — [`onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)`](../../../core/plugins/publications/jsonld/jsonld.php)
- `plg_publications_opengraph` — [`onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)`](../../../core/plugins/publications/opengraph/opengraph.php)
- `plg_publications_questions` — [`onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)`](../../../core/plugins/publications/questions/questions.php)
- `plg_publications_reviews` — [`onPublication($model, $option, $areas, $rtrn='all', $version = 'default', $extended = true)`](../../../core/plugins/publications/reviews/reviews.php)
- `plg_publications_share` — [`onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)`](../../../core/plugins/publications/share/share.php)
- `plg_publications_supportingdocs` — [`onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true, $authorized = true)`](../../../core/plugins/publications/supportingdocs/supportingdocs.php)
- `plg_publications_usage` — [`onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)`](../../../core/plugins/publications/usage/usage.php)
- `plg_publications_versions` — [`onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true, $authorized = false)`](../../../core/plugins/publications/versions/versions.php)
- `plg_publications_wishlist` — [`onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)`](../../../core/plugins/publications/wishlist/wishlist.php)

## `publications.onPublicationAreas`

Fired from:

- [`core/components/com_publications/site/controllers/publications.php:648`](../../../core/components/com_publications/site/controllers/publications.php#L648) with `[ $this->model, $this->model->versionAlias, $extended]`

No plugin in the source tree listens for this event.

## `publications.onPublicationExtended`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_publications_forks` — [`onPublicationExtended($publication, $option, $miniview=0)`](../../../core/plugins/publications/forks/forks.php)

## `publications.onPublicationRateItem`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_publications_reviews` — [`onPublicationRateItem($option)`](../../../core/plugins/publications/reviews/reviews.php)

## `publications.onPublicationSub`

Fired from:

- [`core/components/com_publications/site/views/view/tmpl/default.php:200`](../../../core/components/com_publications/site/views/view/tmpl/default.php#L200) with `[$this->publication, $this->option, 1]`

Listeners:

- `plg_publications_forks` — [`onPublicationSub($publication, $option, $miniview=0)`](../../../core/plugins/publications/forks/forks.php)
- `plg_publications_groups` — [`onPublicationSub($publication, $option, $miniview=0)`](../../../core/plugins/publications/groups/groups.php)
- `plg_publications_recommendations` — [`onPublicationSub($publication, $option, $miniview=0)`](../../../core/plugins/publications/recommendations/recommendations.php)
- `plg_publications_related` — [`onPublicationSub($publication, $option, $miniview=0)`](../../../core/plugins/publications/related/related.php)
- `plg_publications_watch` — [`onPublicationSub($publication, $option, $miniview=0)`](../../../core/plugins/publications/watch/watch.php)

## `publications.onPublicationsList`

Fired from:

- [`core/components/com_publications/site/views/browse/tmpl/item.php:51`](../../../core/components/com_publications/site/views/browse/tmpl/item.php#L51) with `[$this->line]`

No plugin in the source tree listens for this event.

## `publications.onWatch`

Fired from:

- [`core/components/com_publications/site/controllers/curation.php:966`](../../../core/components/com_publications/site/controllers/curation.php#L966) with `[$pub]`

Listeners:

- `plg_publications_watch` — [`onWatch($publication, $activity = 'newversion')`](../../../core/plugins/publications/watch/watch.php)

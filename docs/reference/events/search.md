<!--
status: generated
source: Event::trigger('search.*') call sites and core/plugins/search/
-->

# Search events

Events in the `search` group. A plugin in `core/plugins/search/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `search.onAddIndex`

Fired from:

- [`core/plugins/groups/search/search.php:42`](../../../core/plugins/groups/search/search.php#L42) with `[$table, $ormGroup]`
- [`core/plugins/system/content/content.php:26`](../../../core/plugins/system/content/content.php#L26) with `[$table, $model]`

Listeners:

- `plg_search_solr` — [`onAddIndex($table, $model)`](../../../core/plugins/search/solr/solr.php)

## `search.onAddPermissionSet`

Fired from:

- [`core/libraries/Hubzero/Search/Adapters/SolrQueryAdapter.php:436`](../../../core/libraries/Hubzero/Search/Adapters/SolrQueryAdapter.php#L436)

No plugin in the source tree listens for this event.

## `search.onBeforeSearchRenderMembers`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_search_members` — [`onBeforeSearchRenderMembers($res)`](../../../core/plugins/search/members/members.php)

## `search.onExtensionAfterDelete`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_search_remote` — [`onExtensionAfterDelete($extension, Components\Plugins\Models\Plugin $model)`](../../../core/plugins/search/remote/remote.php)

## `search.onFormatResult`

Fired from:

- [`core/components/com_search/site/controllers/solr.php:356`](../../../core/components/com_search/site/controllers/solr.php#L356) with `[$result['hubtype'], &$result, $terms, $highlightOptions]`

No plugin in the source tree listens for this event.

## `search.onGetTypes`

Fired from:

- [`core/components/com_search/api/controllers/searchv1_0.php:268`](../../../core/components/com_search/api/controllers/searchv1_0.php#L268)

Listeners:

- `plg_search_blogs` — [`onGetTypes($type = null)`](../../../core/plugins/search/blogs/blogs.php)
- `plg_search_citations` — [`onGetTypes($type = null)`](../../../core/plugins/search/citations/citations.php)
- `plg_search_collections` — [`onGetTypes($type = null)`](../../../core/plugins/search/collections/collections.php)
- `plg_search_content` — [`onGetTypes($type = null)`](../../../core/plugins/search/content/content.php)
- `plg_search_courses` — [`onGetTypes($type = null)`](../../../core/plugins/search/courses/courses.php)
- `plg_search_events` — [`onGetTypes($type = null)`](../../../core/plugins/search/events/events.php)
- `plg_search_forum` — [`onGetTypes($type = null)`](../../../core/plugins/search/forum/forum.php)
- `plg_search_groups` — [`onGetTypes($type = null)`](../../../core/plugins/search/groups/groups.php)
- `plg_search_kb` — [`onGetTypes($type = null)`](../../../core/plugins/search/kb/kb.php)
- `plg_search_members` — [`onGetTypes($type = null)`](../../../core/plugins/search/members/members.php)
- `plg_search_projects` — [`onGetTypes($type = null)`](../../../core/plugins/search/projects/projects.php)
- `plg_search_publications` — [`onGetTypes($type = null)`](../../../core/plugins/search/publications/publications.php)
- `plg_search_questions` — [`onGetTypes($type = null)`](../../../core/plugins/search/questions/questions.php)
- `plg_search_resources` — [`onGetTypes($type = null)`](../../../core/plugins/search/resources/resources.php)
- `plg_search_tickets` — [`onGetTypes($type = null)`](../../../core/plugins/search/tickets/tickets.php)
- `plg_search_wiki` — [`onGetTypes($type = null)`](../../../core/plugins/search/wiki/wiki.php)
- `plg_search_wishlists` — [`onGetTypes($type = null)`](../../../core/plugins/search/wishlists/wishlists.php)

## `search.onIndex`

Fired from:

- [`core/plugins/cron/search/search.php:171`](../../../core/plugins/cron/search/search.php#L171) with `[$item->type, $item->type_id, true]`

Listeners:

- `plg_search_blogs` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/blogs/blogs.php)
- `plg_search_citations` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/citations/citations.php)
- `plg_search_collections` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/collections/collections.php)
- `plg_search_content` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/content/content.php)
- `plg_search_courses` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/courses/courses.php)
- `plg_search_events` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/events/events.php)
- `plg_search_forum` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/forum/forum.php)
- `plg_search_groups` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/groups/groups.php)
- `plg_search_kb` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/kb/kb.php)
- `plg_search_members` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/members/members.php)
- `plg_search_projects` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/projects/projects.php)
- `plg_search_publications` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/publications/publications.php)
- `plg_search_questions` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/questions/questions.php)
- `plg_search_resources` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/resources/resources.php)
- `plg_search_tickets` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/tickets/tickets.php)
- `plg_search_wiki` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/wiki/wiki.php)
- `plg_search_wishlists` — [`onIndex($type, $id, $run = false)`](../../../core/plugins/search/wishlists/wishlists.php)

## `search.onRemoveIndex`

Fired from:

- [`core/plugins/system/content/content.php:39`](../../../core/plugins/system/content/content.php#L39) with `[$table, $model]`

Listeners:

- `plg_search_solr` — [`onRemoveIndex($table, $model)`](../../../core/plugins/search/solr/solr.php)

<!--
status: generated
source: Event::trigger('tags.*') call sites and core/plugins/tags/
-->

# Tags events

Events in the `tags` group. A plugin in `core/plugins/tags/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `tags.onTagAfterSave`

Fired from:

- [`core/components/com_tags/admin/controllers/entries.php:231`](../../../core/components/com_tags/admin/controllers/entries.php#L231) with `[&$row, $isNew]`
- [`core/components/com_tags/api/controllers/entriesv1r0.php:265`](../../../core/components/com_tags/api/controllers/entriesv1r0.php#L265) with `[&$record, $isNew]`
- [`core/components/com_tags/api/controllers/entriesv1r0.php:400`](../../../core/components/com_tags/api/controllers/entriesv1r0.php#L400) with `[&$record, $isNew]`
- [`core/components/com_tags/site/controllers/tags.php:805`](../../../core/components/com_tags/site/controllers/tags.php#L805) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `tags.onTagAreas`

Fired from:

- [`core/components/com_tags/site/controllers/tags.php:432`](../../../core/components/com_tags/site/controllers/tags.php#L432)

No plugin in the source tree listens for this event.

## `tags.onTagBeforeSave`

Fired from:

- [`core/components/com_tags/admin/controllers/entries.php:206`](../../../core/components/com_tags/admin/controllers/entries.php#L206) with `[&$row, $isNew]`
- [`core/components/com_tags/api/controllers/entriesv1r0.php:249`](../../../core/components/com_tags/api/controllers/entriesv1r0.php#L249) with `[&$record, $isNew]`
- [`core/components/com_tags/api/controllers/entriesv1r0.php:380`](../../../core/components/com_tags/api/controllers/entriesv1r0.php#L380) with `[&$record, $isNew]`
- [`core/components/com_tags/site/controllers/tags.php:780`](../../../core/components/com_tags/site/controllers/tags.php#L780) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `tags.onTagDelete`

Fired from:

- [`core/components/com_tags/admin/controllers/entries.php:276`](../../../core/components/com_tags/admin/controllers/entries.php#L276) with `[$id]`
- [`core/components/com_tags/api/controllers/entriesv1r0.php:438`](../../../core/components/com_tags/api/controllers/entriesv1r0.php#L438) with `[$id]`
- [`core/components/com_tags/site/controllers/tags.php:857`](../../../core/components/com_tags/site/controllers/tags.php#L857) with `[$id]`

No plugin in the source tree listens for this event.

## `tags.onTagView`

Fired from:

- [`core/components/com_tags/site/controllers/tags.php:171`](../../../core/components/com_tags/site/controllers/tags.php#L171) with `[ $tags, $this->view->filters['limit'], $this->view->filters['start'], $this->view->filters['sort'], $area, ]`
- [`core/components/com_tags/site/controllers/tags.php:440`](../../../core/components/com_tags/site/controllers/tags.php#L440) with `[ $tags, $limit, $limitstart, $sort, '', ]`
- [`core/components/com_tags/site/controllers/tags.php:500`](../../../core/components/com_tags/site/controllers/tags.php#L500) with `[ $tags, $limit, $limitstart, $sort, $area, ]`

Listeners:

- `plg_tags_answers` — [`onTagView($tags, $limit = 0, $limitstart = 0, $sort = '', $areas = null)`](../../../core/plugins/tags/answers/answers.php)
- `plg_tags_blogs` — [`onTagView($tags, $limit = 0, $limitstart = 0, $sort = '', $areas = null)`](../../../core/plugins/tags/blogs/blogs.php)
- `plg_tags_citations` — [`onTagView($tags, $limit = 0, $limitstart = 0, $sort = '', $areas = null)`](../../../core/plugins/tags/citations/citations.php)
- `plg_tags_collections` — [`onTagView($tags, $limit = 0, $limitstart = 0, $sort = '', $areas = null)`](../../../core/plugins/tags/collections/collections.php)
- `plg_tags_courses` — [`onTagView($tags, $limit = 0, $limitstart = 0, $sort = '', $areas = null)`](../../../core/plugins/tags/courses/courses.php)
- `plg_tags_events` — [`onTagView($tags, $limit = 0, $limitstart = 0, $sort = '', $areas = null)`](../../../core/plugins/tags/events/events.php)
- `plg_tags_forum` — [`onTagView($tags, $limit = 0, $limitstart = 0, $sort = '', $areas = null)`](../../../core/plugins/tags/forum/forum.php)
- `plg_tags_groups` — [`onTagView($tags, $limit = 0, $limitstart = 0, $sort = '', $areas = null)`](../../../core/plugins/tags/groups/groups.php)
- `plg_tags_kb` — [`onTagView($tags, $limit = 0, $limitstart = 0, $sort = '', $areas = null)`](../../../core/plugins/tags/kb/kb.php)
- `plg_tags_members` — [`onTagView($tags, $limit = 0, $limitstart = 0, $sort = '', $areas = null)`](../../../core/plugins/tags/members/members.php)
- `plg_tags_publications` — [`onTagView($tags, $limit = 0, $limitstart = 0, $sort = '', $areas = null)`](../../../core/plugins/tags/publications/publications.php)
- `plg_tags_resources` — [`onTagView($tags, $limit = 0, $limitstart = 0, $sort = '', $areas = null)`](../../../core/plugins/tags/resources/resources.php)
- `plg_tags_wiki` — [`onTagView($tags, $limit = 0, $limitstart = 0, $sort = '', $areas = null)`](../../../core/plugins/tags/wiki/wiki.php)

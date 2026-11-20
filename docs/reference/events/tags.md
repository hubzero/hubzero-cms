<!--
status: generated
source: Event::trigger('tags.*') call sites and core/plugins/tags/
-->

# Tags events

Events in the `tags` group. A plugin in `core/plugins/tags/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `tags.onTagAfterSave`

Fired from:

- [`core/components/com_tags/admin/controllers/entries.php:229`](../../../core/components/com_tags/admin/controllers/entries.php#L229) with `[&$row, $isNew]`
- [`core/components/com_tags/api/controllers/entriesv1r0.php:263`](../../../core/components/com_tags/api/controllers/entriesv1r0.php#L263) with `[&$record, $isNew]`
- [`core/components/com_tags/api/controllers/entriesv1r0.php:398`](../../../core/components/com_tags/api/controllers/entriesv1r0.php#L398) with `[&$record, $isNew]`
- [`core/components/com_tags/site/controllers/tags.php:809`](../../../core/components/com_tags/site/controllers/tags.php#L809) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `tags.onTagAreas`

Fired from:

- [`core/components/com_tags/site/controllers/tags.php:436`](../../../core/components/com_tags/site/controllers/tags.php#L436)

No plugin in the source tree listens for this event.

## `tags.onTagBeforeSave`

Fired from:

- [`core/components/com_tags/admin/controllers/entries.php:204`](../../../core/components/com_tags/admin/controllers/entries.php#L204) with `[&$row, $isNew]`
- [`core/components/com_tags/api/controllers/entriesv1r0.php:247`](../../../core/components/com_tags/api/controllers/entriesv1r0.php#L247) with `[&$record, $isNew]`
- [`core/components/com_tags/api/controllers/entriesv1r0.php:378`](../../../core/components/com_tags/api/controllers/entriesv1r0.php#L378) with `[&$record, $isNew]`
- [`core/components/com_tags/site/controllers/tags.php:784`](../../../core/components/com_tags/site/controllers/tags.php#L784) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `tags.onTagDelete`

Fired from:

- [`core/components/com_tags/admin/controllers/entries.php:274`](../../../core/components/com_tags/admin/controllers/entries.php#L274) with `[$id]`
- [`core/components/com_tags/api/controllers/entriesv1r0.php:436`](../../../core/components/com_tags/api/controllers/entriesv1r0.php#L436) with `[$id]`
- [`core/components/com_tags/site/controllers/tags.php:858`](../../../core/components/com_tags/site/controllers/tags.php#L858) with `[$id]`

No plugin in the source tree listens for this event.

## `tags.onTagView`

Fired from:

- [`core/components/com_tags/site/controllers/tags.php:170`](../../../core/components/com_tags/site/controllers/tags.php#L170) with `[ $tags, $this->view->get('filters')['limit'], $this->view->get('filters')['start'], $this->view->get('filters')['sort'], $area, ]`
- [`core/components/com_tags/site/controllers/tags.php:444`](../../../core/components/com_tags/site/controllers/tags.php#L444) with `[ $tags, $limit, $limitstart, $sort, '', ]`
- [`core/components/com_tags/site/controllers/tags.php:504`](../../../core/components/com_tags/site/controllers/tags.php#L504) with `[ $tags, $limit, $limitstart, $sort, $area, ]`

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

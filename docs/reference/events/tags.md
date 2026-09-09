<!--
status: generated
source: Event::trigger('tags.*') call sites and core/plugins/tags/
-->

# Tags events

Events in the `tags` group. A plugin in `core/plugins/tags/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `tags.onTagAfterSave`

Fired from:

- [`core/components/com_tags/admin/controllers/entries.php:232`](../../../core/components/com_tags/admin/controllers/entries.php#L232) with `[&$row, $isNew]`
- [`core/components/com_tags/api/controllers/entriesv1_0.php:254`](../../../core/components/com_tags/api/controllers/entriesv1_0.php#L254) with `[&$record, $isNew]`
- [`core/components/com_tags/api/controllers/entriesv1_0.php:390`](../../../core/components/com_tags/api/controllers/entriesv1_0.php#L390) with `[&$record, $isNew]`
- [`core/components/com_tags/site/controllers/tags.php:837`](../../../core/components/com_tags/site/controllers/tags.php#L837) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `tags.onTagAreas`

Fired from:

- [`core/components/com_tags/site/controllers/tags.php:464`](../../../core/components/com_tags/site/controllers/tags.php#L464)

No plugin in the source tree listens for this event.

## `tags.onTagBeforeSave`

Fired from:

- [`core/components/com_tags/admin/controllers/entries.php:210`](../../../core/components/com_tags/admin/controllers/entries.php#L210) with `[&$row, $isNew]`
- [`core/components/com_tags/api/controllers/entriesv1_0.php:235`](../../../core/components/com_tags/api/controllers/entriesv1_0.php#L235) with `[&$record, $isNew]`
- [`core/components/com_tags/api/controllers/entriesv1_0.php:366`](../../../core/components/com_tags/api/controllers/entriesv1_0.php#L366) with `[&$record, $isNew]`
- [`core/components/com_tags/site/controllers/tags.php:815`](../../../core/components/com_tags/site/controllers/tags.php#L815) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `tags.onTagDelete`

Fired from:

- [`core/components/com_tags/admin/controllers/entries.php:278`](../../../core/components/com_tags/admin/controllers/entries.php#L278) with `[$id]`
- [`core/components/com_tags/api/controllers/entriesv1_0.php:424`](../../../core/components/com_tags/api/controllers/entriesv1_0.php#L424) with `[$id]`
- [`core/components/com_tags/site/controllers/tags.php:881`](../../../core/components/com_tags/site/controllers/tags.php#L881) with `[$id]`

No plugin in the source tree listens for this event.

## `tags.onTagView`

Fired from:

- [`core/components/com_tags/site/controllers/tags.php:177`](../../../core/components/com_tags/site/controllers/tags.php#L177) with `[ $tags, $this->view->filters['limit'], $this->view->filters['start'], $this->view->filters['sort'], $area ]`
- [`core/components/com_tags/site/controllers/tags.php:473`](../../../core/components/com_tags/site/controllers/tags.php#L473) with `[ $tags, $limit, $limitstart, $sort, '' ]`
- [`core/components/com_tags/site/controllers/tags.php:538`](../../../core/components/com_tags/site/controllers/tags.php#L538) with `[ $tags, $limit, $limitstart, $sort, $area ]`

Listeners:

- `plg_tags_answers` — [`onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)`](../../../core/plugins/tags/answers/answers.php)
- `plg_tags_blogs` — [`onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)`](../../../core/plugins/tags/blogs/blogs.php)
- `plg_tags_citations` — [`onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)`](../../../core/plugins/tags/citations/citations.php)
- `plg_tags_collections` — [`onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)`](../../../core/plugins/tags/collections/collections.php)
- `plg_tags_courses` — [`onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)`](../../../core/plugins/tags/courses/courses.php)
- `plg_tags_events` — [`onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)`](../../../core/plugins/tags/events/events.php)
- `plg_tags_forum` — [`onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)`](../../../core/plugins/tags/forum/forum.php)
- `plg_tags_groups` — [`onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)`](../../../core/plugins/tags/groups/groups.php)
- `plg_tags_kb` — [`onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)`](../../../core/plugins/tags/kb/kb.php)
- `plg_tags_members` — [`onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)`](../../../core/plugins/tags/members/members.php)
- `plg_tags_publications` — [`onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)`](../../../core/plugins/tags/publications/publications.php)
- `plg_tags_resources` — [`onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)`](../../../core/plugins/tags/resources/resources.php)
- `plg_tags_wiki` — [`onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)`](../../../core/plugins/tags/wiki/wiki.php)

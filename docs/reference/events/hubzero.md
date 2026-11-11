<!--
status: generated
source: Event::trigger('hubzero.*') call sites and core/plugins/hubzero/
-->

# Hubzero events

Events in the `hubzero` group. A plugin in `core/plugins/hubzero/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `hubzero.onAfterDisplayContent`

Fired from:

- [`core/components/com_citations/site/views/citations/tmpl/view.php:550`](../../../core/components/com_citations/site/views/citations/tmpl/view.php#L550) with `$params`
- [`core/components/com_groups/site/views/pages/tmpl/_view.php:262`](../../../core/components/com_groups/site/views/pages/tmpl/_view.php#L262) with `$params`
- [`core/plugins/groups/citations/views/view/tmpl/view.php:554`](../../../core/plugins/groups/citations/views/view/tmpl/view.php#L554) with `$params`

Listeners:

- `plg_hubzero_comments` — [`onAfterDisplayContent($obj, $option, $url = null, $params = null)`](../../../core/plugins/hubzero/comments/comments.php)

## `hubzero.onGetAutocompleter`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_hubzero_autocompleter` — [`onGetAutocompleter($atts)`](../../../core/plugins/hubzero/autocompleter/autocompleter.php)

## `hubzero.onGetMultiEntry`

Fired from:

- [`core/components/com_blog/admin/views/entries/tmpl/edit.php:172`](../../../core/components/com_blog/admin/views/entries/tmpl/edit.php#L172) with `[['tags', 'tags', 'field-tags', '', $this->row->tags('string')]]`
- [`core/components/com_citations/site/views/citations/tmpl/browse.php:323`](../../../core/components/com_citations/site/views/citations/tmpl/browse.php#L323) with `[['tags', 'tag', 'actags', '', $this->escape($this->filters['tag'])]]`
- [`core/components/com_citations/site/views/citations/tmpl/edit.php:149`](../../../core/components/com_citations/site/views/citations/tmpl/edit.php#L149) with `[ [ 'members', 'author', 'field-author', '', (isset($this->authorString) ? $this->authorString : ''), ], ]`
- [`core/components/com_citations/site/views/citations/tmpl/edit.php:401`](../../../core/components/com_citations/site/views/citations/tmpl/edit.php#L401) with `[ [ 'tags', 'tags', 'actags', '', implode(",", $this->tags), ], ]`
- [`core/components/com_citations/site/views/citations/tmpl/edit.php:429`](../../../core/components/com_citations/site/views/citations/tmpl/edit.php#L429) with `[ [ 'tags', 'badges', 'actags1', '', implode(",", $this->badges), ], ]`
- [`core/components/com_courses/admin/views/students/tmpl/add.php:77`](../../../core/components/com_courses/admin/views/students/tmpl/add.php#L77) with `[ [ 'members', // The component to call 'fields[user_id]', // Name of the input field 'acmembers', // ID of the input field '', // CSS cl…`
- [`core/components/com_courses/site/views/course/tmpl/edit.php:78`](../../../core/components/com_courses/site/views/course/tmpl/edit.php#L78) with `[['tags', 'tags', 'actags','', $this->course->tags('string')]]`
- [`core/components/com_courses/site/views/managers/tmpl/display.php:36`](../../../core/components/com_courses/site/views/managers/tmpl/display.php#L36) with `[ ['members', 'usernames', 'field-usernames', '', ''], ]`
- [`core/components/com_developer/admin/views/applications/tmpl/edit.php:142`](../../../core/components/com_developer/admin/views/applications/tmpl/edit.php#L142) with `[['members', 'team', 'acmembers', '', implode(', ', $currentTeam)]]`
- [`core/components/com_developer/site/views/applications/tmpl/edit.php:130`](../../../core/components/com_developer/site/views/applications/tmpl/edit.php#L130) with `[['members', 'team', 'acmembers']]`
- [`core/components/com_events/site/views/edit/tmpl/default.php:118`](../../../core/components/com_events/site/views/edit/tmpl/default.php#L118) with `[['tags', 'tags', 'actags','',$this->lists['tags']]]`
- [`core/components/com_groups/site/views/groups/tmpl/edit.php:25`](../../../core/components/com_groups/site/views/groups/tmpl/edit.php#L25) with `[['tags', 'tags', 'actags','', $this->tags]]`
- and 50 more call sites

Listeners:

- `plg_hubzero_autocompleter` — [`onGetMultiEntry($atts)`](../../../core/plugins/hubzero/autocompleter/autocompleter.php)

## `hubzero.onGetSingleEntry`

Fired from:

- [`core/components/com_messages/admin/views/messages/tmpl/edit.php:41`](../../../core/components/com_messages/admin/views/messages/tmpl/edit.php#L41) with `[ [ 'members', // The component to call 'fields[user_id_to]', // Name of the input field 'field-user_id_to', // ID of the input field 're…`
- [`core/components/com_tools/admin/views/preferences/tmpl/edit.php:55`](../../../core/components/com_tools/admin/views/preferences/tmpl/edit.php#L55) with `[ [ 'members', // The component to call 'fields[user_id]', // Name of the input field 'field-user_id', // ID of the input field '', // CS…`
- [`core/plugins/projects/team/team.php:459`](../../../core/plugins/projects/team/team.php#L459) with `[['members', 'uid', 'uid']]`

Listeners:

- `plg_hubzero_autocompleter` — [`onGetSingleEntry($atts)`](../../../core/plugins/hubzero/autocompleter/autocompleter.php)

## `hubzero.onGetSingleEntryWithSelect`

Fired from:

- [`core/components/com_publications/site/views/curation/tmpl/assign.php:48`](../../../core/components/com_publications/site/views/curation/tmpl/assign.php#L48) with `[['members', 'owner', 'owner', '', $selected, '', 'owner']]`
- [`core/components/com_support/admin/views/tickets/tmpl/add.php:140`](../../../core/components/com_support/admin/views/tickets/tmpl/add.php#L140) with `[['groups', 'ticket[group_id]', 'acgroup', '', '', '', 'owner']]`
- [`core/components/com_support/admin/views/tickets/tmpl/batch.php:95`](../../../core/components/com_support/admin/views/tickets/tmpl/batch.php#L95) with `[['groups', 'fields[group]', 'acgroup', '', '', '', 'owner']]`
- [`core/components/com_support/admin/views/tickets/tmpl/edit.php:573`](../../../core/components/com_support/admin/views/tickets/tmpl/edit.php#L573) with `[['groups', 'ticket[group_id]', 'acgroup','', $group,'','owner']]`
- [`core/components/com_support/site/views/tickets/tmpl/new.php:200`](../../../core/components/com_support/site/views/tickets/tmpl/new.php#L200) with `[ [ 'groups', 'problem[group_id]', 'acgroup', '', $this->escape($group), '', 'ticketowner', ], ]`
- [`core/components/com_support/site/views/tickets/tmpl/ticket.php:484`](../../../core/components/com_support/site/views/tickets/tmpl/ticket.php#L484) with `[['groups', 'ticket[group_id]', 'acgroup', '', $group, '', 'ticketowner']]`

Listeners:

- `plg_hubzero_autocompleter` — [`onGetSingleEntryWithSelect($atts)`](../../../core/plugins/hubzero/autocompleter/autocompleter.php)

## `hubzero.onSystemOverview`

Fired from:

- [`core/components/com_system/api/controllers/systemv1r0.php:154`](../../../core/components/com_system/api/controllers/systemv1r0.php#L154) with `[$values]`

Listeners:

- `plg_hubzero_systemplate` — [`onSystemOverview($values = 'all')`](../../../core/plugins/hubzero/systemplate/systemplate.php)
- `plg_hubzero_systickets` — [`onSystemOverview($values = 'all')`](../../../core/plugins/hubzero/systickets/systickets.php)
- `plg_hubzero_sysusers` — [`onSystemOverview($values = 'all')`](../../../core/plugins/hubzero/sysusers/sysusers.php)

<!--
status: generated
source: Event::trigger('hubzero.*') call sites and core/plugins/hubzero/
-->

# Hubzero events

Events in the `hubzero` group. A plugin in `core/plugins/hubzero/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `hubzero.onAfterDisplayContent`

Fired from:

- [`core/components/com_citations/site/views/citations/tmpl/view.php:592`](../../../core/components/com_citations/site/views/citations/tmpl/view.php#L592) with `$params`
- [`core/components/com_groups/site/views/pages/tmpl/_view.php:276`](../../../core/components/com_groups/site/views/pages/tmpl/_view.php#L276) with `$params`
- [`core/plugins/groups/citations/views/view/tmpl/view.php:594`](../../../core/plugins/groups/citations/views/view/tmpl/view.php#L594) with `$params`

Listeners:

- `plg_hubzero_comments` — [`onAfterDisplayContent($obj, $option, $url = null, $params = null)`](../../../core/plugins/hubzero/comments/comments.php)

## `hubzero.onGetAutocompleter`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_hubzero_autocompleter` — [`onGetAutocompleter($atts)`](../../../core/plugins/hubzero/autocompleter/autocompleter.php)

## `hubzero.onGetMultiEntry`

Fired from:

- [`core/components/com_blog/admin/views/entries/tmpl/edit.php:189`](../../../core/components/com_blog/admin/views/entries/tmpl/edit.php#L189) with `[['tags', 'tags', 'field-tags', '', $this->row->tags('string')]]`
- [`core/components/com_citations/site/views/citations/tmpl/browse.php:367`](../../../core/components/com_citations/site/views/citations/tmpl/browse.php#L367) with `[['tags', 'tag', 'actags', '', $this->escape($this->filters['tag'])]]`
- [`core/components/com_citations/site/views/citations/tmpl/edit.php:192`](../../../core/components/com_citations/site/views/citations/tmpl/edit.php#L192) with `[ [ 'members', 'author', 'field-author', '', (isset($this->authorString) ? $this->authorString : ''), ], ]`
- [`core/components/com_citations/site/views/citations/tmpl/edit.php:451`](../../../core/components/com_citations/site/views/citations/tmpl/edit.php#L451) with `[ [ 'tags', 'tags', 'actags', '', implode(",", $this->tags), ], ]`
- [`core/components/com_citations/site/views/citations/tmpl/edit.php:479`](../../../core/components/com_citations/site/views/citations/tmpl/edit.php#L479) with `[ [ 'tags', 'badges', 'actags1', '', implode(",", $this->badges), ], ]`
- [`core/components/com_courses/admin/views/students/tmpl/add.php:83`](../../../core/components/com_courses/admin/views/students/tmpl/add.php#L83) with `[ [ 'members', // The component to call 'fields[user_id]', // Name of the input field 'acmembers', // ID of the input field '', // CSS cl…`
- [`core/components/com_courses/site/views/course/tmpl/edit.php:92`](../../../core/components/com_courses/site/views/course/tmpl/edit.php#L92) with `[['tags', 'tags', 'actags','', $this->course->tags('string')]]`
- [`core/components/com_courses/site/views/managers/tmpl/display.php:42`](../../../core/components/com_courses/site/views/managers/tmpl/display.php#L42) with `[ ['members', 'usernames', 'field-usernames', '', ''], ]`
- [`core/components/com_developer/admin/views/applications/tmpl/edit.php:159`](../../../core/components/com_developer/admin/views/applications/tmpl/edit.php#L159) with `[['members', 'team', 'acmembers', '', implode(', ', $currentTeam)]]`
- [`core/components/com_developer/site/views/applications/tmpl/edit.php:136`](../../../core/components/com_developer/site/views/applications/tmpl/edit.php#L136) with `[['members', 'team', 'acmembers']]`
- [`core/components/com_events/site/views/edit/tmpl/default.php:140`](../../../core/components/com_events/site/views/edit/tmpl/default.php#L140) with `[['tags', 'tags', 'actags','',$this->lists['tags']]]`
- [`core/components/com_groups/site/views/groups/tmpl/edit.php:42`](../../../core/components/com_groups/site/views/groups/tmpl/edit.php#L42) with `[['tags', 'tags', 'actags','', $this->tags]]`
- and 50 more call sites

Listeners:

- `plg_hubzero_autocompleter` — [`onGetMultiEntry($atts)`](../../../core/plugins/hubzero/autocompleter/autocompleter.php)

## `hubzero.onGetSingleEntry`

Fired from:

- [`core/components/com_messages/admin/views/messages/tmpl/edit.php:47`](../../../core/components/com_messages/admin/views/messages/tmpl/edit.php#L47) with `[ [ 'members', // The component to call 'fields[user_id_to]', // Name of the input field 'field-user_id_to', // ID of the input field 're…`
- [`core/components/com_tools/admin/views/preferences/tmpl/edit.php:61`](../../../core/components/com_tools/admin/views/preferences/tmpl/edit.php#L61) with `[ [ 'members', // The component to call 'fields[user_id]', // Name of the input field 'field-user_id', // ID of the input field '', // CS…`
- [`core/plugins/projects/team/team.php:459`](../../../core/plugins/projects/team/team.php#L459) with `[['members', 'uid', 'uid']]`

Listeners:

- `plg_hubzero_autocompleter` — [`onGetSingleEntry($atts)`](../../../core/plugins/hubzero/autocompleter/autocompleter.php)

## `hubzero.onGetSingleEntryWithSelect`

Fired from:

- [`core/components/com_publications/site/views/curation/tmpl/assign.php:54`](../../../core/components/com_publications/site/views/curation/tmpl/assign.php#L54) with `[['members', 'owner', 'owner', '', $selected, '', 'owner']]`
- [`core/components/com_support/admin/views/tickets/tmpl/add.php:146`](../../../core/components/com_support/admin/views/tickets/tmpl/add.php#L146) with `[['groups', 'ticket[group_id]', 'acgroup', '', '', '', 'owner']]`
- [`core/components/com_support/admin/views/tickets/tmpl/batch.php:101`](../../../core/components/com_support/admin/views/tickets/tmpl/batch.php#L101) with `[['groups', 'fields[group]', 'acgroup', '', '', '', 'owner']]`
- [`core/components/com_support/admin/views/tickets/tmpl/edit.php:675`](../../../core/components/com_support/admin/views/tickets/tmpl/edit.php#L675) with `[['groups', 'ticket[group_id]', 'acgroup','', $group,'','owner']]`
- [`core/components/com_support/site/views/tickets/tmpl/new.php:242`](../../../core/components/com_support/site/views/tickets/tmpl/new.php#L242) with `[ [ 'groups', 'problem[group_id]', 'acgroup', '', $this->escape($group), '', 'ticketowner', ], ]`
- [`core/components/com_support/site/views/tickets/tmpl/ticket.php:599`](../../../core/components/com_support/site/views/tickets/tmpl/ticket.php#L599) with `[['groups', 'ticket[group_id]', 'acgroup', '', $group, '', 'ticketowner']]`

Listeners:

- `plg_hubzero_autocompleter` — [`onGetSingleEntryWithSelect($atts)`](../../../core/plugins/hubzero/autocompleter/autocompleter.php)

## `hubzero.onSystemOverview`

Fired from:

- [`core/components/com_system/api/controllers/systemv1r0.php:154`](../../../core/components/com_system/api/controllers/systemv1r0.php#L154) with `[$values]`

Listeners:

- `plg_hubzero_systemplate` — [`onSystemOverview($values = 'all')`](../../../core/plugins/hubzero/systemplate/systemplate.php)
- `plg_hubzero_systickets` — [`onSystemOverview($values = 'all')`](../../../core/plugins/hubzero/systickets/systickets.php)
- `plg_hubzero_sysusers` — [`onSystemOverview($values = 'all')`](../../../core/plugins/hubzero/sysusers/sysusers.php)

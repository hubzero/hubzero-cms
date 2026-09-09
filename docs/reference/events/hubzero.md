<!--
status: generated
source: Event::trigger('hubzero.*') call sites and core/plugins/hubzero/
-->

# Hubzero events

Events in the `hubzero` group. A plugin in `core/plugins/hubzero/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `hubzero.onAfterDisplayContent`

Fired from:

- [`core/components/com_citations/site/views/citations/tmpl/view.php:561`](../../../core/components/com_citations/site/views/citations/tmpl/view.php#L561) with `$params`
- [`core/components/com_groups/site/views/pages/tmpl/_view.php:224`](../../../core/components/com_groups/site/views/pages/tmpl/_view.php#L224) with `$params`
- [`core/plugins/groups/citations/views/view/tmpl/view.php:566`](../../../core/plugins/groups/citations/views/view/tmpl/view.php#L566) with `$params`

Listeners:

- `plg_hubzero_comments` — [`onAfterDisplayContent($obj, $option, $url=null, $params = null)`](../../../core/plugins/hubzero/comments/comments.php)

## `hubzero.onGetAutocompleter`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_hubzero_autocompleter` — [`onGetAutocompleter($atts)`](../../../core/plugins/hubzero/autocompleter/autocompleter.php)

## `hubzero.onGetMultiEntry`

Fired from:

- [`core/components/com_blog/admin/views/entries/tmpl/edit.php:85`](../../../core/components/com_blog/admin/views/entries/tmpl/edit.php#L85) with `[array('tags', 'tags', 'field-tags', '', $this->row->tags('string'))]`
- [`core/components/com_citations/site/views/citations/tmpl/browse.php:287`](../../../core/components/com_citations/site/views/citations/tmpl/browse.php#L287) with `[array('tags', 'tag', 'actags', '', $this->escape($this->filters['tag']))]`
- [`core/components/com_citations/site/views/citations/tmpl/edit.php:133`](../../../core/components/com_citations/site/views/citations/tmpl/edit.php#L133) with `[array('members', 'author', 'field-author', '', (isset($this->authorString) ? $this->authorString : ''))]`
- [`core/components/com_citations/site/views/citations/tmpl/edit.php:343`](../../../core/components/com_citations/site/views/citations/tmpl/edit.php#L343) with `[ array('tags', 'tags', 'actags', '', implode(",", $this->tags) ) ]`
- [`core/components/com_citations/site/views/citations/tmpl/edit.php:365`](../../../core/components/com_citations/site/views/citations/tmpl/edit.php#L365) with `[ array('tags', 'badges', 'actags1', '', implode(",", $this->badges) ) ]`
- [`core/components/com_courses/admin/views/students/tmpl/add.php:62`](../../../core/components/com_courses/admin/views/students/tmpl/add.php#L62) with `[ array( 'members', // The component to call 'fields[user_id]', // Name of the input field 'acmembers', // ID of the input field '', // C…`
- [`core/components/com_courses/site/views/course/tmpl/edit.php:66`](../../../core/components/com_courses/site/views/course/tmpl/edit.php#L66) with `[array('tags', 'tags', 'actags','', $this->course->tags('string'))]`
- [`core/components/com_courses/site/views/managers/tmpl/display.php:28`](../../../core/components/com_courses/site/views/managers/tmpl/display.php#L28) with `[array('members', 'usernames', 'field-usernames', '', '')]`
- [`core/components/com_developer/admin/views/applications/tmpl/edit.php:125`](../../../core/components/com_developer/admin/views/applications/tmpl/edit.php#L125) with `[array('members', 'team', 'acmembers', '', implode(', ', $currentTeam))]`
- [`core/components/com_developer/site/views/applications/tmpl/edit.php:95`](../../../core/components/com_developer/site/views/applications/tmpl/edit.php#L95) with `[array('members', 'team', 'acmembers')]`
- [`core/components/com_events/site/views/edit/tmpl/default.php:89`](../../../core/components/com_events/site/views/edit/tmpl/default.php#L89) with `[array('tags', 'tags', 'actags','',$this->lists['tags'])]`
- [`core/components/com_groups/site/views/groups/tmpl/edit.php:16`](../../../core/components/com_groups/site/views/groups/tmpl/edit.php#L16) with `[array('tags', 'tags', 'actags','', $this->tags)]`
- and 50 more call sites

Listeners:

- `plg_hubzero_autocompleter` — [`onGetMultiEntry($atts)`](../../../core/plugins/hubzero/autocompleter/autocompleter.php)

## `hubzero.onGetSingleEntry`

Fired from:

- [`core/components/com_messages/admin/views/messages/tmpl/edit.php:31`](../../../core/components/com_messages/admin/views/messages/tmpl/edit.php#L31) with `[ array( 'members', // The component to call 'fields[user_id_to]', // Name of the input field 'field-user_id_to', // ID of the input fiel…`
- [`core/components/com_tools/admin/views/preferences/tmpl/edit.php:45`](../../../core/components/com_tools/admin/views/preferences/tmpl/edit.php#L45) with `[ array( 'members', // The component to call 'fields[user_id]', // Name of the input field 'field-user_id', // ID of the input field '', …`
- [`core/plugins/projects/team/team.php:471`](../../../core/plugins/projects/team/team.php#L471) with `[array('members', 'uid', 'uid')]`

Listeners:

- `plg_hubzero_autocompleter` — [`onGetSingleEntry($atts)`](../../../core/plugins/hubzero/autocompleter/autocompleter.php)

## `hubzero.onGetSingleEntryWithSelect`

Fired from:

- [`core/components/com_publications/site/views/curation/tmpl/assign.php:37`](../../../core/components/com_publications/site/views/curation/tmpl/assign.php#L37) with `[array('members', 'owner', 'owner', '', $selected, '', 'owner')]`
- [`core/components/com_support/admin/views/tickets/tmpl/add.php:68`](../../../core/components/com_support/admin/views/tickets/tmpl/add.php#L68) with `[array('groups', 'ticket[group_id]', 'acgroup','','','','owner')]`
- [`core/components/com_support/admin/views/tickets/tmpl/batch.php:60`](../../../core/components/com_support/admin/views/tickets/tmpl/batch.php#L60) with `[array('groups', 'fields[group]', 'acgroup','','','','owner')]`
- [`core/components/com_support/admin/views/tickets/tmpl/edit.php:558`](../../../core/components/com_support/admin/views/tickets/tmpl/edit.php#L558) with `[array('groups', 'ticket[group_id]', 'acgroup','', $group,'','owner')]`
- [`core/components/com_support/site/views/tickets/tmpl/new.php:171`](../../../core/components/com_support/site/views/tickets/tmpl/new.php#L171) with `[array('groups', 'problem[group_id]', 'acgroup', '', $this->escape($group), '', 'ticketowner')]`
- [`core/components/com_support/site/views/tickets/tmpl/ticket.php:467`](../../../core/components/com_support/site/views/tickets/tmpl/ticket.php#L467) with `[array('groups', 'ticket[group_id]', 'acgroup', '', $group, '', 'ticketowner')]`

Listeners:

- `plg_hubzero_autocompleter` — [`onGetSingleEntryWithSelect($atts)`](../../../core/plugins/hubzero/autocompleter/autocompleter.php)

## `hubzero.onSystemOverview`

Fired from:

- [`core/components/com_system/api/controllers/systemv1_0.php:153`](../../../core/components/com_system/api/controllers/systemv1_0.php#L153) with `[$values]`

Listeners:

- `plg_hubzero_systemplate` — [`onSystemOverview($values = 'all')`](../../../core/plugins/hubzero/systemplate/systemplate.php)
- `plg_hubzero_systickets` — [`onSystemOverview($values = 'all')`](../../../core/plugins/hubzero/systickets/systickets.php)
- `plg_hubzero_sysusers` — [`onSystemOverview($values = 'all')`](../../../core/plugins/hubzero/sysusers/sysusers.php)

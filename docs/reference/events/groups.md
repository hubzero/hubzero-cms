<!--
status: generated
source: Event::trigger('groups.*') call sites and core/plugins/groups/
-->

# Groups events

Events in the `groups` group. A plugin in `core/plugins/groups/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `groups.onAfterStoreGroup`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_groups_projects` — [`onAfterStoreGroup($group)`](../../../core/plugins/groups/projects/projects.php)

## `groups.onBeforeGroup`

Fired from:

- [`core/components/com_groups/helpers/view.php:115`](../../../core/components/com_groups/helpers/view.php#L115) with `[ $group, $authorized, ]`

Listeners:

- `plg_groups_announcements` — [`onBeforeGroup($group, $authorized)`](../../../core/plugins/groups/announcements/announcements.php)

## `groups.onGroup`

Fired from:

- [`core/components/com_groups/helpers/view.php:153`](../../../core/components/com_groups/helpers/view.php#L153) with `[ $group, 'com_groups', $authorized, $limit, $start, $action, $pluginAccess, [$tab], ]`

Listeners:

- `plg_groups_activity` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/activity/activity.php)
- `plg_groups_announcements` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/announcements/announcements.php)
- `plg_groups_blog` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/blog/blog.php)
- `plg_groups_calendar` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/calendar/calendar.php)
- `plg_groups_citations` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/citations/citations.php)
- `plg_groups_collections` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/collections/collections.php)
- `plg_groups_courses` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/courses/courses.php)
- `plg_groups_files` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/files/files.php)
- `plg_groups_forum` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/forum/forum.php)
- `plg_groups_memberoptions` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/memberoptions/memberoptions.php)
- `plg_groups_members` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/members/members.php)
- `plg_groups_messages` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/messages/messages.php)
- `plg_groups_projects` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/projects/projects.php)
- `plg_groups_resources` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/resources/resources.php)
- `plg_groups_usage` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/usage/usage.php)
- `plg_groups_wiki` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/wiki/wiki.php)
- `plg_groups_wishlist` — [`onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)`](../../../core/plugins/groups/wishlist/wishlist.php)

## `groups.onGroupAfterSave`

Fired from:

- [`core/components/com_groups/admin/controllers/manage.php:427`](../../../core/components/com_groups/admin/controllers/manage.php#L427) with `[$before, $group]`
- [`core/components/com_groups/admin/controllers/manage.php:1255`](../../../core/components/com_groups/admin/controllers/manage.php#L1255) with `[$before, $group]`
- [`core/components/com_groups/site/controllers/groups.php:793`](../../../core/components/com_groups/site/controllers/groups.php#L793) with `[$before, $group]`

Listeners:

- `plg_groups_projects` — [`onGroupAfterSave($before, $after)`](../../../core/plugins/groups/projects/projects.php)
- `plg_groups_search` — [`onGroupAfterSave($before, $group)`](../../../core/plugins/groups/search/search.php)

## `groups.onGroupAreas`

Fired from:

- [`core/components/com_groups/api/controllers/groupsv1r0.php:383`](../../../core/components/com_groups/api/controllers/groupsv1r0.php#L383) with `[]`
- [`core/components/com_groups/api/controllers/groupsv1r1.php:371`](../../../core/components/com_groups/api/controllers/groupsv1r1.php#L371)
- [`core/components/com_groups/api/controllers/groupsv1r1.php:393`](../../../core/components/com_groups/api/controllers/groupsv1r1.php#L393) with `[]`
- [`core/components/com_groups/helpers/view.php:86`](../../../core/components/com_groups/helpers/view.php#L86) with `[]`
- [`core/components/com_groups/site/controllers/groups.php:508`](../../../core/components/com_groups/site/controllers/groups.php#L508) with `[]`
- [`core/components/com_groups/site/views/emails/tmpl/saved.php:425`](../../../core/components/com_groups/site/views/emails/tmpl/saved.php#L425) with `[]`
- [`core/components/com_groups/site/views/emails/tmpl/saved_plain.php:124`](../../../core/components/com_groups/site/views/emails/tmpl/saved_plain.php#L124) with `[]`
- [`core/libraries/Hubzero/User/Group/Helper.php:285`](../../../core/libraries/Hubzero/User/Group/Helper.php#L285) with `[]`

No plugin in the source tree listens for this event.

## `groups.onGroupClassroomprojects`

Fired from:

- [`core/plugins/groups/projects/views/browse/tmpl/default.php:98`](../../../core/plugins/groups/projects/views/browse/tmpl/default.php#L98) with `[$this->group]`

No plugin in the source tree listens for this event.

## `groups.onGroupDelete`

Fired from:

- [`core/components/com_groups/admin/controllers/manage.php:1154`](../../../core/components/com_groups/admin/controllers/manage.php#L1154) with `[$group]`
- [`core/components/com_groups/site/controllers/groups.php:1135`](../../../core/components/com_groups/site/controllers/groups.php#L1135) with `[$group]`

Listeners:

- `plg_groups_blog` — [`onGroupDelete($group)`](../../../core/plugins/groups/blog/blog.php)
- `plg_groups_collections` — [`onGroupDelete($group)`](../../../core/plugins/groups/collections/collections.php)
- `plg_groups_forum` — [`onGroupDelete($group)`](../../../core/plugins/groups/forum/forum.php)
- `plg_groups_resources` — [`onGroupDelete($group)`](../../../core/plugins/groups/resources/resources.php)
- `plg_groups_wiki` — [`onGroupDelete($group)`](../../../core/plugins/groups/wiki/wiki.php)
- `plg_groups_wishlist` — [`onGroupDelete($group)`](../../../core/plugins/groups/wishlist/wishlist.php)

## `groups.onGroupDeleteCount`

Fired from:

- [`core/components/com_groups/site/controllers/groups.php:1036`](../../../core/components/com_groups/site/controllers/groups.php#L1036) with `[$this->view->group]`

Listeners:

- `plg_groups_blog` — [`onGroupDeleteCount($group)`](../../../core/plugins/groups/blog/blog.php)
- `plg_groups_collections` — [`onGroupDeleteCount($group)`](../../../core/plugins/groups/collections/collections.php)
- `plg_groups_resources` — [`onGroupDeleteCount($group)`](../../../core/plugins/groups/resources/resources.php)
- `plg_groups_wiki` — [`onGroupDeleteCount($group)`](../../../core/plugins/groups/wiki/wiki.php)
- `plg_groups_wishlist` — [`onGroupDeleteCount($group)`](../../../core/plugins/groups/wishlist/wishlist.php)

## `groups.onGroupMemberAfter`

Fired from:

- [`core/plugins/groups/members/views/profile/tmpl/default.php:232`](../../../core/plugins/groups/members/views/profile/tmpl/default.php#L232) with `[$this->group, $this->profile]`

Listeners:

- `plg_groups_citations` — [`onGroupMemberAfter($group, $profile)`](../../../core/plugins/groups/citations/citations.php)

## `groups.onGroupMemberBefore`

Fired from:

- [`core/plugins/groups/members/views/profile/tmpl/default.php:97`](../../../core/plugins/groups/members/views/profile/tmpl/default.php#L97) with `[$this->group, $this->profile]`

No plugin in the source tree listens for this event.

## `groups.onGroupNew`

Fired from:

- [`core/components/com_groups/site/controllers/groups.php:808`](../../../core/components/com_groups/site/controllers/groups.php#L808) with `[$group]`

No plugin in the source tree listens for this event.

## `groups.onGroupProjects`

Fired from:

- [`core/plugins/groups/projects/views/partials/tmpl/submenu.php:16`](../../../core/plugins/groups/projects/views/partials/tmpl/submenu.php#L16) with `[$this->group]`

No plugin in the source tree listens for this event.

## `groups.onGroupUserEnrollment`

Fired from:

- [`core/components/com_groups/models/orm/group.php:432`](../../../core/components/com_groups/models/orm/group.php#L432) with `[$this->get('gidNumber'), $userid]`
- [`core/libraries/Hubzero/User/Group.php:611`](../../../core/libraries/Hubzero/User/Group.php#L611) with `[$this->gidNumber, $userid]`

Listeners:

- `plg_groups_memberoptions` — [`onGroupUserEnrollment($gidNumber, $userid)`](../../../core/plugins/groups/memberoptions/memberoptions.php)

## `groups.onGroupUserRevocation`

Fired from:

- [`core/libraries/Hubzero/User/Group/Membership.php:623`](../../../core/libraries/Hubzero/User/Group/Membership.php#L623) with `[$gid, $uid, $reason]`

No plugin in the source tree listens for this event.

## `groups.onGroupsApiCreate`

Fired from:

- [`core/components/com_groups/api/controllers/pluginsv1r0.php:170`](../../../core/components/com_groups/api/controllers/pluginsv1r0.php#L170) with `[ $group, $active, ]`

Listeners:

- `plg_groups_announcements` — [`onGroupsApiCreate($group, $active)`](../../../core/plugins/groups/announcements/announcements.php)

## `groups.onGroupsApiDelete`

Fired from:

- [`core/components/com_groups/api/controllers/pluginsv1r0.php:436`](../../../core/components/com_groups/api/controllers/pluginsv1r0.php#L436) with `[ $group, $active, $id, ]`

Listeners:

- `plg_groups_announcements` — [`onGroupsApiDelete($group, $active, $id)`](../../../core/plugins/groups/announcements/announcements.php)

## `groups.onGroupsApiList`

Fired from:

- [`core/components/com_groups/api/controllers/pluginsv1r0.php:95`](../../../core/components/com_groups/api/controllers/pluginsv1r0.php#L95) with `[ $group, $active, $filters['start'], $filters['limit'], ]`

Listeners:

- `plg_groups_announcements` — [`onGroupsApiList($group, $active, $start, $limit)`](../../../core/plugins/groups/announcements/announcements.php)

## `groups.onGroupsApiRead`

Fired from:

- [`core/components/com_groups/api/controllers/pluginsv1r0.php:258`](../../../core/components/com_groups/api/controllers/pluginsv1r0.php#L258) with `[ $group, $active, $id, ]`

Listeners:

- `plg_groups_announcements` — [`onGroupsApiRead($group, $active, $id)`](../../../core/plugins/groups/announcements/announcements.php)

## `groups.onGroupsApiUpdate`

Fired from:

- [`core/components/com_groups/api/controllers/pluginsv1r0.php:347`](../../../core/components/com_groups/api/controllers/pluginsv1r0.php#L347) with `[ $group, $active, $id, ]`

Listeners:

- `plg_groups_announcements` — [`onGroupsApiUpdate($group, $active, $id)`](../../../core/plugins/groups/announcements/announcements.php)

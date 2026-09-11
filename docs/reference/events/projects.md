<!--
status: generated
source: Event::trigger('projects.*') call sites and core/plugins/projects/
-->

# Projects events

Events in the `projects` group. A plugin in `core/plugins/projects/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `projects.onAfterChangeState`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_projects_publications` — [`onAfterChangeState($pub, $originalStatus = 3)`](../../../core/plugins/projects/publications/publications.php)

## `projects.onAfterCreate`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_projects_publications` — [`onAfterCreate($pub)`](../../../core/plugins/projects/publications/publications.php)

## `projects.onAfterInitialise`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_projects_databases` — [`onAfterInitialise()`](../../../core/plugins/projects/databases/databases.php)

## `projects.onAfterSave`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_projects_publications` — [`onAfterSave($pub)`](../../../core/plugins/projects/publications/publications.php)

## `projects.onAfterUpdate`

Fired from:

- [`core/components/com_projects/api/controllers/filefsv1_0.php:869`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L869) with `$plugin_params`
- [`core/components/com_projects/api/controllers/filesv1_0.php:1098`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L1098) with `$plugin_params`

Listeners:

- `plg_projects_files` — [`onAfterUpdate($model = null, $changes = array()`](../../../core/plugins/projects/files/files.php)

## `projects.onProject`

Fired from:

- [`core/components/com_projects/site/controllers/projects.php:657`](../../../core/components/com_projects/site/controllers/projects.php#L657) with `$plugin_params`
- [`core/components/com_projects/site/controllers/setup.php:948`](../../../core/components/com_projects/site/controllers/setup.php#L948) with `[ $this->model, 'save', array('team') ]`
- [`core/components/com_projects/site/controllers/setup.php:1048`](../../../core/components/com_projects/site/controllers/setup.php#L1048) with `[ $this->model, $this->_task, array('team') ]`
- [`core/components/com_publications/site/controllers/publications.php:1546`](../../../core/components/com_publications/site/controllers/publications.php#L1546) with `$plugin_params`
- [`core/plugins/cron/projects/projects.php:144`](../../../core/plugins/cron/projects/projects.php#L144) with `$plugin_params`

Listeners:

- `plg_projects_databases` — [`onProject($model, $action = 'view', $areas = null, $params = array()`](../../../core/plugins/projects/databases/databases.php)
- `plg_projects_feed` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/feed/feed.php)
- `plg_projects_files` — [`onProject($model, $action = '', $areas = null, $params = array()`](../../../core/plugins/projects/files/files.php)
- `plg_projects_hipaacompliant` — [`onProject($model, $action = '', $areas = NULL)`](../../../core/plugins/projects/hipaacompliant/hipaacompliant.php)
- `plg_projects_info` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/info/info.php)
- `plg_projects_links` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/links/links.php)
- `plg_projects_notes` — [`onProject($model, $action = '', $areas = null, $tool = null)`](../../../core/plugins/projects/notes/notes.php)
- `plg_projects_publications` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/publications/publications.php)
- `plg_projects_team` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/team/team.php)
- `plg_projects_todo` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/todo/todo.php)
- `plg_projects_watch` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/watch/watch.php)

## `projects.onProjectAfterDelete`

Fired from:

- [`core/components/com_projects/models/orm/project.php:532`](../../../core/components/com_projects/models/orm/project.php#L532) with `[$data]`

No plugin in the source tree listens for this event.

## `projects.onProjectAfterDeleteActivity`

Fired from:

- [`core/components/com_projects/admin/controllers/activity.php:309`](../../../core/components/com_projects/admin/controllers/activity.php#L309) with `[$id]`

No plugin in the source tree listens for this event.

## `projects.onProjectAfterSave`

Fired from:

- [`core/components/com_projects/admin/controllers/projects.php:543`](../../../core/components/com_projects/admin/controllers/projects.php#L543) with `[$this->model]`
- [`core/components/com_projects/admin/controllers/projects.php:724`](../../../core/components/com_projects/admin/controllers/projects.php#L724) with `[$model]`
- [`core/components/com_projects/admin/controllers/projects.php:782`](../../../core/components/com_projects/admin/controllers/projects.php#L782) with `[$model]`
- [`core/components/com_projects/admin/controllers/projects.php:842`](../../../core/components/com_projects/admin/controllers/projects.php#L842) with `[$model]`
- [`core/components/com_projects/admin/controllers/projects.php:899`](../../../core/components/com_projects/admin/controllers/projects.php#L899) with `[$model]`
- [`core/components/com_projects/api/controllers/projectsv2_0.php:588`](../../../core/components/com_projects/api/controllers/projectsv2_0.php#L588) with `[&$row, $isNew]`
- [`core/components/com_projects/api/controllers/projectsv2_0.php:1100`](../../../core/components/com_projects/api/controllers/projectsv2_0.php#L1100) with `[&$row, $isNew]`
- [`core/components/com_projects/site/controllers/setup.php:351`](../../../core/components/com_projects/site/controllers/setup.php#L351) with `[$this->model]`
- [`core/plugins/groups/projects/projects.php:288`](../../../core/plugins/groups/projects/projects.php#L288) with `[$model]`

No plugin in the source tree listens for this event.

## `projects.onProjectAfterSaveActivity`

Fired from:

- [`core/components/com_projects/admin/controllers/activity.php:261`](../../../core/components/com_projects/admin/controllers/activity.php#L261) with `[&$recipient, $isNew]`
- [`core/components/com_projects/admin/controllers/activity.php:424`](../../../core/components/com_projects/admin/controllers/activity.php#L424) with `[$entry]`

No plugin in the source tree listens for this event.

## `projects.onProjectAreas`

Fired from:

- [`core/components/com_projects/site/controllers/projects.php:614`](../../../core/components/com_projects/site/controllers/projects.php#L614) with `[$this->model->get('alias')]`

No plugin in the source tree listens for this event.

## `projects.onProjectBeforeDelete`

Fired from:

- [`core/components/com_projects/models/orm/project.php:496`](../../../core/components/com_projects/models/orm/project.php#L496) with `[$data]`

No plugin in the source tree listens for this event.

## `projects.onProjectBeforeSave`

Fired from:

- [`core/components/com_projects/api/controllers/projectsv2_0.php:571`](../../../core/components/com_projects/api/controllers/projectsv2_0.php#L571) with `[&$row, $isNew]`
- [`core/components/com_projects/api/controllers/projectsv2_0.php:1087`](../../../core/components/com_projects/api/controllers/projectsv2_0.php#L1087) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `projects.onProjectBeforeSaveActivity`

Fired from:

- [`core/components/com_projects/admin/controllers/activity.php:239`](../../../core/components/com_projects/admin/controllers/activity.php#L239) with `[&$recipient, $isNew]`

No plugin in the source tree listens for this event.

## `projects.onProjectCount`

Fired from:

- [`core/components/com_projects/admin/controllers/projects.php:266`](../../../core/components/com_projects/admin/controllers/projects.php#L266) with `[$model, 1]`
- [`core/components/com_projects/site/controllers/projects.php:682`](../../../core/components/com_projects/site/controllers/projects.php#L682) with `[$this->model]`

Listeners:

- `plg_projects_links` — [`onProjectCount($model)`](../../../core/plugins/projects/links/links.php)

## `projects.onProjectCreate`

Fired from:

- [`core/components/com_projects/api/controllers/projectsv2_0.php:672`](../../../core/components/com_projects/api/controllers/projectsv2_0.php#L672) with `[$row]`
- [`core/components/com_projects/site/controllers/setup.php:611`](../../../core/components/com_projects/site/controllers/setup.php#L611) with `[$this->model]`

No plugin in the source tree listens for this event.

## `projects.onProjectExtras`

Fired from:

- [`core/components/com_projects/site/views/projects/tmpl/internal.php:53`](../../../core/components/com_projects/site/views/projects/tmpl/internal.php#L53) with `[ $this->model, $this->active ]`

Listeners:

- `plg_projects_feed` — [`onProjectExtras($model, $area)`](../../../core/plugins/projects/feed/feed.php)

## `projects.onProjectIntegrationList`

Fired from:

- [`core/components/com_projects/site/views/projects/tmpl/_menu.php:103`](../../../core/components/com_projects/site/views/projects/tmpl/_menu.php#L103) with `[$this->model]`

No plugin in the source tree listens for this event.

## `projects.onProjectMember`

Fired from:

- [`core/plugins/projects/feed/feed.php:212`](../../../core/plugins/projects/feed/feed.php#L212) with `[$model]`

Listeners:

- `plg_projects_watch` — [`onProjectMember($project)`](../../../core/plugins/projects/watch/watch.php)

## `projects.onProjectMiniList`

Fired from:

- [`core/plugins/projects/feed/feed.php:207`](../../../core/plugins/projects/feed/feed.php#L207) with `[$model]`

Listeners:

- `plg_projects_files` — [`onProjectMiniList($model)`](../../../core/plugins/projects/files/files.php)
- `plg_projects_notes` — [`onProjectMiniList($model)`](../../../core/plugins/projects/notes/notes.php)
- `plg_projects_publications` — [`onProjectMiniList($model)`](../../../core/plugins/projects/publications/publications.php)
- `plg_projects_team` — [`onProjectMiniList($model)`](../../../core/plugins/projects/team/team.php)
- `plg_projects_todo` — [`onProjectMiniList($model)`](../../../core/plugins/projects/todo/todo.php)

## `projects.onProjectNotification`

Fired from:

- [`core/components/com_projects/site/views/projects/tmpl/internal.php:46`](../../../core/components/com_projects/site/views/projects/tmpl/internal.php#L46) with `[ $this->model, $this->active ]`

Listeners:

- `plg_projects_feed` — [`onProjectNotification($model, $area)`](../../../core/plugins/projects/feed/feed.php)

## `projects.onProjectPublicList`

Fired from:

- [`core/components/com_projects/site/views/projects/tmpl/external.php:99`](../../../core/components/com_projects/site/views/projects/tmpl/external.php#L99) with `[$this->model]`

Listeners:

- `plg_projects_info` — [`onProjectPublicList($model)`](../../../core/plugins/projects/info/info.php)
- `plg_projects_notes` — [`onProjectPublicList($model)`](../../../core/plugins/projects/notes/notes.php)
- `plg_projects_publications` — [`onProjectPublicList($model)`](../../../core/plugins/projects/publications/publications.php)
- `plg_projects_team` — [`onProjectPublicList($model)`](../../../core/plugins/projects/team/team.php)

## `projects.onProjectsBrowse`

Fired from:

- [`core/components/com_projects/site/views/projects/tmpl/_item.php:272`](../../../core/components/com_projects/site/views/projects/tmpl/_item.php#L272) with `[$this->row]`

No plugin in the source tree listens for this event.

## `projects.onShared`

Fired from:

- [`core/plugins/groups/projects/projects.php:354`](../../../core/plugins/groups/projects/projects.php#L354) with `[ 'feed', $this->model, $this->_projects, User::get('id'), $filters/*, in_array(User::get('id'), $this->group->get('managers')), array( '…`
- [`core/plugins/members/projects/projects.php:250`](../../../core/plugins/members/projects/projects.php#L250) with `[ 'feed', $this->model, $projects, $this->_user->get('id'), $view->filters ]`
- [`core/plugins/members/todo/views/browse/tmpl/default.php:57`](../../../core/plugins/members/todo/views/browse/tmpl/default.php#L57) with `[ 'todo', $this->model, $this->projects, $this->member->get('id'), $this->filters ]`

Listeners:

- `plg_projects_feed` — [`onShared($area, $model, $projects, $uid, $filters)`](../../../core/plugins/projects/feed/feed.php)
- `plg_projects_todo` — [`onShared($area, $model, $projects, $uid, $filters)`](../../../core/plugins/projects/todo/todo.php)

## `projects.onSharedUpdate`

Fired from:

- [`core/plugins/groups/projects/projects.php:419`](../../../core/plugins/groups/projects/projects.php#L419) with `[ $project, $entry, $managers, $posted_by, $posted ]`

Listeners:

- `plg_projects_feed` — [`onSharedUpdate($model, $entry, $managers = 0, $posted_by = 0, $posted = null)`](../../../core/plugins/projects/feed/feed.php)

## `projects.onWatch`

Fired from:

- [`core/components/com_projects/models/project.php:1486`](../../../core/components/com_projects/models/project.php#L1486) with `[$this, $class, array($aid), User::get('id')]`
- [`core/plugins/projects/feed/feed.php:439`](../../../core/plugins/projects/feed/feed.php#L439) with `[ $this->model, ($row->get('parent') ? 'quote' : 'blog'), array($row->get('id')), User::get('id') ]`

Listeners:

- `plg_projects_watch` — [`onWatch($project, $area = '', $activities = array()`](../../../core/plugins/projects/watch/watch.php)

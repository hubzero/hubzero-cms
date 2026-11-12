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

- [`core/components/com_projects/api/controllers/filefsv1r0.php:906`](../../../core/components/com_projects/api/controllers/filefsv1r0.php#L906) with `$plugin_params`
- [`core/components/com_projects/api/controllers/filesv1r0.php:1032`](../../../core/components/com_projects/api/controllers/filesv1r0.php#L1032) with `$plugin_params`

Listeners:

- `plg_projects_files` — [`onAfterUpdate($model = null, $changes = [])`](../../../core/plugins/projects/files/files.php)

## `projects.onProject`

Fired from:

- [`core/components/com_projects/site/controllers/projects.php:615`](../../../core/components/com_projects/site/controllers/projects.php#L615) with `$plugin_params`
- [`core/components/com_projects/site/controllers/setup.php:928`](../../../core/components/com_projects/site/controllers/setup.php#L928) with `[ $this->model, 'save', ['team'], ]`
- [`core/components/com_projects/site/controllers/setup.php:1031`](../../../core/components/com_projects/site/controllers/setup.php#L1031) with `[ $this->model, $this->task, ['team'], ]`
- [`core/components/com_publications/site/controllers/publications.php:1453`](../../../core/components/com_publications/site/controllers/publications.php#L1453) with `$plugin_params`
- [`core/plugins/cron/projects/projects.php:138`](../../../core/plugins/cron/projects/projects.php#L138) with `$plugin_params`

Listeners:

- `plg_projects_databases` — [`onProject($model, $action = 'view', $areas = null, $params = [])`](../../../core/plugins/projects/databases/databases.php)
- `plg_projects_feed` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/feed/feed.php)
- `plg_projects_files` — [`onProject($model, $action = '', $areas = null, $params = [])`](../../../core/plugins/projects/files/files.php)
- `plg_projects_hipaacompliant` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/hipaacompliant/hipaacompliant.php)
- `plg_projects_info` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/info/info.php)
- `plg_projects_links` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/links/links.php)
- `plg_projects_notes` — [`onProject($model, $action = '', $areas = null, $tool = null)`](../../../core/plugins/projects/notes/notes.php)
- `plg_projects_publications` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/publications/publications.php)
- `plg_projects_team` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/team/team.php)
- `plg_projects_todo` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/todo/todo.php)
- `plg_projects_watch` — [`onProject($model, $action = '', $areas = null)`](../../../core/plugins/projects/watch/watch.php)

## `projects.onProjectAfterDelete`

Fired from:

- [`core/components/com_projects/models/orm/project.php:548`](../../../core/components/com_projects/models/orm/project.php#L548) with `[$data]`

No plugin in the source tree listens for this event.

## `projects.onProjectAfterDeleteActivity`

Fired from:

- [`core/components/com_projects/admin/controllers/activity.php:301`](../../../core/components/com_projects/admin/controllers/activity.php#L301) with `[$id]`

No plugin in the source tree listens for this event.

## `projects.onProjectAfterSave`

Fired from:

- [`core/components/com_projects/admin/controllers/projects.php:539`](../../../core/components/com_projects/admin/controllers/projects.php#L539) with `[$this->model]`
- [`core/components/com_projects/admin/controllers/projects.php:709`](../../../core/components/com_projects/admin/controllers/projects.php#L709) with `[$model]`
- [`core/components/com_projects/admin/controllers/projects.php:760`](../../../core/components/com_projects/admin/controllers/projects.php#L760) with `[$model]`
- [`core/components/com_projects/admin/controllers/projects.php:813`](../../../core/components/com_projects/admin/controllers/projects.php#L813) with `[$model]`
- [`core/components/com_projects/admin/controllers/projects.php:864`](../../../core/components/com_projects/admin/controllers/projects.php#L864) with `[$model]`
- [`core/components/com_projects/api/controllers/projectsv2r0.php:566`](../../../core/components/com_projects/api/controllers/projectsv2r0.php#L566) with `[&$row, $isNew]`
- [`core/components/com_projects/api/controllers/projectsv2r0.php:1074`](../../../core/components/com_projects/api/controllers/projectsv2r0.php#L1074) with `[&$row, $isNew]`
- [`core/components/com_projects/site/controllers/setup.php:342`](../../../core/components/com_projects/site/controllers/setup.php#L342) with `[$this->model]`
- [`core/plugins/groups/projects/projects.php:301`](../../../core/plugins/groups/projects/projects.php#L301) with `[$model]`

No plugin in the source tree listens for this event.

## `projects.onProjectAfterSaveActivity`

Fired from:

- [`core/components/com_projects/admin/controllers/activity.php:256`](../../../core/components/com_projects/admin/controllers/activity.php#L256) with `[&$recipient, $isNew]`
- [`core/components/com_projects/admin/controllers/activity.php:401`](../../../core/components/com_projects/admin/controllers/activity.php#L401) with `[$entry]`

No plugin in the source tree listens for this event.

## `projects.onProjectAreas`

Fired from:

- [`core/components/com_projects/site/controllers/projects.php:577`](../../../core/components/com_projects/site/controllers/projects.php#L577) with `[$this->model->get('alias')]`

No plugin in the source tree listens for this event.

## `projects.onProjectBeforeDelete`

Fired from:

- [`core/components/com_projects/models/orm/project.php:519`](../../../core/components/com_projects/models/orm/project.php#L519) with `[$data]`

No plugin in the source tree listens for this event.

## `projects.onProjectBeforeSave`

Fired from:

- [`core/components/com_projects/api/controllers/projectsv2r0.php:551`](../../../core/components/com_projects/api/controllers/projectsv2r0.php#L551) with `[&$row, $isNew]`
- [`core/components/com_projects/api/controllers/projectsv2r0.php:1063`](../../../core/components/com_projects/api/controllers/projectsv2r0.php#L1063) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `projects.onProjectBeforeSaveActivity`

Fired from:

- [`core/components/com_projects/admin/controllers/activity.php:231`](../../../core/components/com_projects/admin/controllers/activity.php#L231) with `[&$recipient, $isNew]`

No plugin in the source tree listens for this event.

## `projects.onProjectCount`

Fired from:

- [`core/components/com_projects/admin/controllers/projects.php:274`](../../../core/components/com_projects/admin/controllers/projects.php#L274) with `[$model, 1]`
- [`core/components/com_projects/site/controllers/projects.php:634`](../../../core/components/com_projects/site/controllers/projects.php#L634) with `[$this->model]`

Listeners:

- `plg_projects_links` — [`onProjectCount($model)`](../../../core/plugins/projects/links/links.php)

## `projects.onProjectCreate`

Fired from:

- [`core/components/com_projects/api/controllers/projectsv2r0.php:649`](../../../core/components/com_projects/api/controllers/projectsv2r0.php#L649) with `[$row]`
- [`core/components/com_projects/site/controllers/setup.php:583`](../../../core/components/com_projects/site/controllers/setup.php#L583) with `[$this->model]`

No plugin in the source tree listens for this event.

## `projects.onProjectExtras`

Fired from:

- [`core/components/com_projects/site/views/projects/tmpl/internal.php:72`](../../../core/components/com_projects/site/views/projects/tmpl/internal.php#L72) with `[ $this->model, $this->active ]`

Listeners:

- `plg_projects_feed` — [`onProjectExtras($model, $area)`](../../../core/plugins/projects/feed/feed.php)

## `projects.onProjectIntegrationList`

Fired from:

- [`core/components/com_projects/site/views/projects/tmpl/_menu.php:149`](../../../core/components/com_projects/site/views/projects/tmpl/_menu.php#L149) with `[$this->model]`

No plugin in the source tree listens for this event.

## `projects.onProjectMember`

Fired from:

- [`core/plugins/projects/feed/feed.php:223`](../../../core/plugins/projects/feed/feed.php#L223) with `[$model]`

Listeners:

- `plg_projects_watch` — [`onProjectMember($project)`](../../../core/plugins/projects/watch/watch.php)

## `projects.onProjectMiniList`

Fired from:

- [`core/plugins/projects/feed/feed.php:219`](../../../core/plugins/projects/feed/feed.php#L219) with `[$model]`

Listeners:

- `plg_projects_files` — [`onProjectMiniList($model)`](../../../core/plugins/projects/files/files.php)
- `plg_projects_notes` — [`onProjectMiniList($model)`](../../../core/plugins/projects/notes/notes.php)
- `plg_projects_publications` — [`onProjectMiniList($model)`](../../../core/plugins/projects/publications/publications.php)
- `plg_projects_team` — [`onProjectMiniList($model)`](../../../core/plugins/projects/team/team.php)
- `plg_projects_todo` — [`onProjectMiniList($model)`](../../../core/plugins/projects/todo/todo.php)

## `projects.onProjectNotification`

Fired from:

- [`core/components/com_projects/site/views/projects/tmpl/internal.php:63`](../../../core/components/com_projects/site/views/projects/tmpl/internal.php#L63) with `[ $this->model, $this->active ]`

Listeners:

- `plg_projects_feed` — [`onProjectNotification($model, $area)`](../../../core/plugins/projects/feed/feed.php)

## `projects.onProjectPublicList`

Fired from:

- [`core/components/com_projects/site/views/projects/tmpl/external.php:135`](../../../core/components/com_projects/site/views/projects/tmpl/external.php#L135) with `[$this->model]`

Listeners:

- `plg_projects_info` — [`onProjectPublicList($model)`](../../../core/plugins/projects/info/info.php)
- `plg_projects_notes` — [`onProjectPublicList($model)`](../../../core/plugins/projects/notes/notes.php)
- `plg_projects_publications` — [`onProjectPublicList($model)`](../../../core/plugins/projects/publications/publications.php)
- `plg_projects_team` — [`onProjectPublicList($model)`](../../../core/plugins/projects/team/team.php)

## `projects.onProjectsBrowse`

Fired from:

- [`core/components/com_projects/site/views/projects/tmpl/_item.php:340`](../../../core/components/com_projects/site/views/projects/tmpl/_item.php#L340) with `[$this->row]`

No plugin in the source tree listens for this event.

## `projects.onShared`

Fired from:

- [`core/plugins/groups/projects/projects.php:364`](../../../core/plugins/groups/projects/projects.php#L364) with `[ 'feed', $this->model, $this->projects, User::get('id'), $filters,/*, in_array(User::get('id'), $this->group->get('managers')), array( '…`
- [`core/plugins/members/projects/projects.php:252`](../../../core/plugins/members/projects/projects.php#L252) with `[ 'feed', $this->model, $projects, $this->user->get('id'), $view->filters, ]`
- [`core/plugins/members/todo/views/browse/tmpl/default.php:78`](../../../core/plugins/members/todo/views/browse/tmpl/default.php#L78) with `[ 'todo', $this->model, $this->projects, $this->member->get('id'), $this->filters, ]`

Listeners:

- `plg_projects_feed` — [`onShared($area, $model, $projects, $uid, $filters)`](../../../core/plugins/projects/feed/feed.php)
- `plg_projects_todo` — [`onShared($area, $model, $projects, $uid, $filters)`](../../../core/plugins/projects/todo/todo.php)

## `projects.onSharedUpdate`

Fired from:

- [`core/plugins/groups/projects/projects.php:434`](../../../core/plugins/groups/projects/projects.php#L434) with `[ $project, $entry, $managers, $posted_by, $posted, ]`

Listeners:

- `plg_projects_feed` — [`onSharedUpdate($model, $entry, $managers = 0, $posted_by = 0, $posted = null)`](../../../core/plugins/projects/feed/feed.php)

## `projects.onWatch`

Fired from:

- [`core/components/com_projects/models/project.php:1400`](../../../core/components/com_projects/models/project.php#L1400) with `[$this, $class, [$aid], User::get('id')]`
- [`core/plugins/projects/feed/feed.php:449`](../../../core/plugins/projects/feed/feed.php#L449) with `[ $this->model, ($row->get('parent') ? 'quote' : 'blog'), [$row->get('id')], User::get('id'), ]`

Listeners:

- `plg_projects_watch` — [`onWatch($project, $area = '', $activities = [], $actor = 0)`](../../../core/plugins/projects/watch/watch.php)

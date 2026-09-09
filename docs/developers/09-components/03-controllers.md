<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/components/controllers
-->
# Controllers

A controller answers a request. It reads the incoming variables, asks a model
for data or tells it to change, and either hands the result to a view or
redirects. There are three base classes —
[`SiteController`](../../../core/libraries/Hubzero/Component/SiteController.php),
[`AdminController`](../../../core/libraries/Hubzero/Component/AdminController.php),
and [`ApiController`](../../../core/libraries/Hubzero/Component/ApiController.php) —
and all three implement `Hubzero\Component\ControllerInterface`, whose single
method is `execute()`.

## Tasks

A controller's public methods whose names end in `Task` are its tasks, and
nothing else is callable from the outside. The constructor reflects over the
class and builds a map of task name to method name, dropping the suffix and
lowercasing the key. Methods inherited from `SiteController` are excluded, with
one exception: `displayTask` stays available.

So `otherTask()` is reached by `task=other`, and `savecommentTask()` by
`task=savecomment`. Lookup is case-insensitive.

`execute()` reads the task from the request, preferring `task` and falling
back to `layout`:

```php
$this->_task = strtolower(Request::getCmd('task', Request::getWord('layout', '')));
```

If the task is not in the map, the controller runs `__default`, which is
`display` unless you change it.

> **Note:** An unknown task therefore renders the default view rather than
> returning 404. Call `$this->disableDefaultTask()` if you would rather an
> unrecognised task fail; with `__default` unregistered, `execute()` throws
> `InvalidTaskException` with a 404 status.

## A site controller

<!--include: core/components/com_kb/site/controllers/articles.php:8-30-->

The facade imports at the top are not decoration. Inside a namespace an
unqualified `Route` resolves to
`Components\Kb\Site\Controllers\Route` first, which does not exist, and the
call is a fatal error the moment it runs. Import every facade the file uses,
or fully qualify it as `\Route::url()`. See
[Facades](../03-foundation/04-facades.md#importing-a-facade).

A task takes no arguments and returns nothing; it produces output or a
redirect:

<!--include: core/components/com_kb/site/controllers/articles.php:44-55-->

## Properties

Each controller has:

| Property | What it holds |
|---|---|
| `$this->_name` | the component name without the prefix, e.g. `kb` |
| `$this->_option` | the full component name, e.g. `com_kb` |
| `$this->_controller` | the lowercased short class name, e.g. `articles` |
| `$this->_task` | the task as it arrived in the request |
| `$this->_basePath` | the client directory, e.g. `.../com_kb/site` |
| `$this->view` | the `Hubzero\Component\View` built for this task |
| `$this->config` | the component's parameters, a `Hubzero\Config\Registry` |

`SiteController` extends `Hubzero\Base\Obj`, so `get()`, `set()`,
`setError()`, `getErrors()`, and the rest of that class are available. Any
other property you assign is stored in an internal array through `__set()` and
read back through `__get()`, which is why `$this->archive = new Archive()` in
`execute()` above works without a declared property.

`$this->juser` and `$this->database` are still set by the constructor and are
still marked deprecated. Use the `User` facade and `App::get('db')`.

## The view

`execute()` builds the view before calling the task. Its name is the
controller name and its layout is the task name, so a controller `Articles`
running task `category` renders
`site/views/articles/tmpl/category.php`. The view is pre-loaded with
`option`, `task`, and `controller`.

Change the layout when a task needs to render something other than its own
name — most often when `saveTask()` fails validation and falls through to the
edit form:

```php
$this->view
    ->set('row', $row)
    ->setLayout('edit')
    ->display();
```

Assigned data survives a `setLayout()` call. To change the view directory as
well, call `$this->setView($name, $layout)`, which replaces `$this->view`
entirely — assign your data after that call, not before.

Override `_onBeforeDoTask()` to run something between building the view and
calling the task.

## Remapping tasks

`registerTask($task, $method)` points one more task name at a method that is
already a task. `com_kb`'s administrator controller uses it so that **New**
reuses the edit form, **Apply** reuses the save method, and **Publish** and
**Unpublish** share one state method. The calls go in an overridden
`execute()`, because the constructor has already built the map by the time it
runs:

<!--include: core/components/com_kb/admin/controllers/articles.php:27-40-->

`registerDefaultTask($method)` is the same call with a task of `__default`,
and `unregisterTask($task)` removes a mapping.

> **Note:** `registerTask()` only acts if `$method` is already a value in the
> task map, compared case-sensitively. The values are method names with the
> `Task` suffix removed and their original case kept, so
> `registerTask('add', 'edit')` works for `editTask()` but
> `registerTask('add', 'editEntry')` does nothing at all for `editEntryTask()`:
> the check is `in_array(strtolower($method), $this->_taskMap)`, which
> lowercases the argument but not the stored value. The call fails silently.

## Administrator controllers

`AdminController` extends `SiteController` and adds exactly one thing, a
cancel task that returns to the controller's default view:

<!--include: core/libraries/Hubzero/Component/AdminController.php:13-27-->

Everything else — the toolbar, the sub-menu, permission checks — is written by
the component. Administrator tasks conventionally check `User::authorise()`
before doing anything and call `Request::checkToken()` on anything that
writes.

## API controllers

`ApiController` is a separate base class, not a `SiteController`. Its
constructor takes the response object, its default task is `index` rather
than `display`, and there is no view — a task calls `$this->send($data)` and,
optionally, a status code.

Controller files are versioned. The API loader takes the controller name from
the request or the third URL segment and appends `v{major}_{minor}`; with no
`version` variable in the request it globs
`api/controllers/{controller}v*.php` and takes the highest. So
`api/controllers/entriesv1_0.php` defines
`Components\Kb\Api\Controllers\Entriesv1_0` and answers `/api/kb/list`.

Tasks are documented in their docblock, and the base `indexTask()` reads those
docblocks back with reflection to publish the endpoint list:

<!--include: core/components/com_kb/api/controllers/entriesv1_0.php:24-45-->

A task that changes anything should call `$this->requiresAuthentication()`
first; it aborts with 403 when the request carries no authenticated user.

`ApiController` also ships generic `listTask()`, `createTask()`, `readTask()`,
`updateTask()`, and `deleteTask()` implementations that work off a
`Hubzero\Database\Relational` model — by default the singular of the
controller name, in `Components\{Name}\Models`. Set `$_model` to a fully
qualified class name to point them somewhere else.

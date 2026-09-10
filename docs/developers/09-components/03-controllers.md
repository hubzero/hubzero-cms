<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
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

Keep controllers thin. A task should read the request, call a model, and pick
a view; the rules about what a reservation *is* belong on the model, where the
administrator side and the site side both get them. A rule written into a site
task is a rule the administrator screens do not enforce.

## The smallest one

```php
namespace Components\Bookings\Site\Controllers;

use Hubzero\Component\SiteController;

class Instruments extends SiteController
{
	public function displayTask()
	{
		$this->view->display();
	}
}
```

That is a working controller. `/index.php?option=com_bookings` runs
`displayTask()`, which renders
`site/views/instruments/tmpl/display.php`. Nothing else is registered
anywhere.

## Tasks

A controller's public methods whose names end in `Task` are its tasks, and
nothing else is callable from the outside. The constructor reflects over the
class and builds a map of task name to method name, dropping the suffix and
lowercasing the key. Methods inherited from `SiteController` are excluded, with
one exception: `displayTask` stays available.

So `bookTask()` is reached by `task=book`, and `cancelbookingTask()` by
`task=cancelbooking`. Lookup is case-insensitive.

The suffix is the whole of the access control on method visibility. A public
method without it — a helper you meant to keep to yourself — is unreachable
from a URL; a public method *with* it is reachable by anyone who can guess the
name, whether or not you linked to it. Every task decides for itself who may
run it.

`execute()` reads the task from the request, preferring `task` and falling
back to `layout`:

```php
$this->_task = strtolower(Request::getCmd('task', Request::getWord('layout', '')));
```

If the task is not in the map, the controller runs `__default`, which is
`display` unless you change it.

> **Note:** An unknown task therefore renders the default view rather than
> returning 404. `task=delete` misspelled as `task=delele` shows the listing
> and looks like a page that quietly did nothing. Call
> `$this->disableDefaultTask()` if you would rather an unrecognised task fail;
> with `__default` unregistered, `execute()` throws `InvalidTaskException`
> with a 404 status.

## A site controller

<!--include: core/components/com_kb/site/controllers/articles.php:8-30-->

The facade imports at the top are not decoration. Inside a namespace an
unqualified `Route` resolves to
`Components\Kb\Site\Controllers\Route` first, which does not exist, and the
call is a fatal error the moment it runs. Import every facade the file uses,
or fully qualify it as `\Route::url()`. See
[Facades](../03-foundation/06-facades.md#importing-a-facade).

The failure is worth picturing, because it is the most common one in this
tree: the file parses, the page loads, and one branch — the error path, the
"you already have a reservation" path — dies with
`Class "Components\Bookings\Site\Controllers\Lang" not found`. Run
`php tools/lint/missing-facade-imports.php` rather than waiting to find out.

> **Warning:** Not every facade exists in every client. `Toolbar` and
> `Submenu` are declared only in
> [`core/bootstrap/Administrator/aliases.php`](../../../core/bootstrap/Administrator/aliases.php);
> `Pathway` only in
> [`core/bootstrap/Site/aliases.php`](../../../core/bootstrap/Site/aliases.php).
> The API client has neither, and no `Notify`, `Document` or `Html` either.
> Importing `Toolbar` into a site controller is a correct-looking `use`
> statement for a class that is never registered.

A task takes no arguments and returns nothing; it produces output or a
redirect:

<!--include: core/components/com_kb/site/controllers/articles.php:44-55-->

## Properties

Each controller has:

| Property | What it holds |
|---|---|
| `$this->_name` | the component name without the prefix, e.g. `bookings` |
| `$this->_option` | the full component name, e.g. `com_bookings` |
| `$this->_controller` | the lowercased short class name, e.g. `instruments` |
| `$this->_task` | the task as it arrived in the request |
| `$this->_basePath` | the client directory, e.g. `.../com_bookings/site` |
| `$this->view` | the `Hubzero\Component\View` built for this task |
| `$this->config` | the component's parameters, a `Hubzero\Config\Registry` |

`$this->_name` and `$this->_option` come from the class namespace, not from
the request — see [Structure](02-structure.md#the-name-is-load-bearing).

`SiteController` extends `Hubzero\Base\Obj`, so `get()`, `set()`,
`setError()`, `getErrors()`, and the rest of that class are available. Any
other property you assign is stored in an internal array through `__set()` and
read back through `__get()`, which is why `$this->archive = new Archive()` in
`execute()` above works without a declared property. The cost of that
convenience is that a typo in a property name is not an error: `$this->intrument`
reads back `null`, and the page renders empty.

`$this->juser` and `$this->database` are still set by the constructor and are
still marked deprecated. Use the `User` facade and `App::get('db')`.

## The view

`execute()` builds the view before calling the task. Its name is the
controller name and its layout is the task name, so a controller `Instruments`
running task `book` renders
`site/views/instruments/tmpl/book.php`. The view is pre-loaded with
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
entirely — assign your data after that call, not before. Assigning before is a
silent loss: the new view has none of it, and the layout renders blanks.

Override `_onBeforeDoTask()` to run something between building the view and
calling the task.

## Redirecting after a write

A task that changes something should redirect rather than render, so that a
reload does not repeat the write:

```php
App::redirect(
	Route::url('index.php?option=' . $this->_option . '&controller=reservations', false),
	Lang::txt('COM_BOOKINGS_RESERVATION_SAVED')
);
```

Pass `false` as the second argument to `Route::url()` here. The encoded form is
for markup; an encoded `&amp;` in a `Location` header produces a URL with a
literal `amp;` in a variable name. See [Redirects](../05-basics/11-redirect.md).

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
> lowercases the argument but not the stored value. The call fails silently,
> and `task=add` falls through to the default view.

## Administrator controllers

`AdminController` extends `SiteController` and adds exactly one thing, a
cancel task that returns to the controller's default view:

<!--include: core/libraries/Hubzero/Component/AdminController.php:13-27-->

Everything else — the toolbar, the sub-menu, permission checks — is written by
the component. Two habits are not optional:

- Check `User::authorise()` in the task, against the component or the record.
  The `core.manage` gate in the entry point decides who may open the client at
  all; it says nothing about who may delete an instrument.
- Call `Request::checkToken()` at the top of anything that writes. Without it
  a `GET` from another site can delete records as whoever is logged in.

The entry point's habit of building a controller class name out of
`Request::getCmd('controller')` is why `com_kb` checks `file_exists()` on the
path before using it. Copy the check with the rest of the entry point.

## API controllers

`ApiController` is a separate base class, not a `SiteController`. Its
constructor takes the response object, its default task is `index` rather
than `display`, and there is no view — a task calls `$this->send($data)` and,
optionally, a status code. `Notify`, `Toolbar` and `Pathway` do not exist in
this client.

Controller files are versioned. The API loader takes the controller name from
the request or the third URL segment and appends `v{major}_{minor}`; with no
`version` variable in the request it globs
`api/controllers/{controller}v*.php` and takes the highest. So
`api/controllers/entriesv1_0.php` defines
`Components\Kb\Api\Controllers\Entriesv1_0` and answers `/api/kb/list`.

Tasks are documented in their docblock, and the base `indexTask()` reads those
docblocks back with reflection to publish the endpoint list:

<!--include: core/components/com_kb/api/controllers/entriesv1_0.php:24-45-->

That means the docblock is the API documentation, not a comment about it. An
endpoint with no docblock is an endpoint nobody discovers.

A task that changes anything should call `$this->requiresAuthentication()`
first; it aborts with 403 when the request carries no authenticated user.

`ApiController` also ships generic `listTask()`, `createTask()`, `readTask()`,
`updateTask()`, and `deleteTask()` implementations that work off a
`Hubzero\Database\Relational` model — by default the singular of the
controller name, in `Components\{Name}\Models`. Set `$_model` to a fully
qualified class name to point them somewhere else.

<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/foundation/facades
-->
# Facades

`Route`, `Lang`, `User`, `Request`, `Config`, `Event`. These are the names
you will type most often, and they are the shortest thing in the framework to
explain and the easiest thing in the framework to get wrong.

A facade is a short, root-namespace name standing in front of a service in
the application container. `Route::url()` looks like a static method on a
class called `Route`. There is no such method and, until the moment you call
it, no such class. The name is an alias; the call is forwarded to whatever
object the container holds under `router`.

**If you read one section of this chapter, read
[Importing a facade](#importing-a-facade).** It is the
single most common defect in this codebase's history: 730 unimported facade
calls were fixed in one pass, and a linter now runs in CI to keep them from
coming back.

## The smallest working thing

```php
namespace Components\Booking\Site\Controllers;

use Hubzero\Component\SiteController;
use Route;
use Lang;
use Request;

class Instruments extends SiteController
{
    public function displayTask()
    {
        $id  = Request::getInt('id', 0);
        $url = Route::url('index.php?option=com_booking&id=' . $id);

        $this->view
            ->set('title', Lang::txt('COM_BOOKING_INSTRUMENTS'))
            ->set('url', $url)
            ->display();
    }
}
```

Three facades, three imports, and nothing else to wire up. That is the whole
usage pattern.

## Why they exist

Everything a facade reaches is in the container and can be reached without
one:

```php
$url = App::get('router')->url('index.php?option=com_booking');   // the service
$url = Route::url('index.php?option=com_booking');                // the facade
```

The second is the house style, for two reasons.

**It stays readable.** A controller that reads configuration, translates a
string, routes a URL and checks a user does four container lookups per line
otherwise.

**It stays swappable.** The facade resolves the key *each time it is called*,
so a test can put a double in the container under the same key and every call
site picks it up, with no constructor to thread the fake through:

```php
Route::swap($fakeRouter);   // replaces the container's 'router' binding
```

`swap()` is on the base class and does exactly that. This is the practical
argument for a facade over `new Hubzero\Routing\Router(...)` in your own
code: the object you construct yourself is the one nobody can replace.

Reach for the container directly (`App::get('db')`) only where there is no
facade. There is no facade for the database driver, and none for a named log
other than `debug` — `Hubzero\Log\Manager::__call()` forwards every static
`Log::` call to the logger named `debug`, so a component log goes through
`App::get('log')->logger('booking')`.

## Importing a facade

The aliases are registered in the **root** namespace. Almost every file you
write declares a namespace, and inside a namespace an unqualified class name
resolves against the current namespace first. So this:

```php
namespace Components\Booking\Site\Controllers;

class Instruments extends SiteController
{
    public function displayTask()
    {
        // PHP looks for Components\Booking\Site\Controllers\Route
        $url = Route::url('index.php?option=com_booking');
    }
}
```

does not ask for the `Route` alias at all. It asks for
`Components\Booking\Site\Controllers\Route`.

Import it, and the name means the alias:

```php
namespace Components\Booking\Site\Controllers;

use Route;
use Lang;
use App;
```

Writing `\Route::url(...)` works equally well. The import is the house style
because it puts every global name a file depends on at the top of the file.

### What actually happens when you forget

Worth knowing exactly, because the behaviour is not the one the warning
implies and the difference explains why this bug class survived so long.

PHP asks the autoloaders for `Components\Booking\Site\Controllers\Route`.
[`ClassLoader`](../../../core/libraries/Hubzero/Base/ClassLoader.php) looks
for a matching file and finds none. Then the alias autoloader that
`Facade::createAliases()` registered gets its turn, and it does not only
match whole names — it takes the **last segment** of the name and looks that
up too:

<!--include: core/libraries/Hubzero/Facades/Facade.php:120-137-->

`Route` is in the alias map, so it aliases the namespaced name to the facade
and the call proceeds. Most of the time, on a site request, an unimported
facade quietly works. That is why nothing caught these for years.

It stops working in three situations, and all three are worse than an error
at the point of the mistake:

1. **The client does not register that alias.** Each client has its own list
   — `core/bootstrap/Site/aliases.php` has twenty, `Api` and `Cli` have
   fourteen and thirteen. `Notify`, `Document`, `Module`, `Pathway`, `Cache`
   and `Html` are site and administrator only; `Session` is not in the CLI
   list. A model shared between a component's site and API halves that calls
   an unimported `Document::setTitle()` is fine on the site and fatal under
   `/api/`:

   ```text
   PHP Fatal error:  Uncaught Error: Class "Components\Booking\Models\Document" not found
   ```

   Note which class name the message reports. It is not `Document`, and
   searching the tree for that class finds nothing, which is why this error
   reads as a mystery the first time.

2. **A real class of that name exists in the same namespace.** The class
   loader runs first and wins. `Components\Events\Models\Event`,
   `Components\Publications\Models\Log`, `Components\Groups\Models\Module`
   and `Components\Wishlist\Models\Adapters\User` are all real classes in
   this tree. An unimported `Event::trigger()` written in
   `Components\Events\Models` loads the model instead of the facade and dies
   differently:

   ```text
   PHP Fatal error:  Uncaught Error: Call to undefined method
   Components\Events\Models\Event::trigger()
   ```

   Nothing warns you. The two names are indistinguishable in the source.

3. **The aliases are not registered yet.** They are registered by
   `Application::load()`. Code that runs before that — an installer step, a
   bootstrap-time helper — has no aliases to fall back on.

To all of which add the ordinary cost: every unimported call runs a failed
sweep of both trees before the alias autoloader answers.

> **Warning:** Nothing catches a missing import at parse time. The file is
> valid PHP, the syntax check passes, and where the fallback does not save it
> the failure appears only when that line runs. An error path taken once a
> year can carry the fault for years — which is exactly where most of the 730
> were found.

### The linter

```bash
php tools/lint/missing-facade-imports.php            # scans components, plugins, modules, libraries
php tools/lint/missing-facade-imports.php --fix      # inserts the missing use statements
php tools/lint/missing-facade-imports.php core/components/com_booking
```

Paths are relative to the repository root. With no path it scans
`core/components`, `core/plugins`, `core/modules` and
`core/libraries/Hubzero`, and it exits non-zero when it finds anything, which
is how `.github/workflows/php-lint.yml` gates every push and pull request.

It reads the file with PHP's own tokenizer rather than matching text, so a
facade name inside a comment, a string or a heredoc is not counted, and a
name the file declares itself — or that another file declares in the same
namespace — is left alone. Run it before you send a change; the fix it
applies is the `use` line you forgot.

> **Note:** Plugin entry files are the one place the rule does not bite.
> `plgContentFormathtml` and the rest are declared in the root namespace, so
> an unqualified `Route::url()` in them already means the alias. Their
> namespaced helper classes are subject to the rule like anything else.

## The facades

| Facade | Container key | Behind it | Typical call |
|---|---|---|---|
| `App` | `app` | `Hubzero\Base\Application` | `App::get('db')`, `App::abort(404)`, `App::redirect($url)` |
| `Cache` | `cache.store` | a `Hubzero\Cache\Storage\*` adapter, chosen by the hub's cache handler | `Cache::get($key)` |
| `Component` | `component` | `Hubzero\Component\Loader` | `Component::params('com_booking')` |
| `Config` | `config` | `Hubzero\Config\Repository` | `Config::get('sitename')` |
| `Date` | none — see below | `Hubzero\Utility\Date` | `Date::of($row->created)->toLocal('d M Y')` |
| `Document` | `document` | `Hubzero\Document\Manager` | titles, stylesheets, scripts, feeds |
| `Event` | `dispatcher` | `Hubzero\Events\Dispatcher` | `Event::trigger('booking.onBookingAfterSave', [...])` |
| `Filesystem` | `filesystem` | `Hubzero\Filesystem\Filesystem` | `Filesystem::exists($path)` |
| `Html` | `html.builder` | `Hubzero\Html\Builder` | `Html::grid('sort', ...)` |
| `Lang` | `language` | `Hubzero\Language\Translator` | `Lang::txt('COM_BOOKING_TITLE')` |
| `Log` | `log` | `Hubzero\Log\Manager`, forwarding to the `debug` logger | `Log::debug($message)` |
| `Module` | `module` | `Hubzero\Module\Loader` | |
| `Notify` | `notification` | `Hubzero\Notification\Handler` | `Notify::success($message)` |
| `Pathway` | `pathway` | `Hubzero\Pathway\Trail` | breadcrumbs |
| `Plugin` | `plugin` | `Hubzero\Plugin\Loader` | `Plugin::byType('booking')` |
| `Request` | `request` | `Hubzero\Http\Request` | `Request::getInt('id', 0)` |
| `Response` | `response` | `Hubzero\Http\Response` | |
| `Route` | `router` | `Hubzero\Routing\Manager` | `Route::url('index.php?option=com_booking')` |
| `Session` | `session` | `Hubzero\Session\Manager` | |
| `Submenu` | `submenu` | `Hubzero\Html\Toolbar` | administrator only |
| `Toolbar` | `toolbar` | `Hubzero\Html\Toolbar` | administrator only |
| `User` | `user` | `Hubzero\User\Manager` | `User::get('id')`, `User::isGuest()` |

Not every client registers every one. The site list is
[`core/bootstrap/Site/aliases.php`](../../../core/bootstrap/Site/aliases.php);
each client has its own beside it, and a hub can add to the list without
touching the platform by dropping an `aliases.php` into
`app/bootstrap/<client>/`, which is merged over the core one.

> **Note:** Any static call on `User` other than `getInstance()` acts on the
> *current* user. `User::get('name')` is the visitor's name, not a lookup.
> `User::getInstance($id)` returns another account.

`Date` is the exception to everything above. It declares the accessor `date`,
but no provider registers that key and the facade overrides `getRoot()` to
construct a `Hubzero\Utility\Date` on the spot. So it is a static helper
wearing a facade's clothes: `Date::swap()` sets a container key nothing
reads, and a test cannot replace it.

## Writing one

You need a facade only when you have added a service of your own and will
call it from many places. It is one method:

```php
namespace Components\Booking\Facades;

use Hubzero\Facades\Facade;

class Calendar extends Facade
{
    protected static function getAccessor()
    {
        return 'booking.calendar';
    }
}
```

`getAccessor()` returns the container key. The base class's `__callStatic()`
does the rest: it resolves the key, calls the method on the object, and
returns the result. Register the key from a
[service provider](07-providers.md), and add the alias in
`app/bootstrap/site/aliases.php` if you want the short root-namespace name —
otherwise import the class by its full name like anything else.

A facade with no service behind it fails at the call, not at boot:

```text
RuntimeException: Facade does not implement getAccessor method.
```

is the base class refusing a subclass that forgot the method. A key no
provider registered fails in the container instead, with
`InvalidArgumentException: Identifier "booking.calendar" is not defined.` —
which usually means the provider is missing from the client's
`services.php`, not that the facade is wrong.

## When not to use one

- **In a library class that could be used outside a request.** Anything under
  `core/libraries/Hubzero/` that a CLI job or a test may construct is better
  taking its dependency as a constructor argument. A facade needs a booted
  application; a constructor argument needs nothing.
- **When you need a specific instance.** `Log` writes the `debug` log and
  nothing else. Any other log goes through
  `App::get('log')->logger('booking')`.
- **When there is no facade.** The database driver is `App::get('db')`. Do
  not construct your own connection to get a shorter name.

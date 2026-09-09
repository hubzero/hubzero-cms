<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/components/helpers
-->
# Helpers

A helper is a class that holds work several controllers or views need but that
belongs to none of them: building a `<select>` from a list of categories,
resolving what the current user is allowed to do, formatting something for
display. Helpers keep that code in one place.

## Where they go

A component may have helpers in two places:

| Directory | Namespace | Scope |
|---|---|---|
| `com_kb/helpers/` | `Components\Kb\Helpers` | shared by every client |
| `com_kb/admin/helpers/` | `Components\Kb\Admin\Helpers` | administrator only |
| `com_kb/site/helpers/` | `Components\Kb\Site\Helpers` | site only |

Put a helper at the top level when more than one client uses it, and inside a
client when it is bound to that client's interface — an administrator toolbar
or form control has no business in the site's namespace.

Both are autoloaded by
[`Hubzero\Base\ClassLoader`](../../../core/libraries/Hubzero/Base/ClassLoader.php),
so `Components\Kb\Admin\Helpers\Permissions` is found at
`core/components/com_kb/admin/helpers/permissions.php` with no `require`. Some
shipped components still `require_once` their helpers from the entry point;
that is left over from before the class loader and is not needed in new code.

## A static helper

The common shape is a class of static methods with no state. `com_kb`
resolves the current user's permissions this way, and both administrator views
call it to decide which toolbar buttons to draw:

<!--include: core/components/com_kb/admin/helpers/permissions.php:8-59-->

Used from a view:

```php
$canDo = Components\Kb\Admin\Helpers\Permissions::getActions('article');

if ($canDo->get('core.create'))
{
    Toolbar::addNew();
}
```

`com_kb`'s other helper, `Components\Kb\Admin\Helpers\Html`, is the same
pattern applied to markup: one static method that renders the category
`<select>` used by the edit form, so the list and the indentation rules are
written once.

> **Note:** A namespaced helper needs its own facade imports. `Permissions`
> imports `User` at the top of the file; without that, `User::authorise()`
> would resolve to `Components\Kb\Admin\Helpers\User` and fatal. See
> [Facades](../03-foundation/04-facades.md#importing-a-facade).

## A helper with state

Nothing requires a helper to be static. When a helper needs configuration or a
database handle, make it an ordinary object and extend `Hubzero\Base\Obj` so it
inherits `get()`, `set()`, and the error bag —
[`Components\Answers\Helpers\Economy`](../../../core/components/com_answers/helpers/economy.php)
is built that way.

## View helpers

Views reach helpers by a second route. `Hubzero\View\View::__call()` turns an
unknown method call on a view into a helper lookup, which is how
`$this->css()`, `$this->js()`, `$this->img()`, and `$this->pagination()`
work. The built-in set lives in
`core/libraries/Hubzero/View/Helper`:

| Call | Does |
|---|---|
| `css()`, `js()`, `img()` | attach or locate an asset — see [Assets](assets.md) |
| `pagination()` | render a pager |
| `grid()` | administrator list-table controls |
| `editor()` | render the configured WYSIWYG editor |
| `truncate()` | shorten text on a word boundary |
| `highlight()` | wrap search terms in `<span class="highlight">` |
| `autolink()` | turn bare URLs into links |
| `obfuscate()` | encode an email address against scrapers |
| `clean()` | strip unsafe markup |
| `icon()`, `autocompleter()` | icons; the autocomplete widget |

[`Hubzero\Component\View`](../../../core/libraries/Hubzero/Component/View.php)
extends that lookup to your component. Before falling back to the built-ins it
looks for `Components\{Name}\Helpers\{Method}`, and if it finds one that is
invokable it registers it. So a class
`Components\Kb\Helpers\Byline` with a `__invoke()` method, extending
`Hubzero\View\Helper\AbstractHelper`, becomes `$this->byline($article)` in
every `com_kb` view:

```php
namespace Components\Kb\Helpers;

use Hubzero\View\Helper\AbstractHelper;

class Byline extends AbstractHelper
{
    public function __invoke($article)
    {
        return $this->getView()->escape($article->creator()->get('name'));
    }
}
```

Note that the class name carries no client segment, so the file belongs in the
component's top-level `helpers/` directory, not the client's.

> **Note:** The helper must extend `AbstractHelper`, or at least implement
> `setView()`: the view calls `setView($this)` on the object before invoking
> it, and an invokable class missing that method is a fatal
> "call to undefined method", not a fallback. No component in the tree
> currently uses this hook — the static pattern above is what you will find in
> practice.

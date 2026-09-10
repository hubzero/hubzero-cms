<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/modules/views
-->
# Views

A module's markup belongs in a layout file under `tmpl/`, separate from the
class that gathers the data. Keeping them apart is not only tidiness: a layout
in `tmpl/` can be overridden by a template, and markup echoed from the class
cannot.

That override point is the whole argument. A module ends up in the sidebar of
a hub whose designer you will never meet, and the only way they can make your
booking list match the rest of the page — without forking the module and
inheriting your bugs — is to drop a file into their template. Echo the markup
from `helper.php` and you have taken that away.

## The smallest one

`mod_upcoming_bookings/tmpl/default.php`, in full:

```php
<?php
defined('_HZEXEC_') or die();

$this->css();
?>
<h3><?php echo Lang::txt('MOD_UPCOMING_BOOKINGS_HEADING'); ?></h3>
<?php if (!count($this->reservations)) : ?>
	<p><?php echo Lang::txt('MOD_UPCOMING_BOOKINGS_NONE'); ?></p>
<?php else : ?>
	<ul id="upcoming-bookings-<?php echo $this->module->id; ?>">
		<?php foreach ($this->reservations as $reservation) : ?>
			<li><?php echo Lang::txt(
				'MOD_UPCOMING_BOOKINGS_SLOT',
				$this->escape($reservation->get('starts')),
				$this->escape($reservation->get('ends'))
			); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
```

Everything the class set is on `$this`. There is no view object between them.

## Where layouts live

```
core/modules/mod_mygroups/
    tmpl/
        default.php
        simple.php
        _item.php
        index.html
```

`default.php` is the layout used when nothing says otherwise. Other names are
alternate layouts, selected either by the class or by the instance's `layout`
parameter. A leading underscore is a convention for a partial meant to be
required from another layout, not chosen directly.

## How a layout is found

`getLayoutPath($layout)` on the module class delegates to
[`Hubzero\Module\Loader::getLayoutPath()`](../../../core/libraries/Hubzero/Module/Loader.php),
which returns the first of three paths:

1. `{templates}/{template}/html/mod_upcoming_bookings/{layout}.php` — the active
   template's override.
2. `{module directory}/tmpl/{layout}.php` — the module's own layout.
3. `{module directory}/tmpl/default.php` — the fallback.

Only the first is checked for existence against the template; if neither the
override nor the named layout exists, the third path is returned whether or
not the file is there, so a typo in a layout name silently renders `default`.

A layout name may be qualified with a template: `getLayoutPath('beez:list')`
looks for `list.php` under the `beez` template rather than the active one, and
`_` as the template name means "the active template". Core modules do not use
this form.

> **Note:** Template overrides are how a hub restyles a shipped module without
> forking it. See [Overrides](../11-templates/09-overrides.md).

## Choosing the layout

The base `display()` reads the instance parameter:

```php
require $this->getLayoutPath($this->params->get('layout', 'default'));
```

A class that overrides `display()` chooses for itself, as `mod_mygroups` does
when its `show_recent` parameter is off:

```php
$layout = 'default';
if (!$this->params->get('show_recent', 1))
{
    $layout = 'simple';
}

require $this->getLayoutPath($layout);
```

## Writing one

The layout is `require`d from inside a method of the module class, so `$this`
is the module object. Every property the class set is available, along with
`$this->params`, `$this->module`, `$this->escape()`, and the asset helpers:

<!--include: core/modules/mod_mygroups/tmpl/default.php:1-15-->

Two things to note. The layout has no `namespace` declaration, so it runs in
the global namespace and can call `Lang::txt()`, `Route::url()`, and `User`
without importing anything — unlike `helper.php`, which is namespaced and must
import each facade it uses. And `$this->module->id` is used to build element
ids: two instances of the same module on one page would otherwise collide.

## Escaping

Anything that came from the database or the request goes through
`$this->escape()`:

```php
<h3><?php echo $this->escape($this->module->title); ?></h3>
```

Content that is deliberately HTML — a `mod_custom` body, output already
through the content plugins — is echoed directly.

## Partials

A layout can require another layout from the same module through the same
resolver, which keeps the override point intact. `mod_mygroups` renders each
row this way:

```php
foreach ($this->recentgroups as $group)
{
    if ($group->published)
    {
        $status = $this->getStatus($group);

        require $this->getLayoutPath('_item');
    }
}
```

`$group` and `$status` are locals of the layout, and the partial sees them
because `require` shares the enclosing scope. That is convenient and fragile
in equal measure: rename the loop variable and the partial breaks with no
warning beyond an undefined-variable notice.

## No view class

There is no view object in a module and no `loadTemplate()`. The class is the
view context, the layout is the template, and the output goes to the buffer
that `Hubzero\Module\Loader::render()` opened around the entry file. Plugins
work differently — see [Plugin views](../10-plugins/05-views.md).

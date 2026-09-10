<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/modules/loading
-->
# Loading

Modules reach the page in three ways: a template asks for a position, a
component or plugin renders one inline, or an article contains a tag that
expands into one. All three end at the same place —
[`Hubzero\Module\Loader::render()`](../../../core/libraries/Hubzero/Module/Loader.php),
reachable through the `Module` facade.

Almost always you want the first. A module placed by an administrator into a
template position is the arrangement everything else is built around: the hub
decides where it goes, on which pages, and who sees it, without touching code.
Render a module from a component only when the component's own layout is the
only place the block makes sense. Read this chapter mainly to know why your
module is not appearing — the section on
[positions](#positions-belong-to-the-template) is the answer more often than
not.

## What the loader knows

`Loader::all()` runs one query per request and caches the result. It returns
the published instances that pass every one of these filters:

- `m.published = 1` and the matching `#__extensions` row has `e.enabled = 1`
- `publish_up` is null or past, `publish_down` is null or future
- `m.access` is one of the current user's authorised view levels
- `m.client_id` matches the current client
- the instance is assigned to the current `Itemid`, or to all menu items
- with language filtering on, `m.language` is the current tag or `*`

An instance assigned negatively to the current menu item is then removed, and
duplicates are collapsed. The result is ordered by position, then by
`ordering`.

Read that list as a checklist. Each line is a way for a published module to be
absent from one page and present on another, and two of them catch people. A
module assigned to a single menu item is invisible on every other page, which
looks exactly like a broken module if you test on the wrong one. And a module
whose access level is not Public disappears for the logged-out visitor you are
probably testing as. `mod_upcoming_bookings` has nothing to show a guest
anyway, so give it a Registered access level rather than letting it render an
empty box.

The result is cached under a key made from the menu item, the user's view
levels, the client and the language tag, so a module that is right for one
visitor is not served to another.

## In a template

The template declares a position with a `jdoc` tag, which the document parser
replaces with the rendered output of every module in that position:

```html
<jdoc:include type="modules" name="footer" />
```

`type="modules"` renders the whole position; `type="module"` renders one
named module. Either accepts `style`, naming the chrome function that wraps
each module, and `params`, an inline parameter override.

### Collapsing empty regions

`countModules()` on the document tells a template whether a position has
anything in it, so that the markup around it can be omitted:

```php
<?php if ($this->countModules('helppane')) : ?>
	<div id="help">
		<jdoc:include type="modules" name="helppane" />
	</div>
<?php endif; ?>
```

The argument is an expression. `Hubzero\Document\Type\Html::countModules()`
splits it on ` + `, ` - `, ` * `, ` / `, ` == `, ` != `, ` <> `, ` < `, ` > `,
` <= `, ` >= `, ` and `, ` or `, and ` xor `, replaces each position name with
the number of modules in it, and evaluates the result:

```php
$this->countModules('left or right')
$this->countModules('user1 + user2')
```

> **Warning:** The operator must be surrounded by single spaces. `'left+right'`
> is treated as one position name and counts zero. And quoting each name
> separately — `countModules('left' and 'right')` — evaluates the `and` in PHP
> before the call, so the function receives `true`, counts a position named
> `1`, and returns zero every time. Write the whole expression as one string.

## In a component, plugin, or view

Sometimes the block has to sit inside a component's own markup rather than
beside it — a note halfway down a booking form, where no template position
reaches. `Hubzero\Module\Helper` is a static wrapper over the same loader, and
is what component views use:

| Call | Result |
|---|---|
| `Helper::renderModules($position)` | Returns the combined output of every module in `$position`. |
| `Helper::renderModule($name)` | Returns the output of one module, by element name or by name without the prefix. |
| `Helper::displayModules($position)` | Echoes the same. |
| `Helper::displayModule($name)` | Echoes the same. |
| `Helper::countModules($condition)` | The count expression, as above. |
| `Helper::getParams($id)` | A `Registry` of one instance's parameters, by id or element name. |

```php
echo \Hubzero\Module\Helper::renderModules('extracontent');
```

The `Module` facade exposes the loader itself, which is the same thing with
more control: `Module::position()`, `Module::name()`, `Module::byName()`,
`Module::byPosition()`, `Module::isEnabled()`, `Module::params()`,
`Module::count()`, `Module::render()`, `Module::path()`, and
`Module::canonical()`.

```php
echo Module::position('notices');
echo Module::name('mod_login', 'xhtml');
```

> **Note:** `renderModules()` and `displayModules()` default to a style of
> `-2`, which matches no chrome function, so the module's output is returned
> unwrapped. Pass `'xhtml'` if you want the `<div class="module">` wrapper.
> `renderModule()` and `displayModule()` default to `-1`, which the wrapper
> translates to `none` — also unwrapped, but deliberately.

## Chrome

The style names a function `modChrome_{style}`, loaded from
`core/templates/system/html/modules.php` and, if it exists, from
`{template}/html/modules.php`. The shipped set is `none`, `table`, `xhtml`,
`outline`, `sliders`, and `tabs`. A template adds its own by defining another
function in its `html/modules.php`.

`none` echoes the module's content and nothing else. `xhtml` wraps it in a
`<div class="module{moduleclass_sfx}">` with an `<h3>` title when the
instance's **Show Title** is on, and emits nothing at all when the content is
empty. Multiple styles may be given, space-separated.

## In article content

With the **Content — xHub Tags** plugin enabled, article text can carry a tag
that renders a position where it stands:

```
{xhub:module position="footer"}
```

`style` and `params` attributes are accepted; the plugin defaults the style to
`xhtml`, and translates the legacy numeric values `-1` and `-2` to `none` and
`xhtml`.

## Assigning a module to a position

None of this happens without an instance. In the administrative interface, go
to the Module Manager, create a new instance of the module, give it a title,
choose a position, set the access level and menu assignment, and publish it.

## Positions belong to the template

This is the one to read twice, because it is the most common reason a module
that works is not on the page.

Your module never declares a position. It does not know what positions exist,
and it cannot create one. A position is a string that a template's `index.php`
asks for:

```html
<jdoc:include type="modules" name="right" />
```

The loader returns the published instances whose `position` column holds that
string. That is the entire contract. A position is a name two parties happen
to agree on, and nothing checks that they do.

So there are three ways for a correct module to render nothing, and none of
them logs anything:

| What you did | What happens |
|---|---|
| Assigned the instance to a position the active template's `index.php` never includes | Nothing renders. The instance is published and looks fine in the Module Manager. |
| Assigned it to a position the template declares in `templateDetails.xml` but never uses in `index.php` | Same. The `<positions>` block only fills the picker; it renders nothing by itself. |
| Switched the site to a template with different position names | Every module in a dropped position goes quiet at once. |

The `<positions>` block is documentation for the administrator, not a
declaration to the framework. Templates in the tree already drift from it:
`kimera` renders `breadcrumbs` and `endpage` without declaring them, and
declares `banner` and `introblock` without rendering them.

> **Tip:** When a module will not appear, check the position before anything
> else. Open the active template's `index.php` and search for the position
> name. If it is not there, the module was never going to render, and no
> amount of checking the access level or the menu assignment will find it.

Which position to put `mod_upcoming_bookings` in is therefore a question about
the hub's template, not about your module. `right` and `left` are the two most
widely shared names; both `kimera` and `lucent` render them. See
[Layouts](../11-templates/06-layouts.md#module-positions) for the full list per
shipped template.

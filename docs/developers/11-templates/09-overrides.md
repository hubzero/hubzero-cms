<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/templates/overrides
source-id: 3512
-->
# Output overrides

A template can replace almost any markup or asset a component, module or
plugin produces, without touching the extension. Put a file of the right name
in the right place under the template's `html` directory and the framework
finds it first. Overrides survive upgrades, because the extension's own files
are never edited.

## Why this is the chapter that matters

Most of what a hub actually asks for is not a new template. It is *this one
screen, but with our wording*, or *this list, but without that column*. There
are three ways to do that and only one of them survives an upgrade:

| Approach | Survives an upgrade? |
|---|---|
| Edit the file in `core/components/…` | No. Replaced. |
| Fork the component into `app/components/…` | Yes, but you now maintain a component. |
| Put a layout of the same name under your template's `html/` | Yes. |

The third is what this chapter is about. `northgate` uses it for exactly one
thing worth the effort: `com_bookings`' instrument page, where Northgate needs
its own booking-policy text above the calendar. Everything else it leaves
alone.

Every override belongs to one template. Switch template and the overrides go
with it — which is a feature when two partner institutions share a hub, and a
surprise when someone changes the default style and half the wording reverts.

## Where overrides live

The root is `App::get('template')->path . '/html'` — the active template's
directory plus `html`. That directory is `app/templates/{name}` when it
exists and `core/templates/{name}` otherwise; see
[the book's introduction](README.md#where-templates-live).

Site and administrator templates are separate. An override of an admin screen
goes under the admin template's `html` directory, not the site template's.

> **Warning:** Do not put overrides in a shipped template. `core/templates` is
> replaced on upgrade. Copy the template into `app/templates` first.

None of the shipped templates overrides a layout. Kimera's `html` directory
holds 32 files and every one of them is a stylesheet; Kameleon's holds
`modules.php` and nothing else. Use them as examples of asset overrides, not
layout ones.

## The five rules

Everything below is an application of these. If you remember nothing else,
remember these.

1. **The template is searched first, then the extension.** Nothing merges; the
   first file found wins outright.
2. **The override directory is named for the request's `option`**, not for the
   component that owns the view.
3. **A component layout is called `display`**, not `default`.
4. **A plugin view has no fallback above its own `tmpl` directory.**
5. **Asset override paths are flat.** No `assets`, no `css`, no `js`.

## Rule 1: the template is searched first

A component view resolves its layout through
[`Hubzero\View\View::setPath()`](../../../core/libraries/Hubzero/View/View.php),
which builds the search list:

<!--include: core/libraries/Hubzero/View/View.php:551-585-->

Paths are pushed onto the front of the stack, so the last one added is
searched first. For a component view the order is:

1. `{template}/html/{option}/{view}/`
2. `{component}/{client}/views/{view}/tmpl/`
3. `{component}/{client}/views/{view}/`

So the override of

```
app/components/com_bookings/site/views/instruments/tmpl/display.php
```

is

```
app/templates/northgate/html/com_bookings/instruments/display.php
```

**Copy the file, do not write a new one.** The winner is used whole; nothing
from the extension's version is merged in. An override that contains only the
paragraph you wanted to add renders a page that is only that paragraph.

**When the layout is not found anywhere**, `loadTemplate()` throws
`InvalidLayoutException` with code 404 — *Layout display not found*. A
misspelled override filename does not silently fall through to the component's
copy if the component has no `default.php` either; it takes the page down.

## Rule 2: the directory is named for the request

`{option}` is the `option` request variable — `com_bookings`, `com_kb` — read
fresh from the request, not from the view. Two consequences, and both bite.

**A view rendered on behalf of another component is overridden under the
requesting component's name.** If `com_bookings` builds a `com_tags` view
object to draw its tag cloud, the override for it is
`html/com_bookings/…`, not `html/com_tags/…`.

**An override only fires on requests that carry its `option`.** A
`com_bookings` view rendered inside a group page — where `option=com_groups` —
does not see `html/com_bookings/`. The override is not broken; the request is a
different one. If your override works on the component's own pages and nowhere
else, this is why.

A view can be told otherwise: passing `override_path` in a view's construction
config replaces the request's `option` with a fixed directory name. Component
code that renders another component's views should set it. Most does not.

## Rule 3: the layout is `display`

`Hubzero\Component\View` declares `public $_layout = 'display';`. The abstract
`Hubzero\View\View` it extends declares `'default'`. So a component view's
layout is `display.php` unless a controller sets another with
`$view->setLayout('edit')`.

That is the first thing to check when an override does nothing: a file named
`default.php` in the right directory is simply never asked for.

If the named layout is not found anywhere on the search path,
[`loadTemplate()`](../../../core/libraries/Hubzero/View/View.php) falls back to
`default.php` before giving up. That fallback is why components such as
`com_login`, whose views ship only `tmpl/default.php`, render at all — and it
is also why a `default.php` override sometimes *does* work, which makes the
rule harder to learn. Name it `display.php`.

`setLayout()` also accepts a `{template}:{layout}` form, which reads the layout
from a named template rather than the active one.

### Sub-layouts

A layout can render a fragment of itself by calling `loadTemplate()` with a
suffix. The file taken is `{layout}_{suffix}.php`:

```php
<?php echo $this->loadTemplate('batch'); ?>
```

in `com_categories/admin/views/categories/tmpl/default.php` renders
`default_batch.php` from the same directory. You pass `batch`, not
`default_batch` — the layout name is already known.

Each sub-layout is looked up separately, on the same search path, so a
template can override one fragment and leave the rest alone. This is the
cheapest kind of override there is: copying

```
core/components/com_categories/admin/views/categories/tmpl/default_batch.php
```

to

```
app/templates/northgateadmin/html/com_categories/categories/default_batch.php
```

replaces the batch panel while `default.php` still comes from the component, so
an upgrade to the rest of the screen reaches you normally. `com_categories` is
an administrator component, so that override belongs to the administrator
template.

## Rule 4: plugin views have no fallback

[`Hubzero\Plugin\View`](../../../core/libraries/Hubzero/Plugin/View.php) builds
its own search list:

<!--include: core/libraries/Hubzero/Plugin/View.php:205-226-->

The override directory is named `plg_{folder}_{element}`, and the view name is
a directory inside it. So

```
core/plugins/groups/forum/views/categories/tmpl/display.php
```

is overridden at

```
app/templates/northgate/html/plg_groups_forum/categories/display.php
```

Compare that with `View::setPath()` above. The component version pushes
`dirname($path)` onto the stack when the path ends in `tmpl`, giving a third
search location; the plugin version does not. A plugin view searches exactly
two places — the override and the plugin's own `tmpl` directory — so a layout
sitting one level up, beside `tmpl/`, is never found.

## Rule 5: asset paths are flat

Every asset pushed with `$this->css()`, `$this->js()` or the
`Hubzero\Document\Assets` statics goes through
[`Hubzero\Document\Asset\File`](../../../core/libraries/Hubzero/Document/Asset/File.php),
which checks the template before the extension:

<!--include: core/libraries/Hubzero/Document/Asset/File.php:350-358-->

The override path is the extension's name and the file's name, with no
`assets`, no `css` and no `js` directory in between, whatever the source file's
own path was:

| Source | Override |
|---|---|
| `app/components/com_bookings/site/assets/css/bookings.css` | `html/com_bookings/bookings.css` |
| `app/components/com_bookings/site/assets/js/bookings.js` | `html/com_bookings/bookings.js` |
| `core/modules/mod_reportproblems/assets/css/mod_reportproblems.css` | `html/mod_reportproblems/mod_reportproblems.css` |
| `core/plugins/groups/forum/assets/css/forum.css` | `html/plg_groups_forum/forum.css` |
| `core/plugins/groups/forum/assets/js/forum.js` | `html/plg_groups_forum/forum.js` |

Kimera's `html` directory is a real set of these — `com_blog/blog.css`,
`com_kb/kb.css`, `mod_notices/mod_notices.css`,
`plg_groups_citations/citations.css` and twenty-odd more.

Mirroring the source path — `html/com_bookings/assets/css/bookings.css` — is
the mistake to expect, and it fails by doing nothing: the file is simply not
found and the component's own stylesheet loads instead.

An override replaces the file; it does not add to it. Start from a copy of the
extension's stylesheet, or you lose every rule it had. For a small change,
prefer a rule in your template's own stylesheet over an override you now have
to keep in step with the component.

> **Note:** Because the path is flat, two assets of the same name from the same
> extension collide. `plg_groups_forum` ships both `forum.css` and `like.css`;
> those are fine. An extension shipping `assets/css/x.css` and
> `assets/print/x.css` could not have both overridden.

See [Cascading style sheets](07-css.md#where-the-file-is-looked-for) for the
source paths these fall back to.

## Module layouts

Modules do not use a view object. `Hubzero\Module\Module::display()` requires
one file, chosen by
[`Hubzero\Module\Loader::getLayoutPath()`](../../../core/libraries/Hubzero/Module/Loader.php):

<!--include: core/libraries/Hubzero/Module/Loader.php:346-381-->

There are exactly three candidates, tried in order:

1. `{templates}/{template}/html/{mod_name}/{layout}.php`
2. `{module}/tmpl/{layout}.php`
3. `{module}/tmpl/default.php`

A module is a `mod_name.php` stub, a `helper.php` holding the data logic, and
one or more layouts in `tmpl/`:

```
core/modules/mod_reportproblems/
    assets/css/mod_reportproblems.css
    assets/js/mod_reportproblems.js
    helper.php
    mod_reportproblems.php
    mod_reportproblems.xml
    tmpl/default.php
```

Its layout is overridden at
`app/templates/northgate/html/mod_reportproblems/default.php`.

Two things are specific to modules. The layout comes from the module
instance's own `layout` parameter, so an administrator can point one instance
at a different file. And the parameter accepts a `{template}:{layout}` form,
which reads the layout from a *named* template rather than the active one —
`_` means the active template.

## System assets

The shared stylesheets and scripts in `core/assets` are overridable too. They
use the extension name `system`, and they are the one exception to rule 5 — they
keep a directory segment, the asset's type:

```
{template}/html/system/css/introduction.css
{template}/html/system/js/jquery.fancybox.js
```

Kimera overrides three of them: `introduction.css`, `jquery.fancybox.css` and
`jquery.fancyselect.css`. A view asks for one by naming `system` as the
extension:

```php
$this->css('introduction.css', 'system');
```

## Images

`img()` resolves the same way, with `img` as the asset type, so an image is
overridden at `{template}/html/{extension}/{file}`:

```php
<img src="<?php echo $this->img('logo.png'); ?>" alt="" />
```

> **Note:** The older static `Assets::getComponentImage()`,
> `getModuleImage()` and `getPluginImage()` do **not** agree with this. They
> look for the override at `{template}/html/{extension}/images/{file}`, with
> an `images` segment `img()` does not use, and they search only
> `app/templates`. Use `img()`.

## Module chrome

Chrome is the markup wrapped around a module's own output. It is a set of
plain functions named `modChrome_{style}`, and a template supplies them in a
single file, `{template}/html/modules.php`.

[`Hubzero\Module\Loader::render()`](../../../core/libraries/Hubzero/Module/Loader.php)
always includes `core/templates/system/html/modules.php` first, then the
active template's `modules.php` if it has one. Both are included, so a
template's file adds new styles and redefining an existing one is a fatal
error — PHP will not let the same function be declared twice.

The system file defines `none`, `table`, `xhtml`, `outline`, `sliders` and
`tabs`. The default is `none`:

<!--include: core/templates/system/html/modules.php:10-16-->

A layout picks a style with the `style` attribute on the `jdoc` tag:

```php
<jdoc:include type="modules" name="left" style="xhtml" />
```

The value is split on spaces and each name applied in turn, so
`style="xhtml outline"` runs both. `outline` is appended automatically when a
request carries `tp=1` **and** **Preview Module Positions** is enabled in the
`com_templates` options; that is how the position names appear on the page.
That preview is the fastest way to answer "which position is this?" while you
are building a layout.

Kameleon adds one style of its own, `modChrome_cpanel`, for the control panel
icons.

## Pagination

Pagination is rendered by
[`Hubzero\Pagination\Paginator`](../../../core/libraries/Hubzero/Pagination/Paginator.php)
through a view whose layout is
[`Hubzero/Pagination/Views/paginator.php`](../../../core/libraries/Hubzero/Pagination/Views/paginator.php).

> **Warning:** The pagination override does not work. `Hubzero\Pagination\View`
> adds `{override_path}/html/pagination/` to its search path only when an
> `override_path` was passed into its constructor, and `Paginator::render()`
> never passes one. A `html/pagination/paginator.php` in your template is
> ignored. This is recorded with the project.
>
> The older mechanism — a `templates/{name}/html/pagination.php` defining
> `pagination_list_footer()`, `pagination_list_render()`,
> `pagination_item_active()` and `pagination_item_inactive()` — was removed
> and no code looks for it. To change pagination markup today, style it, or
> pass your own view object to `render()`.

## Quick reference

Using the template named `northgate`:

| To override | Copy | To |
|---|---|---|
| A component layout | `app/components/com_bookings/site/views/instruments/tmpl/display.php` | `app/templates/northgate/html/com_bookings/instruments/display.php` |
| A component sub-layout | `core/components/com_categories/admin/views/categories/tmpl/default_batch.php` | `app/templates/northgateadmin/html/com_categories/categories/default_batch.php` |
| A plugin layout | `core/plugins/groups/forum/views/categories/tmpl/display.php` | `app/templates/northgate/html/plg_groups_forum/categories/display.php` |
| A module layout | `core/modules/mod_reportproblems/tmpl/default.php` | `app/templates/northgate/html/mod_reportproblems/default.php` |
| A component stylesheet | `app/components/com_bookings/site/assets/css/bookings.css` | `app/templates/northgate/html/com_bookings/bookings.css` |
| A module stylesheet | `core/modules/mod_reportproblems/assets/css/mod_reportproblems.css` | `app/templates/northgate/html/mod_reportproblems/mod_reportproblems.css` |
| A plugin stylesheet | `core/plugins/groups/forum/assets/css/forum.css` | `app/templates/northgate/html/plg_groups_forum/forum.css` |
| A shared stylesheet | `core/assets/css/introduction.css` | `app/templates/northgate/html/system/css/introduction.css` |
| An SVG icon | `core/assets/icons/edit.svg` | `app/templates/northgate/html/icons/edit.svg` |
| Module chrome | `core/templates/system/html/modules.php` | `app/templates/northgate/html/modules.php` (add styles; do not redeclare) |

## What you cannot override this way

- **The template's own layouts.** `index.php`, `component.php` and the rest
  belong to the template already; see [Page layouts](06-layouts.md).
- **Language strings.** Those are replaced through the language override
  files, not through `html`; see [Languages](02-languages.md).
- **Pagination markup.** See the warning above.
- **Anything a view prints from a helper or a model.** Overrides replace
  markup, not logic. If the string you want to change is built in PHP outside
  the layout, the override cannot reach it — and that is the point at which a
  plugin, or a change to the component, is the right answer instead.

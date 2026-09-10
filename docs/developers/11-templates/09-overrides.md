<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
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

Every override belongs to one template. Switch template and the overrides go
with it.

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
`modules.php` and nothing else. Use them as examples of asset overrides, not layout ones.

## Component layouts

A component view resolves its layout through
[`Hubzero\View\View::setPath()`](../../../core/libraries/Hubzero/View/View.php),
which builds the search list:

<!--include: core/libraries/Hubzero/View/View.php:551-585-->

Paths are pushed onto the front of the stack, so the last one added is
searched first. For a component view the order is:

1. `{template}/html/{option}/{view}/`
2. `{component}/{client}/views/{view}/tmpl/`
3. `{component}/{client}/views/{view}/`

`{option}` is the `option` request variable — `com_blog`, `com_kb` — and
`{view}` is the view's name. So the override of

```
core/components/com_blog/site/views/entries/tmpl/display.php
```

is

```
app/templates/mytemplate/html/com_blog/entries/display.php
```

> **Note:** The override directory is named for the component **of the current
> request**, not for the component that owns the view. A view rendered by one
> component on behalf of another is overridden under the requesting
> component's name.

### Layout names

The layout name is the file name. `Hubzero\Component\View` defaults it to
`display`, not `default`, and a controller sets another with
`$view->setLayout('edit')`.

If the named layout is not found anywhere on the search path,
[`loadTemplate()`](../../../core/libraries/Hubzero/View/View.php) falls back to
`default.php`. That fallback is why components such as `com_login`, whose
views ship only `tmpl/default.php`, render at all.

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
template can override one fragment and leave the rest alone. Copying

```
core/components/com_categories/admin/views/categories/tmpl/default_batch.php
```

to

```
app/templates/myadmintemplate/html/com_categories/categories/default_batch.php
```

replaces the batch panel while `default.php` still comes from the component.
`com_categories` is an administrator component, so that override belongs to
the administrator template.

## Plugin layouts

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
app/templates/mytemplate/html/plg_groups_forum/categories/display.php
```

Unlike component views, plugin views search only the `tmpl` directory and the
override — there is no fallback to the view directory above `tmpl`.

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
`app/templates/mytemplate/html/mod_reportproblems/default.php`.

Two things are specific to modules. The layout comes from the module
instance's own `layout` parameter, so an administrator can point one instance
at a different file. And the parameter accepts a `{template}:{layout}` form,
which reads the layout from a *named* template rather than the active one —
`_` means the active template.

## Stylesheets and scripts

Every asset pushed with `$this->css()`, `$this->js()` or the
`Hubzero\Document\Assets` statics goes through
[`Hubzero\Document\Asset\File`](../../../core/libraries/Hubzero/Document/Asset/File.php),
which checks the template before the extension:

<!--include: core/libraries/Hubzero/Document/Asset/File.php:350-358-->

The override path is **flat**. It is the extension's name and the file's name,
with no `assets`, no `css` and no `js` directory in between, whatever the
source file's own path was:

| Source | Override |
|---|---|
| `core/components/com_groups/site/assets/css/groups.css` | `html/com_groups/groups.css` |
| `core/modules/mod_reportproblems/assets/css/mod_reportproblems.css` | `html/mod_reportproblems/mod_reportproblems.css` |
| `core/plugins/groups/forum/assets/css/forum.css` | `html/plg_groups_forum/forum.css` |
| `core/plugins/groups/forum/assets/js/forum.js` | `html/plg_groups_forum/forum.js` |

Kimera's `html` directory is a real set of these — `com_blog/blog.css`,
`com_kb/kb.css`, `mod_notices/mod_notices.css`,
`plg_groups_citations/citations.css` and twenty-odd more.

An override replaces the file; it does not add to it. Start from a copy of the
extension's stylesheet, or you lose every rule it had.

> **Note:** Because the path is flat, two assets of the same name from the same
> extension collide. `plg_groups_forum` ships both `forum.css` and `like.css`;
> those are fine. An extension shipping `assets/css/x.css` and
> `assets/print/x.css` could not have both overridden.

See [Cascading style sheets](07-css.md#where-the-file-is-looked-for) for the
source paths these fall back to.

## System assets

The shared stylesheets and scripts in `core/assets` are overridable too. They
use the extension name `system`, and they are the one case that keeps a
directory segment — the asset's type:

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

Using a template named `mytemplate`:

| To override | Copy | To |
|---|---|---|
| A component layout | `core/components/com_blog/site/views/entries/tmpl/display.php` | `app/templates/mytemplate/html/com_blog/entries/display.php` |
| A component sub-layout | `core/components/com_categories/admin/views/categories/tmpl/default_batch.php` | `app/templates/myadmintemplate/html/com_categories/categories/default_batch.php` |
| A plugin layout | `core/plugins/groups/forum/views/categories/tmpl/display.php` | `app/templates/mytemplate/html/plg_groups_forum/categories/display.php` |
| A module layout | `core/modules/mod_reportproblems/tmpl/default.php` | `app/templates/mytemplate/html/mod_reportproblems/default.php` |
| A component stylesheet | `core/components/com_groups/site/assets/css/groups.css` | `app/templates/mytemplate/html/com_groups/groups.css` |
| A module stylesheet | `core/modules/mod_reportproblems/assets/css/mod_reportproblems.css` | `app/templates/mytemplate/html/mod_reportproblems/mod_reportproblems.css` |
| A plugin stylesheet | `core/plugins/groups/forum/assets/css/forum.css` | `app/templates/mytemplate/html/plg_groups_forum/forum.css` |
| A shared stylesheet | `core/assets/css/introduction.css` | `app/templates/mytemplate/html/system/css/introduction.css` |
| Module chrome | `core/templates/system/html/modules.php` | `app/templates/mytemplate/html/modules.php` (add styles; do not redeclare) |

## What you cannot override this way

- **The template's own layouts.** `index.php`, `component.php` and the rest
  belong to the template already; see [Page layouts](06-layouts.md).
- **Language strings.** Those are replaced through the language override
  files, not through `html`; see [Languages](02-languages.md).
- **Anything a view prints from a helper or a model.** Overrides replace
  markup, not logic. If the string you want to change is built in PHP outside
  the layout, the override cannot reach it.

<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/templates/css
source-id: 3510
modified: 2012-09-21
-->
# Cascading style sheets

A page's styling comes from three places: the template's own stylesheets, the
shared stylesheets under `core/assets`, and whatever the components, modules
and plugins on the page push into the document. This chapter covers where each
of those lives, how a file is found, and what a template can do about it.

The reason to read it before writing CSS is order. Your rules and a component's
rules end up in the same cascade with the same specificity more often than you
would like, and which of them wins is decided by *where you linked the file*,
not by anything in the file itself. Getting that wrong produces a template that
works until a component pushes a stylesheet, and then does not.

## The template's own stylesheets

Convention puts a template's CSS in a `css` directory at the top of the
template directory. Nothing enforces the convention and **nothing is loaded
automatically**: every stylesheet a template uses is linked by a layout, by
name. There is no `main.css` that the CMS picks up on its own. A new template
whose CSS never appears is usually a template that never linked it.

A layout links a stylesheet one of two ways, and the choice decides the
cascade.

Directly in the markup, before `<jdoc:include type="head" />`, so that it
loads ahead of anything an extension queues:

```php
<link rel="stylesheet" type="text/css" media="all"
      href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/index.css?v=<?php echo filemtime(__DIR__ . '/css/index.css'); ?>" />
```

Or through the document, in which case it lands *inside* the head block, after
the component's stylesheets:

<!--include: core/templates/kameleon/index.php:13-22-->

Kimera and Lucent use the first form; Kameleon uses the second, which is why
its own rules need enough specificity to win against a component's.

Use the first form unless you want to override component CSS wholesale. It is
what `northgate` does: brand styles load first, components layer on top, and
the handful of component rules Northgate needs to beat are handled with
[output overrides](09-overrides.md) rather than with a specificity war.

Two details of the link are load-bearing:

- `$this->baseurl` is the URL prefix for the templates directory — `/app` when
  the template lives in `app/templates`, `/core` when it ships. Never hardcode
  either. A `northgate` layout with `/core/templates/…` in it links `kimera`'s
  stylesheet on every page and gives no error at all.
- `?v=<?php echo filemtime(...); ?>` is the cache-busting convention used by
  every shipped template and by
  [`Hubzero\Document\Asset\File::link()`](../../../core/libraries/Hubzero/Document/Asset/File.php).
  Use it. A stylesheet linked without it is cached by version-less URL and
  will not refresh for returning visitors after a deployment.

### What each shipped template links

| Template | Layout | Stylesheets it links |
|---|---|---|
| `kimera` | `index.php` | `css/index.css`, plus `css/browser/ie9.css` and `css/browser/ie8.css` in conditional comments, plus a style declaration built by `css/theme.php` |
| | `component.php` | `css/component.css`, same browser files |
| | `error.php` | `css/error.css` |
| | `offline.php` | `css/offline.css` |
| `lucent` | `index.php`, `error.php` | `less/main.css` — the compiled CSS sits beside its LESS sources, not in `css/` |
| | `component.php` | `css/component.css` |
| `welcome` | `index.php` | `css/normalize.min.css`, `css/main.css` |
| `kameleon` | `index.php` | `css/index.css`, `css/browser/ie9.css`, the custom theme declaration |
| | `cpanel.php` | `css/index.css`, `css/cpanel.css` |
| | `component.php` | `css/component.css` |
| | `login.php` | `css/login.css` |
| | `error.php` | `css/error.css` |
| `system` | `error.php` | `css/error.css` |
| | `offline.php` | `css/general.css`, `css/offline.css` |
| | `login.php` | `css/system.css` |
| | `help.php` | `getSystemStylesheet()`, `css/help.css` |
| | `group.php` | `getSystemStylesheet()`, and the **active** template's `css/main.css` and `css/group.css` |

`system/group.php` is the super group layout, and it is the one place a
stylesheet is looked for in a template other than the one that owns the
layout. It asks the active template for `css/main.css` and `css/group.css`.
Kimera ships `css/group.css` but no `css/main.css`, so a super group page
under Kimera requests one stylesheet that does not exist. If you write a site
template that will be used with super groups, ship both files.

> **Note:** Kimera also carries `css/print.css`, `css/download.css`,
> `css/upload.css` and `css/pages/*.css`. No layout links any of them and
> nothing pushes them. They are listed in `templateDetails.xml` and installed,
> but they are dead. Do not copy them forward when you base a template on
> Kimera.

### Theme files

`css/theme.php` in Kimera and `css/themes/custom.php` in Kameleon are PHP
files that **return** a CSS string built from the template's parameters. The
layout includes the file and passes the result to `addStyleDeclaration()`. See
[Page layouts](06-layouts.md#parameters) for how the parameters reach them.

This is the mechanism to copy for `northgate`'s accent colour: one parameter in
the manifest, one `css/theme.php` that turns it into rules, and no second copy
of the template per institution.

Two things about the pattern are easy to get wrong, and Kimera shows both:

- **The theme file reads variables out of the including scope.**
  `kimera/css/theme.php` uses `$bground`, `$color1`, `$color2`, `$opacity` and
  `$opacity2`, which `index.php` sets immediately before the include. Set them
  first or the file builds a stylesheet from nulls.
- **It is pulled in with `include_once`, and it declares a function.**
  `include_once` returns the string on the first include and `true` on any
  later one, which is why `index.php` guards with `if ($styles)`. Switching to a
  plain `include` to get the string twice fatals instead, on the redeclaration
  of `hex2rgb()`. If you need the CSS in two layouts, put the builder in a
  function or a class and call it.

## LESS

LESS is the preprocessor in use. There are two sets of sources.

`core/assets/less/` holds the shared ones. `site.less` is the build file and
names everything in it:

<!--include: core/assets/less/site.less:7-49-->

`core/assets/less/variables.less` holds the colours, font stacks and sizes
those files use; a template that imports the shared sources redefines the
variables it wants before the import. That is the whole of a re-brand, done
properly: `northgate/less/_variables.less` sets `@linkColor` and the font stack,
and the imports below it recompile against those values.

Each template that uses LESS keeps its own sources in a `less` directory and
imports across into `core/assets/less` by relative path. Kimera's
`less/index.less` opens this way:

<!--include: core/templates/kimera/less/index.less:7-12-->

Those `../../../../` paths survive a copy into `app/templates`, because
`app/templates/northgate/less/` sits the same depth below the repository root
as `core/templates/kimera/less/`.

The compiled result is what ships and what the layout links:
`less/index.less` → `css/index.css` for Kimera and Kameleon,
`less/main.less` → `less/main.css` for Lucent.

> **Warning:** The compiled CSS is committed to the repository and **no build
> script ships with it**. There is no gulpfile, no Grunt config and no npm
> package in any template. If you edit a `.less` file you must recompile it
> yourself, with any LESS compiler, and commit the `.css` alongside. Editing
> only the LESS changes nothing that a browser sees — the page loads the stale
> `.css`, so the symptom is an edit that appears to do nothing at all.

> **Note:** `kimera/less/_variables.less` declares
> `@pathTemplate: "/core/templates/kimera"` and nothing uses it. In a copy it
> still names the source template, so the first rule that does use it points at
> the shipped `kimera`'s files. Either delete it or correct it.

## The shared stylesheets

`core/assets/css/` holds the stylesheets shared by every extension: `reset`,
`layout`, `columns`, `fontcons`, `icons`, `buttons`, `notifications`,
`pagination`, `tabs`, `tags`, `comments`, `voting`, `tooltip`, `introduction`
and the third-party jQuery ones. Most are the compiled output of the matching
file in `core/assets/less/`.

Anything on the page can pull one in by naming `system` as the extension:

```php
$this->css('introduction.css', 'system');
```

That resolves to `core/assets/css/introduction.css`, and it is overridable —
see [Output overrides](09-overrides.md#system-assets).

### getSystemStylesheet

`Hubzero\Document\Assets::getSystemStylesheet()` returns the URL of the
compiled shared stylesheet. It compiles `core/assets/less/site.less` with
[`Hubzero\Document\Lessc`](../../../core/libraries/Hubzero/Document/Lessc.php)
and writes the result to `app/cache/{client}/site.css`, minified unless
`application_env` is `development`. In production it serves the cached file
directly; otherwise it recompiles when any imported file has changed.

> **Warning:** In production the cached file is served as it stands, even
> after the LESS sources change. Delete `app/cache/{client}/site.css` and
> `app/cache/{client}/site.less.cache` to force a rebuild. `muse cache css
> clear` looks for those two files one directory too high and clears
> nothing; that is recorded with the project.

A template can take the build over. If the active template has a
`less/site.less`, that file is used as the build root instead of the core one,
and the template's `less` directory is put ahead of `core/assets/less` on the
import path, so `@import "variables.less"` inside it resolves to the
template's copy if there is one.

Call it with no arguments:

```php
<link rel="stylesheet" type="text/css" media="screen"
      href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" />
```

> **Note:** Older documentation shows this method being passed a list of file
> names — `getSystemStylesheet(array('reset', 'fontcons', …))`. That list is
> now only a fallback: it is used if, and only if, the LESS compile throws.
> Passing it does not select which stylesheets go into the page. Pass nothing.

## Pushing CSS from an extension

Components, modules and plugins do not link stylesheets themselves; they queue
them on the document. There are two `css()` methods, and they differ in their
third argument.

### From a view

Views get `css()` and `js()` from
[`Hubzero\View\Helper\Css`](../../../core/libraries/Hubzero/View/Helper/Css.php).
Both return the view, so calls chain:

```php
$this->css()                     // the extension's own stylesheet
     ->css('instruments')        // the .css extension is optional
     ->css('tags', 'com_tags');  // from another component
```

With no arguments, the file taken is the extension's default name: for a
component the name minus `com_` (`com_bookings` → `bookings.css`), for a module
the full directory name (`mod_notices` → `mod_notices.css`), for a plugin the
plugin's own name (`plg_groups_forum` → `forum.css`).

The arguments are `(name, extension, element)`. `element` is for plugins, and
turns the first two into a plugin name:

```php
$this->css('forum', 'groups', 'forum');   // plg_groups_forum
```

A string containing `{` or `@` is treated as a declaration rather than a file
name and goes to `addStyleDeclaration()`:

```php
$this->css('.foo { color: #000; }');
```

### From a controller, module or plugin

`Hubzero\Component\SiteController` (and so `AdminController`),
`Hubzero\Module\Module` and `Hubzero\Plugin\Plugin` get `css()`, `js()` and
`img()` from
[`Hubzero\Base\Traits\AssetAware`](../../../core/libraries/Hubzero/Base/Traits/AssetAware.php):

<!--include: core/libraries/Hubzero/Base/Traits/AssetAware.php:31-58-->

> **Warning:** The third argument here is an **attributes array**, not a
> plugin element. `$this->css('x.css', 'system', array('media' => 'print'))`
> is correct in a controller, module or plugin, and wrong in a view — a view
> reads that array as a plugin name and looks for `plg_system_Array`. The two
> `css()` methods look identical and are not. The failure is a stylesheet that
> silently never loads: `$asset->exists()` is false, so nothing is queued and
> nothing is logged.

### Where the file is looked for

[`Hubzero\Document\Asset\File::sourcePath()`](../../../core/libraries/Hubzero/Document/Asset/File.php)
builds the search list. For `css('bookings', 'com_bookings')` on the site it
tries, in order, under `app/` and then under `core/`:

```
components/com_bookings/site/assets/css/bookings.css
components/com_bookings/site/css/bookings.css
components/bookings/site/assets/css/bookings.css
components/bookings/site/css/bookings.css
```

`site` there is the client name, so an admin view of the same component looks
under `admin/` instead. Modules drop the client segment
(`modules/mod_notices/assets/css/mod_notices.css`) and plugins use the folder
and element (`plugins/groups/forum/assets/css/forum.css`). `assets/css` is the
directory to use for new work; the flatter variants are only there for old
extensions.

Prefixing the name changes the meaning: `./name.css` looks for the file at the
root of the extension directory, and `/name.css` is an absolute path from the
web root. A name beginning `http`, `//` or `://` is passed through as an
external URL.

Every one of these lookups checks the active template for an override first.
That is the subject of the [next chapter](09-overrides.md).

### The static helpers

`Hubzero\Document\Assets` also carries the older static methods —
`addComponentStylesheet()`, `addModuleStyleSheet()`, `addPluginStyleSheet()`
and their `Script` counterparts. They wrap the same asset objects and honour
the same overrides:

```php
Hubzero\Document\Assets::addComponentStylesheet('com_bookings');
Hubzero\Document\Assets::addModuleStyleSheet('mod_example');
Hubzero\Document\Assets::addPluginStyleSheet('groups', 'forum');
```

Prefer `$this->css()`. The statics remain for code that has no view,
controller, module or plugin object to hand.

## Browsers

Every shipped site layout puts browser classes on the `<html>` element, from
[`Hubzero\Browser\Detector`](../../../core/libraries/Hubzero/Browser/Detector.php),
alongside the text direction and the template's own state:

```php
$browser = new \Hubzero\Browser\Detector();
$cls = array('no-js', $browser->name(), $browser->name() . $browser->major(), $this->direction);
```

That gives selectors such as `html.chrome`, `html.firefox52` and `html.rtl` to
hang a fix on, and it is the mechanism to reach for.

Kimera, Kameleon and Lucent also carry conditional comments loading
`css/browser/ie8.css` and `css/browser/ie9.css`. Conditional comments were
removed from Internet Explorer at version 10 and Internet Explorer itself is
out of support; the files are kept so existing installs do not change, and
there is no reason to add them to a new template.

## Further reading

- [JavaScript](08-javascript.md) — the matching `js()` calls and the script
  behaviours.
- [Output overrides](09-overrides.md) — replacing an extension's stylesheet
  from the template.
- [Elements and typography](13-elements.md) — the markup the shared
  stylesheets expect.

<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/modules/assets
-->
# Assets

A module that needs its own stylesheet, script, or images keeps them beside
its code and pushes them to the document from the class or the layout. The
helpers come from the `AssetAware` trait, which
[`Hubzero\Module\Module`](../../../core/libraries/Hubzero/Module/Module.php) mixes
in, so they are available on `$this` anywhere in a module.

## Where assets live

```
core/modules/mod_mygroups/
    assets/
        css/mod_mygroups.css
        img/
        js/mod_mygroups.js
```

The `assets` directory is split by type, and the type directory name matches
the helper: `css/` for `css()`, `js/` for `js()`, `img/` for `img()`. Files
directly under the module directory (`mod_mygroups/css/…`) are also found, but
core modules all use `assets/`.

## The helpers

| Method | Signature |
|---|---|
| `css` | `css($stylesheet = '', $extension = null, $attributes = array())` |
| `js` | `js($asset = '', $extension = null, $attributes = array())` |
| `img` | `img($asset, $extension = null)` |

`css()` and `js()` add the file to the document and return `$this`, so calls
chain. `img()` adds nothing to the document; it returns a URL for use in an
`src` attribute.

When `$extension` is omitted it defaults, for a module, to
`$this->module->module` — the module's own element name. When the asset name
is omitted as well, the name defaults to the extension name too, which is why
`mod_mygroups`'s stylesheet is called `mod_mygroups.css` and the layout can
load both of its assets with no arguments at all:

```php
$this->css()
     ->js();
```

Naming a file explicitly works the same way, with or without the extension:

```php
$this->css('login.css', 'com_login')
     ->css('providers.css', 'com_login')
     ->js('login', 'com_login')
     ->js('jquery.hoverIntent', 'system');
```

The second argument accepts any extension name — `com_login`, `mod_login`,
`plg_members_blog` — and the literal `system`, which resolves against the
top-level `core/assets` directory. `mod_login` uses both, borrowing
`com_login`'s stylesheets rather than duplicating them.

## Where the file is looked for

For a module, `Hubzero\Document\Asset\File` builds this list and takes the
first path that exists:

```
app/modules/mod_example/assets/css/name.css
app/modules/mod_example/css/name.css
app/modules/example/assets/css/name.css
app/modules/example/css/name.css
core/modules/mod_example/assets/css/name.css
core/modules/mod_example/css/name.css
core/modules/example/assets/css/name.css
core/modules/example/css/name.css
```

Every `app/` path is tried before any `core/` path, so a hub overrides a
shipped module's stylesheet by dropping a file into `app/modules`. An active
template can override any of them with a file at
`{template}/html/mod_example/name.css`, which is checked last and wins when it
exists.

The returned URL carries a cache-buster taken from the file's modification
time: `/core/modules/mod_mygroups/assets/css/mod_mygroups.css?v=1568392841`.

> **Warning:** If the file cannot be found, `css()` and `js()` do nothing at
> all — no exception, no warning, no `<link>` in the head. A stylesheet that
> "isn't loading" is almost always a name that does not match a file, or a
> file outside `assets/`.

## Attributes

The third argument tunes the tag. For `css()`, `media` and `type` and an
`attribs` array; for `js()`, `type`, `defer`, and `async`:

```php
$this->css('print', null, array('media' => 'print'))
     ->js('widget', null, array('defer' => true));
```

## Declarations

Passing a string that contains `{` or `@` to `css()` makes it a declaration
rather than a file reference: the string is added to the document with
`addStyleDeclaration()` instead of being resolved to a path. This is a
convenience for a rule or two computed from a parameter, not a way to build a
stylesheet.

## Images

`img()` returns the URL of a file under `assets/img`, and unlike the other two
it keeps the file extension you give it:

```html
<img src="<?php echo $this->img('picture.png'); ?>" alt="A picture" />
```

Only `png`, `gif`, `jpg`, `jpeg`, and `jpe` are recognised. For an SVG or a
web font, build the path yourself from `Request::root(true)`.

## From the class or the layout

Both work, because both run with `$this` bound to the module object.
`mod_login` pushes its assets from `display()` in `helper.php`; `mod_mygroups`
does it at the top of `tmpl/default.php`. Pushing from the layout has the
small advantage that a template override which replaces the markup also
replaces the asset it depends on.

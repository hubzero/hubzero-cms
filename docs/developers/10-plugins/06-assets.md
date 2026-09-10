<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/plugins/assets
-->
# Assets

A plugin that renders a screen usually needs a stylesheet and a script. They
live beside the plugin's code and are pushed to the document by helpers named
`css()`, `js()`, and `img()` — available both on the plugin class and on a
plugin view, though the two are different methods with different third
arguments.

Most plugins need none of this. `plg_bookings_notify` renders a mail body and
nothing that reaches a browser, so it ships no `assets` directory at all. This
chapter is for the group tabs, member panels and project steps that do draw
something on a page — and its whole content is the answer to one question:
where does the framework look for the file, and what happens when it is not
there.

## Where assets live

```
core/plugins/members/blog/
    assets/
        css/blog.css
        img/
        js/blog.js
```

The directory under `assets` matches the helper: `css/`, `js/`, `img/`. Files
placed directly under the plugin directory (`blog/css/blog.css`) are found
too, but every core plugin uses `assets/`.

## Default names

Called with no arguments, the helpers work out both the extension and the file
name for you:

- the extension defaults to `plg_{group}_{name}` — for the members blog,
  `plg_members_blog`;
- the file name defaults to the plugin's *element*, the third segment of that
  string — `blog`.

"Third segment" is literal: `Hubzero\Document\Asset\File` splits the
extension name on underscores and takes `$parts[1]` as the group directory and
`$parts[2]` as the element. A group or element name containing an underscore
resolves to a directory that does not exist, and by the rule at the end of this
chapter that means nothing loads and nothing complains. See
[Characters in the two names](02-structure.md#characters-in-the-two-names).

So `blog.css` and `blog.js` are what `$this->css()->js()` loads, and
`forum.css` is what the groups forum plugin loads. Name your files after the
plugin and you never have to pass an argument.

## On the plugin class

`Hubzero\Plugin\Plugin` mixes in the `AssetAware` trait:

| Method | Signature |
|---|---|
| `css` | `css($stylesheet = '', $extension = null, $attributes = array())` |
| `js` | `js($asset = '', $extension = null, $attributes = array())` |
| `img` | `img($asset, $extension = null)` |

`css()` and `js()` return `$this`, so they chain. The third argument is a
tag-attribute array: `media`, `type`, and `attribs` for a stylesheet; `type`,
`defer`, and `async` for a script.

```php
$this->css()
     ->js('blog');
```

## On a plugin view

Inside a layout, `$this` is a `Hubzero\Plugin\View`, and `css()`, `js()`, and
`img()` are resolved as *view helpers*
(`Hubzero\View\Helper\Css` and friends). Their signature differs:

| Method | Signature |
|---|---|
| `css` | `css($stylesheet = '', $extension = null, $element = null)` |
| `js` | `js($asset = '', $extension = null, $element = null)` |
| `img` | `img($asset = '', $extension = null, $element = null)` |

The third argument is not attributes: passing it makes the second and third be
read as a plugin group and element, and the pair is assembled into
`plg_{group}_{element}`. These two lines load the same file:

```php
$this->css('like', 'plg_groups_forum');
$this->css('like', 'groups', 'forum');
```

> **Warning:** Passing an attributes array as the third argument in a layout —
> the signature that works on the plugin class — makes the helper treat it as
> an element name and splice it into the extension string. The result matches
> no directory, so nothing loads; all you get is an array-to-string conversion
> notice.

Layouts in `plg_groups_forum` and `plg_members_blog` call these with one
argument or none:

```php
$this->css()
     ->js();

$this->css('jquery.datepicker.css', 'system');
```

`system` resolves against the top-level `core/assets` directory, which is
where the shared jQuery plugins and icon sets live.

## Where the file is looked for

`Hubzero\Document\Asset\File` builds this list for a plugin and takes the
first path that exists:

```
app/plugins/{group}/{name}/assets/css/file.css
app/plugins/{group}/{name}/css/file.css
core/plugins/{group}/{name}/assets/css/file.css
core/plugins/{group}/{name}/css/file.css
```

Every `app/` path is tried before any `core/` path, so a hub replaces a
shipped plugin's stylesheet by putting a file in `app/plugins`. A template can
override any of them with `{template}/html/plg_{group}_{name}/file.css`, which
is checked afterwards and wins.

The returned URL carries a cache-buster from the file's modification time:
`/core/plugins/members/blog/assets/css/blog.css?v=1568392841`.

> **Warning:** When the file cannot be found, nothing happens — no exception,
> no warning, no tag in the document. A stylesheet that "isn't loading" is
> nearly always a name that does not match a file, or a file outside
> `assets/`.

## Declarations

Passing `css()` a string containing `{` or `@` makes it a declaration: the
string is added with `addStyleDeclaration()` rather than resolved to a path.
Useful for a rule computed from a parameter; not a way to build a stylesheet.

## Images

`img()` returns a URL for a file under `assets/img` and keeps the extension
you give it:

```html
<img src="<?php echo $this->img('icon.png'); ?>" alt="" />
```

Only `png`, `gif`, `jpg`, `jpeg`, and `jpe` are recognised. For an SVG, build
the path from `Request::root(true)` yourself.

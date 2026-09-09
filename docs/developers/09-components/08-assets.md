<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/components/assets
-->
# Assets

Stylesheets, scripts, and images belong to a client, not to a component as a
whole — the administrator interface and the public site rarely want the same
CSS. Each client directory therefore has its own `assets` tree:

```
core/components/com_kb/
    site/assets/
        css/kb.css
        js/kb.js
    admin/assets/
        js/kb.js
```

`css`, `js`, and `img` are the directories the helpers know about by name.
Anything else — `fonts`, `less`, `scss` — is yours to organise, and is
addressed with an explicit path.

## Attaching them

`css()`, `js()`, and `img()` are available on every controller, through the
[`AssetAware`](../../../core/libraries/Hubzero/Base/Traits/AssetAware.php)
trait, and in every view, through the
[view helpers](../../../core/libraries/Hubzero/View/Helper/AbstractHelper.php). They chain, and
the usual call sits at the top of a layout:

<!--include: core/components/com_kb/site/views/articles/tmpl/display.php:11-12-->

Both arguments have defaults, and both defaults are what you usually want:

| Argument | Default |
|---|---|
| the asset name | the component name without `com_`, so `com_kb` looks for `kb.css` |
| the extension | the component currently running |

So `$this->css()` in a `com_kb` view attaches `kb.css` from `com_kb`'s own
assets. `$this->css('print')` attaches `print.css` from the same place.
`$this->css('tags', 'com_tags')` reaches into another component. The file
extension is optional — `css('print')` and `css('print.css')` are the same
call.

Passing `'system'` as the extension reads from the platform's own assets in
`core/assets`.

`img()` is the odd one: it attaches nothing, and returns a URL.

```html
<img src="<?php echo $this->img('helpful.png'); ?>" alt="" />
```

> **Note:** The two implementations differ in their third argument. On a
> controller it is an array of HTML attributes — `media`, `defer`, `async`.
> In a view it is a plugin element name, used only when the second argument is
> a plugin folder: `$this->css('style', 'members', 'dashboard')` resolves to
> `plg_members_dashboard`. Passing an attribute array to the view helper does
> nothing.

## How a file is found

The name and extension are turned into a list of candidate paths by
[`Hubzero\Document\Asset\File`](../../../core/libraries/Hubzero/Document/Asset/File.php),
and the first that exists wins. For a component, with `{client}` being `site`
or `admin`:

1. `PATH_APP/components/com_kb/{client}/assets/css/kb.css`
2. `PATH_APP/components/com_kb/{client}/css/kb.css`
3. the same two under `PATH_CORE`

Every `app` path is tried before every `core` path, so a hub can replace a
single stylesheet by putting its own copy in `app/components/`. The `assets`
directory is checked before the bare one, which is why the recommended layout
is worth following: it works with no configuration.

A leading `./` in the name drops the `css`/`js`/`img` directory, so
`css('./custom')` looks for `{client}/assets/custom.css`. A leading `/` makes
the path relative to `PATH_ROOT` instead. A name beginning `http`, `//`, or
`://` is treated as external and passed through untouched.

## Template overrides

Before serving what it found, the asset checks the active template for an
override at `{template path}/html/{extension}/{file}` — for example
`app/templates/hubzero/html/com_kb/kb.css`. If that file exists it is used
instead. This is the same mechanism templates use to override layouts, and it
lets a template restyle a component without a copy of the component.

## Cache busting

`link()` appends the file's modification time as a query string:

```
/core/components/com_kb/site/assets/css/kb.css?v=1583172290
```

The URL changes whenever the file does, so browsers pick up an edit without
being told to clear anything.

## Inline styles and scripts

The same helpers take raw source rather than a file name, and add it as a
declaration:

```php
$this->css('#content .article { margin-top: 0; }')
     ->js('jQuery(document).ready(function ($) { /* ... */ });');
```

Detection is by content, not by a flag. A stylesheet argument containing `{`
or `@` is treated as CSS source; a script argument containing `(` or `;` is
treated as JavaScript source. An empty extension name also forces a
declaration.

> **Warning:** That heuristic is easy to trip over. A file name is never
> allowed to contain those characters, so a stylesheet called `theme@2x.css`
> is silently injected into the page as CSS source rather than linked. Keep
> asset file names to letters, digits, hyphens, and underscores.

Declarations are emitted as inline `<style>` and `<script>` blocks in the
document head, which a strict content security policy rejects. Prefer a file:
put behaviour in a `.js` file under `assets/js` and drive it from `data-`
attributes on the markup, rather than writing script into the page.

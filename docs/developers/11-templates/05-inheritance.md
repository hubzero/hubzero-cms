<!--
status: new
reviewed-against: 2.4-dev @ 5806e983c4
reviewed: 2026-09-13
screenshots: none
-->
# Inheritance

A template can be a **child** of another: it names a parent, ships only the
files it wants to change, and inherits the rest.

This is the alternative to copying. [Structure](03-structure.md#where-it-lives)
describes copying a shipped template into `app/templates` and working from
there, which is how every hub's template has been built so far. The copy works,
but it stops receiving anything the original learns afterwards, and the hub is
left maintaining several hundred files to change a colour and a front page.

## Declaring one

The child names its parent in its own manifest:

```xml
<install type="template">
    <name>Northgate</name>
    <parent>lucent</parent>
    <inheritable>0</inheritable>
</install>
```

and the parent has to agree:

```xml
<install type="template">
    <name>Lucent</name>
    <inheritable>1</inheritable>
</install>
```

Both halves are required. A parent that does not declare `<inheritable>1</inheritable>`
cannot be inherited from, and a child naming a parent that refuses is treated
as having no parent at all — it renders from the system template, which is the
same symptom as a template with no `index.php`.

Of the shipped templates, only `lucent` is currently inheritable.

**One generation.** A template that is itself a child cannot be a parent, which
is what `<inheritable>0</inheritable>` in the child says. There is no chain to
walk and no way to write a loop.

A child still needs [registering](01-migrations.md) like any other template: a
row in `#__extensions` and a style in `#__template_styles`. It is a template
that happens to be short, not a new kind of extension.

## What is inherited

Everything the child does not carry, looked for in the parent:

| | Resolution |
|---|---|
| Page shells — `index.php`, `home.php`, `component.php`, `{tmpl}.php` | child, then parent, then `core/templates/system` |
| Output overrides in `html/` | child, then parent, then the extension's own views |
| Stylesheets, scripts, images via `$this->asset()` | child, then parent |
| Files found with `$this->templateFile()` | child, then parent |
| Module positions, parameters, language strings | **not inherited** — see below |

The search is per file, not per directory. A child that ships `js/hub.js` and
nothing else gets its own `hub.js` and the parent's `core.js`, each linked at
its own address.

The page keeps wearing the **child's** name throughout — its style, its body
classes, its identity in the administrator — whatever file happened to draw it.

## Addressing files

A child breaks the assumption a template's files are all in the directory the
template is named after, so do not build those addresses by hand:

```php
// Wrong in a child: __DIR__ is wherever the file being executed lives, and
// the file being executed is often the parent's.
$this->addScript($this->baseurl . '/templates/' . $this->template
    . '/js/core.js?v=' . filemtime(__DIR__ . '/js/core.js'));

// Right: asks the child, then the parent, and answers with the address of
// whichever has it - including the right root when the two are in different
// ones.
$this->addScript($this->asset('js/core.js'));
```

`asset()` returns the URL with the file's modification time as a cache-buster,
or the URL unversioned where no template in the chain has the file. It does not
throw. `templateFile()` is the same search answering with a filesystem path
instead, for a template that needs to `require` one of its own files.

See [Stylesheets](07-css.md) and [Javascript](08-javascript.md).

## What is not inherited

**Module positions.** `templateDetails.xml` is read for the position list as
written; a child declares the positions it renders, including any of the
parent's it keeps. This is deliberate — a child that alters `index.php` may
render an entirely different set — but it does mean copying that block when the
child inherits the parent's `index.php` unchanged.

**Parameters.** Same file, same reason. A child's parameters are its own.

**Language strings.** The loader loads `tpl_{name}` for the template in use.
A child that inherits its parent's `index.php` also inherits every
`Lang::txt()` call in it, so it needs its own language file carrying those
keys.

These three are the rough edges. They are the places a child is not yet as thin
as it could be.

## Where a hub sits in `app/` and its parent in `core/`

The usual arrangement. A hub's own template lives in `app/templates/northgate`
and inherits `core/templates/lucent`, and the two roots are addressed
separately: `northgate`'s files are served from `/app/templates/northgate/…`
and the inherited ones from `/core/templates/lucent/…`. `asset()` handles this;
hand-built URLs do not.

Note that `app/` still shadows `core/` **by name**, as
[Structure](03-structure.md#where-it-lives) warns: a directory at
`app/templates/lucent` replaces the shipped `lucent` entirely and is not a
child of it. Inheritance is opt-in through the manifest, never implied by a
name.

## What it does not fix

An existing fork. A template that has been copied and edited for years has
diverged past the point where a parent can supply anything useful, and pointing
it at one would mostly produce conflicts between two files that have both
moved. Inheritance is worth reaching for when starting a template, not as a way
of un-forking one that already exists.

## Code

- [`Hubzero\Template\Loader::resolveParent()`](../../../core/libraries/Hubzero/Template/Loader.php) —
  reads the manifests and settles `parent` and `parentPath` on the style
- [`Hubzero\Document\Type\Html::_fetchTemplate()`](../../../core/libraries/Hubzero/Document/Type/Html.php) —
  the shell search
- [`Hubzero\Document\Type\Html::asset()`](../../../core/libraries/Hubzero/Document/Type/Html.php) and
  `templateFile()` — the per-file search
- [`Hubzero\View\View::_setPath()`](../../../core/libraries/Hubzero/View/View.php) —
  the override search path

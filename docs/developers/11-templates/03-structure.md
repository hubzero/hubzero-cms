<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/templates/structure
source-id: 3506
modified: 2015-08-24
-->
# Structure

A template is a directory. This chapter says what goes in it, what is
required, and what the CMS does with each part.

Read it once before you start deleting things from the copy you made. Most of
a template directory is convention you can rename or drop; a small part of it
is looked for by name, and dropping that part fails silently rather than
loudly.

## Where it lives

Your templates go in `app/templates/{name}`. The templates that ship with the
CMS are in `core/templates/{name}` and are replaced on upgrade, so do not edit
them in place — copy one into `app/templates` instead, as the
[overview](README.md#where-to-start) describes. Nothing under `app/` is part of
the distribution.

[`Hubzero\Template\Loader`](../../../core/libraries/Hubzero/Template/Loader.php)
resolves a style to a directory, checking `app/` before `core/`. A template in
`app/templates/kimera` therefore shadows the shipped `kimera` entirely.

> **Warning:** That shadowing is total and it is easy to do by accident. If you
> copy `kimera` and forget to change the destination name, every hub page that
> asks for the `kimera` style gets your copy instead — including any style an
> administrator set up earlier. Give the copy its own name (`northgate`) and
> register it as its own style.

## The tree

This is `kimera`, the fullest of the shipped site templates, and the tree
`northgate` inherits by copying it:

```
core/templates/kimera/
    css/               Compiled stylesheets
        browser/       Per-browser fixes: ie8.css, ie9.css
        pages/         Per-page stylesheets: home.css, community.css, …
        theme.php      Parameter-driven colours, served as CSS
    html/              Output overrides for other extensions
    img/               Images the template's own CSS refers to
    js/                Scripts: hub.js, html5.js
    language/
        en-GB/
            en-GB.tpl_kimera.ini
    less/              LESS sources for everything under css/
    migrations/
        Migration20170831000000TplKimera.php
    component.php      Layout for tmpl=component requests
    composer.json      Package metadata
    error.php          Layout for error pages
    favicon.ico
    index.php          The main layout
    offline.php        Layout shown when the site is offline
    templateDetails.xml
    template_thumbnail.png
```

Nothing here is magic except the names. `css/`, `js/`, `img/` and `less/` are
conventions your own stylesheets and `index.php` refer to; you can call them
what you like. The rest the CMS looks for by name.

## What is actually required

Only two things:

- **`index.php`.** If it is missing, `Hubzero\Document\Type\Html` silently
  falls back to `core/templates/system` and renders that instead. The symptom
  is a hub that looks unstyled rather than broken.
- **A row in `#__extensions`, and a style in `#__template_styles`.** The
  administrator's template list is a query against `#__extensions`, not a scan
  of the filesystem, so a directory nobody has registered is invisible — and,
  the other way round, a registered name whose directory is missing is still
  listed. Write a [migration](01-migrations.md) to register it.

`templateDetails.xml` is *not* required for the template to render. It is
required for the administrator to configure it: without it the template has no
parameters to edit and contributes no module positions. See
[Packaging](10-packaging.md).

## The parts

| Path | What reads it |
|---|---|
| `index.php` | The document, for every normal page. See [Page layout](06-layouts.md). |
| `{tmpl}.php` | The document, when the request carries `tmpl={name}`. `tmpl=component` gives modal windows and popups their bare frame; `com_help` sets `tmpl=help`, `com_cpanel` sets `tmpl=cpanel`, `com_login` sets `tmpl=login`, and `com_groups` sets `tmpl=group` for super group pages. |
| `error.php` | The error document. |
| `offline.php` | Rendered when the site is switched offline and the visitor lacks `core.login.offline`. |
| `email.php` | [`Hubzero\Mail\Template`](../../../core/libraries/Hubzero/Mail/Template.php), for HTML mail. |
| `templateDetails.xml` | `com_templates` for parameters and metadata, `com_modules` for the position list. |
| `language/{tag}/{tag}.tpl_{name}.ini` | Loaded automatically. See [Languages](02-languages.md). |
| `migrations/` | `muse migration`. See [Migrations](01-migrations.md). |
| `html/` | Every extension's asset and layout lookup. See [Output overrides](09-overrides.md). |
| `html/icons/{symbol}.svg` | `Html::asset('icon', …)`, overriding `core/assets/icons`. |
| `template_thumbnail.png` | The template list in the administrator. 206 pixels wide; `kimera`'s is 206×150. An optional `template_preview.png` beside it makes the thumbnail a link to the full-size image. |
| `favicon.ico` | Only if your `index.php` links to it. Nothing links it for you. |
| `composer.json` | Composer, when the template is installed as a package. |

## What the shipped templates leave out

Not one of them has the full tree, which is a useful measure of what you can
skip:

| | `kimera` | `lucent` | `welcome` | `kameleon` | `system` |
|---|---|---|---|---|---|
| `index.php` | yes | yes | yes | yes | yes |
| `component.php` | yes | yes | — | yes | yes |
| `error.php` | yes | yes | — | yes | yes |
| `templateDetails.xml` | yes | yes | yes | yes | — |
| `composer.json` | yes | — | yes | yes | yes |
| `migrations/` | yes | yes | yes | yes | — |
| `html/` overrides | 32 files | — | — | 4 files | — |
| `template_thumbnail.png` | yes | — | — | yes | — |

`system` is the fallback and is never registered as a style, which is why it
has neither a manifest nor a migration. It also holds the shared `email.php`,
`group.php`, `help.php`, `login.php` and `offline.php` layouts that other
templates inherit when they do not define their own.

## What to keep in `northgate`

Working from the `kimera` copy, this is the honest division:

| Keep | Because |
|---|---|
| `index.php`, `component.php`, `error.php` | Three of the four layouts a visitor can reach. Restyle all three or the hub looks unfinished inside every modal. |
| `less/` and the compiled `css/` beside it | Where the branding actually goes. |
| `language/`, renamed | Every string the layout prints. |
| `migrations/`, renamed | Nothing works until this runs. |
| `templateDetails.xml` | The parameters and the position list. |

| Drop | Because |
|---|---|
| `css/pages/*.css`, `css/print.css`, `css/download.css`, `css/upload.css` | No layout links them and nothing pushes them. They are dead in `kimera` too. See [Cascading style sheets](07-css.md). |
| `css/browser/ie8.css`, `css/browser/ie9.css` and their conditional comments | Conditional comments stopped working at Internet Explorer 10. |
| `js/html5.js` | The HTML5 shiv, for the same browsers. |
| Most of `html/` | 32 per-component stylesheets tuned to `kimera`'s colours. Keep the ones for components your hub actually shows — for `northgate`, that means the `com_bookings` overrides you write, not `kimera`'s `com_projects` ones. |

> **Note:** `lucent` ships a `templateDetails.xml` whose root element is
> `<install>` rather than `<extension>`, with an empty `<files>` list and a
> stale GPLv2 `<license>`. Both root tags are accepted, but copy `kimera`'s
> manifest rather than `lucent`'s.

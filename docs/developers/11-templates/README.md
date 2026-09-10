<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/templates
source-id: 3503
-->
# Templates

A template is the set of files that turn a hub's content into pages. It owns
the document shell, the module positions, the stylesheets and the scripts.
It is not the site: the template supplies the frame, and components, modules
and plugins supply what goes inside it.

These chapters are for developers writing a template against the Hubzero
framework. They assume you are comfortable with HTML, CSS and PHP.

## Where templates live

Templates are found in two places:

- `core/templates/` — the templates that ship with the CMS. They are marked
  *protected* and are replaced on upgrade, so do not edit them in place.
- `app/templates/` — the templates belonging to this hub. Put your own work
  here.

[`Hubzero\Template\Loader`](../../../core/libraries/Hubzero/Template/Loader.php)
looks in `app/templates/{name}` first and falls back to
`core/templates/{name}`, so a template in `app/` of the same name overrides a
shipped one entirely. If neither has an `index.php`, the loader falls back to
`core/templates/system`.

## What ships

| Template | Client | Notes |
|---|---|---|
| `kimera` | Site | The default site template. LESS sources, a colour and background theme in its parameters, and a large set of output overrides in `html/`. |
| `lucent` | Site | A newer site template. Registered but not made the home style by its migration. |
| `welcome` | Site | A single-page welcome/splash template, used before a hub has content. |
| `system` | Site | The fallback. Also holds the default `email.php`, `group.php`, `help.php`, `login.php` and `offline.php` layouts. |
| `kameleon` | Administrator | The administration template, with a set of colour themes. |

Run `git ls-files core/templates` to see the whole tree.

> **Note:** `kimera` and `lucent` are the two worth reading before you start.
> `kimera` shows the override-heavy approach; `lucent` shows a leaner one.

## Where to start

The quickest start is to copy a shipped template into `app/templates` and work
from there:

```bash
php core/bin/muse scaffolding copy template core/kimera to app/mytemplate
```

That copies the directory and rewrites the template name inside every copied
file. Then write a [migration](01-migrations.md) so the CMS knows the
template exists.

> **Warning:** The copy rewrites file *contents* only. Filenames keep the
> source template's name, so after copying you must rename
> `language/en-GB/en-GB.tpl_kimera.ini` and the file in `migrations/` yourself,
> or the language file will not load and the migration class will not match
> its filename.

## The chapters

- [Migrations](01-migrations.md) — registering the template with the CMS.
- [Languages](02-languages.md) — translatable strings.
- [Structure](03-structure.md) — the files and directories a template holds.
- [Designing](04-designing.md) — planning the design before you write markup.
- [Page layout](06-layouts.md) — `index.php`, `error.php` and the rest.
- [Cascading style sheets](07-css.md) — the stylesheets a template loads.
- [JavaScript](08-javascript.md) — jQuery, template scripts and extension assets.
- [Output overrides](09-overrides.md) — replacing an extension's markup.
- [Packaging](10-packaging.md) — the manifests and how a template is installed.
- [Socicons](11-socicons.md) and [Fontcons](12-fontcons.md) — the icon fonts.
- [Elements and typography](13-elements.md) — the shared grid, buttons and
  notification styles.
- [Accessibility](14-accessibility.md) — what the shipped templates and the
  HTML helpers emit, and where the gaps are.

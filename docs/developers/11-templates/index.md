<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
screenshots: none
-->
# Templates

A template is the set of files that turn a hub's content into pages. It owns
the document shell, the module positions, the stylesheets and the scripts.
It is not the site: the template supplies the frame, and components, modules
and plugins supply what goes inside it.

These chapters are for developers writing a template against the Hubzero
framework. They assume you are comfortable with HTML, CSS and PHP.

## When you want one

Reach for a template when the thing you are changing is **the page around
everything else** — the masthead, the navigation, the colours, the footer,
where the sidebar sits. A template applies to every page on the hub at once,
and switching it changes the look of components you did not write.

Reach for something narrower when the change is narrower:

| You want | Build |
|---|---|
| The whole hub to look like an institution's site | a template |
| One component's markup changed, everything else left alone | an [output override](09-overrides.md) inside your template |
| A block of content in a sidebar | a [module](../08-modules/index.md) |
| A screen with records behind it | a [component](../09-components/index.md) |

An override lives inside a template, so the two are not really alternatives:
you get a template first, then put overrides in it.

## The example these chapters build

One template runs through this section: **`northgate`**, the site template for
Northgate University, a partner institution on a hub whose main application is
[`com_bookings`](../09-components/index.md) — the component that books a lab's
instruments.

It is deliberately not built from nothing. `northgate` is a copy of the shipped
`kimera` template with Northgate's colours, masthead and footer, plus a handful
of output overrides that restyle `com_bookings` to match. That is what almost
every real hub template is, and it is the route these chapters take:

1. [Copy a shipped template](#where-to-start) into `app/templates/northgate`.
2. [Register it](01-migrations.md) with a migration, or the CMS cannot see it.
3. Rename its [language file](02-languages.md) and rewrite its strings.
4. Rework the [layout](06-layouts.md) and the [stylesheets](07-css.md).
5. Add [output overrides](09-overrides.md) for the components that need them.

Where a chapter shows `kimera`, `lucent`, `welcome`, `kameleon` or `system`,
that is code you can open in this repository. Where it shows `northgate`, that
is what you write.

## Where templates live

Templates are found in two places:

- `core/templates/` — the templates that ship with the CMS. They are replaced
  on upgrade, so do not edit them in place.
- `app/templates/` — the templates belonging to this hub. Put your own work
  here. Nothing under `app/` is part of the distribution; it is the hub's own
  directory and it is not in this repository.

[`Hubzero\Template\Loader`](../../../core/libraries/Hubzero/Template/Loader.php)
resolves a style to a directory, checking `app/templates/{name}` first and
falling back to `core/templates/{name}`, so a template in `app/` of the same
name overrides a shipped one entirely. If neither has an `index.php`, the
loader falls back to `core/templates/system`.

> **Warning:** That fallback is silent. A template whose `index.php` is missing,
> misspelled or unreadable does not error — the hub simply renders in the bare
> `system` template, which looks like a stylesheet failing to load. If a hub
> suddenly goes unstyled, check that `index.php` exists at the resolved path
> before you look at anything else.

## What ships

| Template | Client | Notes |
|---|---|---|
| `kimera` | Site | The reference site template. LESS sources (LESS is a stylesheet language the CMS compiles to CSS), a colour and background theme in its parameters, and a large set of output overrides in `html/`. Copy this one. |
| `lucent` | Site | A newer site template. Leaner, with layered LESS. Registered but not made the home style by its migration, and its manifest is in the older form. |
| `meridian` | Site | "A hub before it has a subject": a complete site layout with no subject's imagery. It is the home style on a fresh hub — see below. |
| `mesozoic` | Site | The same layout dressed for a hub about deep time; a worked example of a themed template. |
| `system` | Site | The fallback. Also holds the default `email.php`, `group.php`, `help.php`, `login.php` and `offline.php` layouts that other templates inherit. |
| `kameleon` | Administrator | The administration template, with a set of colour themes. |

Those six are the whole set; nothing else lives under `core/templates`.

> **Note:** `kimera` and `lucent` are the two worth reading before you start.
> `kimera` shows the override-heavy approach and is what `northgate` is copied
> from; `lucent` shows a leaner one.

## What a fresh hub renders

The install data makes **`meridian`** the home style for the site client:

```sql
INSERT INTO `#__template_styles` (`id`, `template`, `client_id`, `home`, `title`, `params`)
VALUES (1,'meridian',0,'1','Meridian, a hub before it has a subject','{}');
```

[`meridian/index.php`](../../../core/templates/meridian/index.php) is a whole
layout: it includes the component, system messages and its module positions,
so a fresh hub renders its pages straight away.

One thing on a fresh hub does not render, and it is worth knowing before you
write a line of markup. The install data publishes a **Login Form** module to a
position named `position-7`, and no shipped site template includes that
position. The **Main Menu** was seeded there too;
`Migration20260913090000MainMenu` moves it to `user3`, which `meridian` draws
as its main navigation, and builds a menu for a hub that has none. [Page
layouts](06-layouts.md#why-a-position-renders-nothing) has the full account and
the check to run.

## Where to start

Copy a shipped template into `app/templates` and work from there:

```bash
php core/bin/muse scaffolding copy template core/kimera to app/northgate
```

That copies the directory and rewrites the template name inside the copied
files. Then write a [migration](01-migrations.md) so the CMS knows the template
exists.

> **Warning:** The copy is shallower than it looks.
> [`Scaffolding::make()`](../../../core/libraries/Hubzero/Console/Command/Scaffolding.php)
> rewrites the template name inside the files at the **top level** of the copied
> directory — `index.php`, `component.php`, `error.php`, `templateDetails.xml` —
> and does not descend into subdirectories. `less/`, `css/`, `language/`,
> `migrations/` and `html/` arrive verbatim. No file is renamed either. After
> the copy you must, by hand:
>
> - rename `language/en-GB/en-GB.tpl_kimera.ini` to `en-GB.tpl_northgate.ini`
>   and rewrite its `TPL_KIMERA_*` keys, or none of its strings load;
> - rename `migrations/Migration20170831000000TplKimera.php` and its class, or
>   the migration runner skips the file and — if the shipped `kimera` is still
>   installed — PHP fatals on the duplicate class name;
> - fix anything in a subdirectory that names the source template. In `kimera`
>   that is `@pathTemplate: "/core/templates/kimera"` in `less/_variables.less`
>   — currently unused, and wrong the moment you use it.
>
> The `@import "../../../../core/assets/less/…"` paths do survive the move:
> `app/templates/northgate/less/` is the same depth below the repository root as
> `core/templates/kimera/less/`.

## The chapters

- [Migrations](01-migrations.md) — registering the template with the CMS.
- [Languages](02-languages.md) — translatable strings.
- [Structure](03-structure.md) — the files and directories a template holds.
- [Designing](04-designing.md) — planning the design before you write markup.
- [Page layout](06-layouts.md) — `index.php`, `home.php`, `error.php` and the
  rest.
- [Cascading style sheets](07-css.md) — the stylesheets a template loads.
- [JavaScript](08-javascript.md) — jQuery, template scripts and extension assets.
- [Output overrides](09-overrides.md) — replacing an extension's markup.
- [Packaging](10-packaging.md) — the manifests and how a template is installed.
- [Socicons](11-socicons.md) and [Fontcons](12-fontcons.md) — the icon fonts.
- [Elements and typography](13-elements.md) — the shared grid, buttons and
  notification styles.
- [Accessibility](14-accessibility.md) — what the shipped templates and the
  HTML helpers emit, and where the gaps are.

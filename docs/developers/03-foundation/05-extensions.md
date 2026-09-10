<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/foundation/extensions
source-id: 3442
modified: 2015-07-06
imported: 2026-09-09
-->
# Extensions

Almost nothing a hub does is in the framework. The framework boots a
container, works out which client and which component the URL names, and
hands the result to a template; the features are extensions on top of it.
There are four kinds, and each is found and run a different way.

This page is the platform's side of that: what each kind is, and the code
that loads it. The authoring side — package layout, manifests, parameters,
languages, deployment — is in [Extensions](../extensions/README.md), and
each kind then has a book of its own.

## The four kinds

| Kind | Directory | Loader | Found through |
|---|---|---|---|
| Component | `components/com_{name}` | `Hubzero\Component\Loader` | `#__extensions` |
| Plugin | `plugins/{group}/{name}` | `Hubzero\Plugin\Loader` | `#__extensions` |
| Module | `modules/mod_{name}` | `Hubzero\Module\Loader` | `#__modules`, `#__modules_menu` |
| Template | `templates/{name}` | `Hubzero\Template\Loader` | `#__template_styles` |

Each loader searches `PATH_APP` before `PATH_CORE` and stops at the first
directory it finds, so a hub replaces a shipped extension by putting a
directory of the same name under `app/`. See
[Structure](01-structure.md#overriding-a-core-extension).

## Components

A component is an application in its own right: controllers, models, its own
tables, its own routes, its own views, and up to three faces — `site/`,
`admin/` and `api/`. Exactly one component handles a request, the one named
by `option`, and it renders into the template's main content area. A menu is
in effect a switch between components.

[`Hubzero\Component\Loader`](../../../core/libraries/Hubzero/Component/Loader.php)
does the work. `render($option)`:

1. Reads the component's row from `#__extensions` — cached for `cachetime`
   minutes — and aborts with a 404 if it is missing or disabled.
2. Defines `PATH_COMPONENT`, `PATH_COMPONENT_SITE` and
   `PATH_COMPONENT_ADMINISTRATOR`, plus the `JPATH_` aliases.
3. Picks an entry point, in this order: a
   `Components\{Name}\{Client}\Bootstrap` class if one autoloads, then
   `{client}/{name}.php`, then a React application under
   `{client}/assets/react/{name}`, and failing all three its own default
   dispatcher.
4. Loads the component's language file and executes the entry point with
   output buffering on, returning what it printed.

`Component::params($option)` returns the `params` column of the same cached
row as a `Registry`.

## Plugins

A plugin owns no URL. Its public methods are named after events —
`onAfterRoute`, `onContentPrepare`, `onGroupView` — and the dispatcher calls
them when something triggers one. Plugins are grouped by what they extend,
and the group is the directory: `plugins/authentication/`,
`plugins/content/`, `plugins/members/`. Most of the pluggable behaviour in
the CMS is a plugin group, and the
[events reference](../../reference/events/README.md) lists what the tree
triggers.

[`Hubzero\Plugin\Loader`](../../../core/libraries/Hubzero/Plugin/Loader.php)
builds its list once per request, from `#__extensions`:

```sql
SELECT folder AS type, element AS name, protected, params
  FROM `#__extensions`
 WHERE enabled >= 1 AND type = 'plugin' AND state >= 0
   AND access IN (<the current user's viewing levels>)
 ORDER BY ordering ASC
```

`import($type)` then loads every plugin in a group, instantiates each, and
binds its public methods to the dispatcher by name. `ordering` decides who
runs first within a group. A plugin whose access level the current user does
not hold is never loaded at all — not loaded and skipped, but absent — so
there is nothing to guard against in the plugin itself.

## Modules

A module renders a small block of HTML into a named template position: a
login form, a breadcrumb trail, a list of recent entries. It never owns the
request, and the same module can be published several times, in different
positions, with different parameters.

That is why modules are the one kind with a second table.
`#__extensions` registers the module *type*; `#__modules` holds the
instances, and `#__modules_menu` says which menu items each appears on.
[`Hubzero\Module\Loader`](../../../core/libraries/Hubzero/Module/Loader.php)
loads the published instances for the current client whose access level the
user holds and whose menu assignment matches, ordered by `ordering`;
`byPosition($position)` filters that list, and the template asks for a
position at a time.

Rendering a module is an `include` of `mod_{name}.php` with output buffering
on, wrapped in a *chrome* function from the template's
`html/modules.php` that supplies the surrounding markup.

> **Note:** On this release a module's `mod_{name}.php` is a stub that
> requires `helper.php`, and the loader includes the file rather than
> instantiating a class. Do not expect a class-per-module.

## Templates

A template is the page around whatever the component produced: the markup,
the CSS, the positions modules render into, and the error and offline pages.
It is not the site design and not a website — it is the frame the content is
dropped into.

Two providers share the work.
[`Hubzero\Template\Loader`](../../../core/libraries/Hubzero/Template/Loader.php),
bound as `template` by `TemplateServiceProvider`, picks the style marked
`home` for the current client out of `#__template_styles` joined against
`#__extensions`, and caches the result for `cachetime` minutes.
`DocumentServiceProvider` is [middleware](01-structure.md#handle): on the way
back out it calls `render()` on the document with that template, which is
what produces the finished page.

The templates that ship are in `core/templates`:

| Template | Client |
|---|---|
| `kimera` | Site |
| `kameleon` | Administrator |
| `system` | Shared fallback layouts, error pages, module chrome |
| `lucent` | An alternative site template |
| `welcome` | The first-run landing page |

A template can override any extension's view without copying the extension.
See [Overrides](../templates/overrides.md).

## Languages

A language is not an extension. It is a set of INI files and an XML metadata
file, loaded by `Hubzero\Language\Translator`, and Hubzero ships `en-GB`
only. Earlier versions of this page called languages a fifth extension type;
they are not one. See [Languages](../basics/languages.md).

## Nothing scans the filesystem

Worth saying plainly, because it is the first thing that trips people up: a
directory on disk is not an extension. Every loader above reads a database
table. Copy a component into `app/components/` and visit its URL and you get
a 404, because there is no `#__extensions` row; copy a plugin in and no
event ever reaches it. A [migration](../database/migrations.md) writes that
row, which is why every extension ships one. See
[Deploying extensions](../extensions/deployext.md).

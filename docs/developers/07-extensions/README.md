<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/extensions
source-id: 3466
modified: 2017-04-17
imported: 2026-09-09
-->
# Extensions

Everything a hub does beyond serving a request is an extension. The CMS
itself is a thin application that boots a container, works out which
component owns the URL, and asks a template to draw the result; the features
— resources, groups, projects, publications, the wiki — are components,
plugins, modules and templates sitting on top of it.

This section covers what all four kinds have in common: what a package must
contain, how parameters are declared, how strings are translated, and how
code gets onto a running hub. Each kind then has its own chapter set —
[Components](../09-components/README.md), [Plugins](../10-plugins/README.md),
[Modules](../08-modules/README.md), [Templates](../11-templates/README.md) — for
the parts that differ.

## The four kinds

| Kind | Lives in | Owns | Registered as |
|---|---|---|---|
| Component | `components/com_{name}` | The page's main content, one per request | `type = 'component'` |
| Plugin | `plugins/{group}/{name}` | A response to an event | `type = 'plugin'`, `folder = '{group}'` |
| Module | `modules/mod_{name}` | A block in a template position | `type = 'module'` |
| Template | `templates/{name}` | The page around the component | `type = 'template'` |

### Components

A component is an application in its own right: its own controllers, models,
database tables, routes, views and administrative interface. Exactly one
component handles each request — the one named by `option` in the URL, which
the router derives from the menu item — and it renders into the template's
main content area. A menu is, in effect, a switch between components.

A component is the only kind with three faces: `site/` for hub visitors,
`admin/` for the administrator interface, and `api/` for the REST API. See
[Components](../09-components/README.md).

### Plugins

A plugin answers events. It declares no routes and owns no page; instead its
public methods are named after events — `onAfterRoute`, `onContentPrepare`,
`onGroupView` — and the dispatcher calls them when something triggers one.
Plugins are grouped by the kind of thing they extend, and the group is the
directory: `plugins/authentication/`, `plugins/content/`,
`plugins/members/`. Most of the CMS's pluggable behaviour — the tabs on a
group page, the login methods, the cron jobs — is a plugin group. The
[events reference](../../reference/events/README.md) lists what the tree
triggers. See [Plugins](../10-plugins/README.md).

### Modules

A module renders a small block of HTML into a named position in the
template: a login form, a breadcrumb trail, a list of recent entries. It
never owns the request. The same module can be published in different
positions on different templates and appear several times with different
parameters. See [Modules](../08-modules/README.md).

### Templates

A template is the page around whatever the component produced — the markup,
the CSS, the positions modules render into, and the error and offline pages.
The templates that ship are in `core/templates`: `kimera` for the site,
`kameleon` for the administrator interface, plus `system`, `lucent` and
`welcome`. See [Templates](../11-templates/README.md).

> **Note:** The old version of this page listed languages as a fifth
> extension type. A language pack is a set of INI files and an XML metadata
> file, not code, and Hubzero ships only `en-GB`. Translating is covered in
> [Languages](03-languages.md).

## Where extensions live

Two trees hold the same shapes:

| Tree | What is in it |
|---|---|
| `core/` | The extensions the release ships. Updated wholesale by an upgrade. |
| `app/` | This hub's own extensions, and its overrides of core ones. Not in the repository. |

The loaders check `app/` before `core/` and use the first directory they
find, so a hub replaces a core extension by putting a directory of the same
name under `app/`. That is an all-or-nothing replacement: once
`app/components/com_blog` exists, nothing under `core/components/com_blog`
is used. To change a few files rather than a component, use a
[template override](../11-templates/09-overrides.md) instead.

The class loader,
[`Hubzero\Base\ClassLoader`](../../../core/libraries/Hubzero/Base/ClassLoader.php),
follows the same rule for classes: `Components\Blog\Models\Entry` is looked
for under `app/components/com_blog` first, then `core/components/com_blog`.
Composer's PSR-4 map covers only the `Hubzero\` and `Bootstrap\` namespaces;
every extension class comes through this loader.

## How an extension is found

Nothing scans the filesystem. Every extension has a row in the
`#__extensions` table, and that row — not the directory — is what the
loaders read. A component with no row 404s; a plugin with no row never
receives an event. The row also holds the extension's parameters, in its
`params` column.

An extension creates its own row from a
[migration](../06-database/02-migrations.md).
[Extensions](../03-foundation/05-extensions.md) in the Foundation section covers
each loader in detail; [Requirements](01-extreqs.md) covers what else a
package must carry.

## In this section

- [Requirements](01-extreqs.md) — what an extension package has to contain
  before the platform will load it.
- [Parameters](02-parameters.md) — declaring the settings an administrator
  edits, and the field types available.
- [Languages](03-languages.md) — where an extension's strings go, and how
  they are named.
- [Deploying extensions](04-deployext.md) — installing code on a running
  hub.

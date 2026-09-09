<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/modules
-->
# Modules

A module is a small, repeatable block of output that a template drops into a
named position on the page: a login form, a list of the groups you belong to,
a breadcrumb trail. Unlike a component, a module never owns the request. It is
handed the page's parameters, renders some HTML into an output buffer, and the
buffer is spliced into the template.

Modules live in `core/modules` (the ones that ship with the CMS) and in
`app/modules` (the ones a hub adds or overrides). Every module directory is
named after the module and prefixed with `mod_`, and the same name is used for
the entry file, the manifest, the language files, and the row in the
`#__extensions` table.

## What a module is made of

At minimum a module is two files: an entry point named after the directory,
and an XML manifest. Nearly every real module adds a helper class, a layout,
and a language file:

```
core/modules/mod_mygroups/
    assets/
        css/mod_mygroups.css
        js/mod_mygroups.js
    language/en-GB/
        en-GB.mod_mygroups.ini
        en-GB.mod_mygroups.sys.ini
    migrations/
        Migration20190109000000ModMyGroups.php
    tmpl/
        default.php
        simple.php
        _item.php
    composer.json
    helper.php
    mod_mygroups.php
    mod_mygroups.xml
```

The entry file is included by [`Hubzero\Module\Loader`](../../../core/libraries/Hubzero/Module/Loader.php)
with two variables already in scope, `$params` and `$module`. It hands both to
a helper class extending [`Hubzero\Module\Module`](../../../core/libraries/Hubzero/Module/Module.php),
which gathers data and requires a layout out of `tmpl/`.

> **Note:** Module classes are namespaced (`namespace Modules\MyGroups;`) but
> the layouts they require are not. A namespaced class must `use` every facade
> it calls — `use User;`, `use Request;` — or the call resolves to a class in
> the module's own namespace and fatals. See
> [Facades](../03-foundation/04-facades.md). Layouts run in the global
> namespace and need no imports.

## Placing a module

Installing a module makes the CMS aware that it exists; it does not put it on
a page. An administrator creates an *instance* of the module in the Module
Manager, gives it a title, assigns it to a template position, sets its access
level and its menu assignments, and publishes it. Several instances of the
same module can exist at once, each with its own parameters. Everything the
loader knows about an instance comes from the `#__modules` and
`#__modules_menu` tables.

## In this section

- [Migrations](01-migrations.md) — registering the module with the CMS.
- [Structure](02-structure.md) — the directory layout and where the loader
  looks for it.
- [Controllers](03-controllers.md) — the entry file, and what is in scope
  when it runs.
- [Helpers](04-helpers.md) — the module class, its properties, and caching.
- [Languages](05-languages.md) — translation files and how they are loaded.
- [Views](06-views.md) — layouts, `getLayoutPath()`, and template overrides.
- [Assets](07-assets.md) — pushing CSS and JavaScript to the document.
- [Packaging](08-packaging.md) — the Composer and XML manifests.
- [Loading](09-loading.md) — rendering modules from templates, components,
  and article content.

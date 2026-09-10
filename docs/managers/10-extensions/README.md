<!--
status: rewritten
reviewed-against: 2.4-main @ 123ea53b14
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/extensions
source-id: 3405
imported: 2026-09-09
-->
# Extensions

Everything Hubzero puts on a page comes from an extension. The platform ships
with a large set of them, and a hub can add its own. This section covers the
four administrator screens that manage extensions, all reached from the
**Extensions** menu in the administrator interface.

| Screen | What it manages |
|---|---|
| [Extension Manager](04-extension-manager.md) | Enabling, disabling and updating every installed extension |
| [Module Manager](01-modules.md) | Module instances, their positions and their page assignments |
| [Plug-in Manager](03-plugins.md) | Plugin state, access level and run order |
| [Template Manager](02-templates.md) | Template styles, the default template, and template source files |

A fifth entry, **Language Manager**, opens `com_languages`. It has its own
chapter, [Language Manager](../09-components/20-languages.md), among the
[component chapters](../09-components/README.md).

## The extension types

### Components

A component is an application. It has its own controllers, models, views,
database tables, administrator screens and access rules, and it renders the
main body of the page. Exactly one component runs per request; a menu item is
essentially a switch that decides which one. Components live in
[`core/components/`](../../../core/components), and a hub's own components in
`app/components/`. Their names begin with `com_`: `com_resources`,
`com_groups`, `com_publications`.

Components are not created or removed from the Extensions screens. They are
enabled and disabled from the [Extension Manager](04-extension-manager.md),
configured from their own **Options** button, and given menu items from the
[menu manager](../07-menus.md).

### Modules

A module is a small block of output placed around the component, in a named
position defined by the template: a login box, a breadcrumb trail, a site
notice, a list of the newest resources. Modules live in
[`core/modules/`](../../../core/modules) and are named `mod_login`,
`mod_breadcrumbs`, and so on. One hundred ship with the core, 78 for the site
and 22 for the administrator interface.

Unlike a component, a module is instantiated. The same `mod_custom` code can
back a dozen separate blocks, each with its own title, position, access level
and set of pages. That is what the [Module Manager](01-modules.md) edits.

### Plugins

A plugin answers events. Something in the platform triggers an event —
a user logs in, a resource is saved, a page of content is about to be
rendered — and every enabled plugin that implements a handler for it runs, in
order. Plugins live in [`core/plugins/`](../../../core/plugins), grouped into
folders by the kind of event they answer: `authentication`, `content`,
`members`, `groups`, `cron`, `system`, and about thirty more.

Plugins also supply whole tabbed sections of some components. The tabs on a
group page, on a member profile and on a project are each a plugin in the
`groups`, `members` and `projects` folders.

### Templates

A template controls presentation. It supplies `index.php` (the page
skeleton), stylesheets, scripts, the list of module positions it offers, and
optional overrides of any component or module layout. Templates live in
[`core/templates/`](../../../core/templates), and a hub's own in
`app/templates/`.

A template is not the same thing as a *style*. One template can have several
styles, each a saved set of that template's parameters, and styles are what
you assign to the site or to individual menu items. See
[Templates](02-templates.md).

### Languages

A language pack is a set of `.ini` files of key/value pairs, one file per
extension, plus an XML manifest describing the language. Every string the
interface renders comes from one of these keys, which is why this
documentation quotes labels as the language files spell them. Language packs
cover both the site and the administrator interface.

## Where extensions live

Hubzero looks for an extension in the hub's own directory first and falls back
to the core:

| Type | Hub | Core |
|---|---|---|
| Components | `app/components/com_name/` | `core/components/com_name/` |
| Modules | `app/modules/mod_name/` | `core/modules/mod_name/` |
| Plugins | `app/plugins/folder/name/` | `core/plugins/folder/name/` |
| Templates | `app/templates/name/` | `core/templates/name/` |

Nothing in `core/` should be edited on a running hub. To change core
behaviour, put a replacement of the same name under `app/`; it wins. The
[Extension Manager](04-extension-manager.md) calls those hub-side additions
**Custom Extensions** and installs them from a git repository.

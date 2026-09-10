<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/supergroups/components
source-id: 3526
modified: 2014-09-10
-->
# Components

A super group can carry components of its own: MVC extensions that live in the
group's directory, answer at a URL under the group, and never appear in the
hub's extension manager.

## When you want one

The Coastal Resilience Center starts with a [PHP page](04-php_pages.md) for
its tide-gauge status board, and that is the right size for one page. It
outgrows it the moment the centre wants a page per gauge, at
`/groups/coastal/gauges/pier-7`, with a list, a detail view, and a form for
the technician who corrects a bad reading. That is a controller, a view per
screen, a model and a router: a component.

| The group needs | Build |
|---|---|
| One page, no URLs below it | a [PHP page](04-php_pages.md) |
| A snippet inside editor-written prose | a [macro](03-custom_macros.md) |
| Several screens, records, and its own URL space, for this group | a super group component |
| The same thing for the whole hub | an ordinary [component](../09-components/README.md) |

The last row is the one to think hardest about. A super group component is
invisible to the hub: no entry in the extension manager, no `access.xml`, no
configuration screen, no administrator face, and nothing else on the hub can
link to it by option name. If a second group would want it, write a real
component and let both groups link to it.

## Turning them on

Super group components are off by default. An administrator switches them on
once for the whole hub:

1. Go to **Users** → **Groups** and select **Options**.
2. On the **Super Groups** tab, set **Super Group Components** to yes.
3. Select **Save & Close**.

With the option off, the group's `components/` directory is never looked at
and requests fall through to PHP pages and group pages instead — silently, so
a component that "does not exist" on a hub where it worked yesterday is worth
checking here first. The option is `super_components` in
[`config/config.xml`](../../../core/components/com_groups/config/config.xml);
see the [generated reference](../../reference/configuration/components/groups.md).

## The smallest thing that works

Two files, no router, no model:

```
app/site/groups/1051/components/com_gauges/gauges.php
```

```php
<?php
echo '<h2>Tide gauges</h2>';
```

That answers at `/groups/coastal/gauges`. Add the router and the controllers
once it renders.

## Structure

A super group component is the `site` half of a CMS component, flattened:

```
app/site/groups/1051/components/com_gauges/
├── gauges.php           entry file, required
├── router.php           optional
├── controllers/
│   └── readings.php
├── models/
│   └── reading.php
├── helpers/
├── views/
│   └── readings/
│       └── tmpl/
│           └── display.php
├── language/
│   └── en-GB/
│       └── en-GB.com_gauges.ini
└── assets/
    ├── css/
    └── js/
```

Two names are fixed: the directory is `com_<name>` and the entry file inside
it is `<name>.php`. Everything else is convention that the framework's own
defaults happen to follow.

There is no `admin`, no `api` and no `site` subdirectory. A super group
component is only ever dispatched on the site, so the directories a full
component keeps under `site/` sit at the top here. Writing controllers,
views, models and routes is otherwise the same job as in a
[CMS component](../09-components/README.md).

> **Note:** Older documentation said an XML manifest belongs here and that a
> super group component can be moved into the main components directory
> unchanged. Neither is true. Nothing reads a manifest in a group directory,
> and moving the component would mean moving everything but `router.php` down
> into a `site/` directory first.

## How it is reached

The second segment of the group URL becomes the component name:

```
/groups/coastal/gauges          ->  components/com_gauges/gauges.php
```

[`Components\Groups\Helpers\View::superGroupComponents()`](../../../core/components/com_groups/helpers/view.php)
checks for the directory and the entry file, and gives up quietly if either is
missing. It runs before PHP pages and before group pages, so a component
shadows both.

The same collisions apply as for [PHP pages](04-php_pages.md#when-a-php-page-is-reached):
a name that matches an enabled group plugin, or one of the segments the group
router claims for itself, never reaches the component. So `com_files`,
`com_members`, `com_wiki` and `com_media` are all names a group cannot use:
the segment is taken before the component is looked for.

## What the entry file gets

The file is `include`d and everything it prints becomes the component's
output. Before it runs, the hub defines:

```php
JPATH_GROUPCOMPONENT   // /path/to/app/site/groups/1051/components/com_gauges
```

`$group` and `$tab` are in scope, holding the `Hubzero\User\Group` and the
active tab name.

Class autoloading does **not** reach into a group directory — the autoloader
maps `Components\Gauges\*` to `core/components/com_gauges` and
`app/components/com_gauges`, neither of which exists. The entry file has to
require what it needs:

```php
<?php
require_once JPATH_GROUPCOMPONENT . DS . 'controllers' . DS . 'readings.php';

$controller = new \Components\Gauges\Controllers\Readings();
$controller->execute();
```

Forget that `require_once` and you get *Class … not found* on a white page,
with the group's own template gone as well, because the fatal happens while
the group is still assembling its content. It is the first thing to check when
a component that worked in one place fails in another.

That constant is also what several parts of the framework key off:

| Behaviour | Where |
|---|---|
| `Assets::addComponentStylesheet()` and `addComponentScript()` resolve to the component's own `assets/css` and `assets/js` | [`Hubzero\Document\Assets`](../../../core/libraries/Hubzero/Document/Assets.php) |
| The `css()` and `js()` view helpers do the same | [`Hubzero\View\Helper\Css`](../../../core/libraries/Hubzero/View/Helper/Css.php) |
| A model extending `Hubzero\Base\Model` whose file sits under the component directory gets the group's database instead of the hub's | [`Hubzero\Base\Model::initDbo()`](../../../core/libraries/Hubzero/Base/Model.php) |

A controller extending
[`Hubzero\Component\SiteController`](../../../core/libraries/Hubzero/Component/SiteController.php)
needs no configuration: it takes its base path from its own file, two levels
up, which is the component directory. Views therefore resolve to
`views/<name>/tmpl`, helpers to `helpers/`, and the component's language file
is loaded from `language/en-GB/en-GB.com_<name>.ini` inside the component.
Move a controller one directory deeper and every one of those resolves one
level wrong, which shows as *Layout "display" not found*.

> **Note:** The group's own top-level `language/` directory is a different
> thing: it overrides the strings of the hub's *group plugins*, as described
> in [the section overview](README.md). A component's
> strings belong inside the component. A key that renders as itself —
> `COM_GAUGES_TITLE` on the page — means the file is in the wrong one of the
> two.

> **Warning:** `$this->database` on a `SiteController` is the **hub's**
> connection, not the group's. Call
> `\Hubzero\User\Group\Helper::getDbo()` when you mean the group's
> [database](05-databases.md). The same applies to any model extending
> `Hubzero\Database\Relational`; see
> [Models and the group connection](05-databases.md#models-and-the-group-connection).

The component's output is then wrapped by
[`site/views/pages/tmpl/_view_component.php`](../../../core/components/com_groups/site/views/pages/tmpl/_view_component.php):

```php
<div class="group-component">
	<?php echo $this->content; ?>
</div>
```

Put a `_view_component.php` in the group's `template/` directory to replace
that wrapper.

## Routing

URLs built inside a super group component come back prefixed with
`/groups/<alias>/<component>/` automatically. Do not add the prefix yourself.

Both halves are done by
[`core/plugins/system/supergroup`](../../../core/plugins/system/supergroup/supergroup.php):

- **Building.** A rule appended to the router turns
  `Route::url('index.php?option=com_gauges&…')` into
  `/groups/coastal/gauges/<segments>`, where the segments come from the
  component's `router.php`. It only fires while the current request is already
  inside `/groups/…` and the option names a directory the group actually has.
- **Parsing.** Before the component runs, the remainder of the path — what is
  left after `groups/<alias>/<component>` — is handed to the same router and
  every variable it returns is set on the request.

That plugin is enabled on a stock install. If every link in your component
comes back as `index.php?option=com_gauges&…`, check that it still is before
you look at your `router.php`.

`router.php` may provide either a class or a pair of functions:

| Form | Parse | Build |
|---|---|---|
| `Components\<Name>\Site\Router` | `parse($segments)` | `build($query)` |
| `Components\<Name>\Router` | `parse($segments)` | `build($query)` |
| Plain functions | `<Name>ParseRoute($segments)` | `<Name>BuildRoute($query)` |

A component with no `router.php` still works; it just has no path segments of
its own beyond the component name.

> **Note:** If a file defines both a router class and the plain functions, the
> two halves disagree about which wins: on parse the function's result
> replaces the class's, while on build the class is used and the function
> ignored. Define one or the other.

> **Warning:** The build rule reads the router of the component the request is
> *currently in*, and prefixes the result with that component's name — not
> with the option being built. Building a URL for a second group component
> from inside the first produces a path under the first. Two components in one
> group cannot link to each other through `Route::url()`; write those links by
> hand.

### Query strings

Query string parameters reaching a super group component are moved out of the
way and put back: the group router copies every parameter to an `sg_`-prefixed
name during parsing, and the system plugin copies them back to their plain
names immediately before the component is included. This keeps a component's
`?task=`, `?id=` and the like from being read as instructions to com_groups.
It happens automatically, and a component reads its parameters through
`Request` as usual.

## Creating one

`muse group scaffolding component` is documented in older material. On this
release it does not work for a group: scaffolding resolves its install
directory against `core/` rather than `app/`, so the files land in
`core/site/groups/<id>/components/`, and the template it writes is a full CMS
component with `admin/` and `site/` directories rather than the flat layout a
group needs. The `@FIXME` on the path is in the source. Both problems are
recorded in the review findings.

Build the directory by hand, or copy the `site/` directory of an existing
component up one level and adjust. It is a small amount of typing compared
with what the component itself will take.

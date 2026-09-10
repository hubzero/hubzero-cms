<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/extensions/parameters
source-id: 3468
imported: 2026-09-09
source-state: unpublished
-->
# Parameters

A parameter is a setting an administrator can change without editing code.
You declare them in XML; the CMS builds a form from the declaration, stores
what was entered as JSON in the extension's `#__extensions` row, and hands
them back to you as a `Registry`.

This page is about declaring them. [Config](../05-basics/03-config.md) covers
reading them at runtime — `Component::params()`, `$this->params`, and the
`Registry` methods. Every parameter the shipped extensions declare is listed
in the [configuration reference](../../reference/configuration/README.md).

## Where the declaration goes

| Kind | File | Root |
|---|---|---|
| Component | `config/config.xml` | `<config>` |
| Module | its XML manifest | `<config><fields name="params">` |
| Plugin | its XML manifest | `<config><fields name="params">` |
| Template | `templateDetails.xml` | `<config><fields name="params">` |

A component keeps its parameters in a separate file, and that file's root
element is `<config>` with `<fieldset>` children directly inside it:

<!--include: core/components/com_blog/config/config.xml:9-14-->

Everything else declares them inside its manifest, wrapped in a
`<fields name="params">`:

<!--include: core/plugins/system/debug/debug.xml:20-29-->

## Fieldsets

A `<fieldset>` becomes a tab or a titled group on the settings screen. The
names are conventional rather than enforced:

| Name | Where it appears |
|---|---|
| `basic` | The first tab. Most parameters belong here. |
| `advanced` | A second tab, for the layout and CSS class settings a module gets. |
| Anything else | Its own tab, labelled from the fieldset's `label` attribute or its name. |

`com_blog` uses `basic`, `archive`, `entry` and `feeds`; a component is free
to group its settings however reads best.

## Fields

```xml
<field
	name="introlength"
	type="text"
	default="300"
	label="COM_BLOG_CONFIG_INTROLENGTH_LABEL"
	description="COM_BLOG_CONFIG_INTROLENGTH_DESC"
/>
```

| Attribute | Meaning |
|---|---|
| `name` | The key. `$params->get('introlength')` reads it. Required. |
| `type` | Which field class renders it. Defaults to `text`. |
| `default` | The value used until an administrator saves something else. |
| `label` | A language key for the field's label. |
| `description` | A language key for its help text. |
| `required` | `true` to refuse an empty value. |
| `filter` | How the submitted value is cleaned — `safehtml`, `integer`, `raw`. |
| `class`, `size`, `cols`, `rows` | Passed to the rendered control. |

`label` and `description` are language keys, resolved from the extension's
own `.ini` file. Put them there rather than writing English into the
manifest. See [Languages](03-languages.md).

Fields that offer a fixed set of choices carry `<option>` children, whose
text is also a language key:

```xml
<field name="show_from" type="list" default="site"
	label="COM_BLOG_CONFIG_DATA_SRC_LABEL"
	description="COM_BLOG_CONFIG_DATA_SRC_DESC">
	<option value="site">COM_BLOG_CONFIG_DATA_SRC_SITE</option>
	<option value="member">COM_BLOG_CONFIG_DATA_SRC_MEMBER</option>
	<option value="group">COM_BLOG_CONFIG_DATA_SRC_GROUP</option>
</field>
```

## Field types

A `type` is resolved to a class in
[`Hubzero\Form\Fields`](../../../core/libraries/Hubzero/Form/Fields) by
`Hubzero\Form\Helper::loadFieldType()`: `type="calendar"` loads
`Fields\Calendar`. Two things are worth knowing about that lookup:

- `type="list"` is special-cased to `Fields\Select`, because `List` cannot be
  a PHP class name. Most of the tree writes `list`.
- **An unrecognised type silently becomes a text box.** There is no error and
  no log line, so a typo in `type` costs you the widget and nothing tells
  you. Check the name against the directory.

These are the types that exist:

| Type | Control |
|---|---|
| `text` | Single-line text box |
| `textarea` | Multi-line text box |
| `password` | Text box that obscures what is typed |
| `email`, `url`, `tel` | Text boxes with the matching HTML input type |
| `number`, `integer` | Numeric input; `integer` renders a range as a drop-down |
| `hidden` | Stored but not shown |
| `list` | Drop-down of the `<option>` children |
| `select` | The same class, under its own name |
| `groupedlist` | Drop-down with `<group>` headings |
| `combo` | Drop-down that also accepts a typed value |
| `radio` | Radio buttons |
| `checkbox`, `checkboxes` | One box, or a set of them |
| `spacer` | A visual separator; stores nothing |
| `calendar` | Text box with a date picker |
| `color` | Colour picker |
| `editor` | WYSIWYG editor |
| `media`, `file` | Media picker, file upload |
| `filelist`, `folderlist`, `imagelist` | Files, folders, or images from a named directory |
| `category` | Content categories |
| `tags` | Tag input |
| `user` | User picker |
| `usergroup` | User groups |
| `accesslevel` | Viewing access levels |
| `rules` | The permissions grid |
| `language`, `contentlanguage` | Installed languages, content languages |
| `country` | Country list |
| `timezone` | Time zones |
| `plugins` | Plugins in a given group |
| `templatestyle` | Installed template styles |
| `componentlayout`, `modulelayout` | Alternative layouts for a component or module |
| `menuitem` | Menu items |
| `sql` | Options from a query given in the field's `query` attribute |
| `cachehandler`, `sessionhandler`, `databaseconnection` | The configured handlers |

> **Note:** The old version of this page listed `editors`, `languages`,
> `timezones`, `menu` and `helpsites`. None of those exist. The singular
> forms — `editor`, `language`, `timezone` — are the real names, there is no
> menu-picker field, and `helpsites` went with the Joomla help system.

## Permissions

Permissions are declared in a second file, `config/access.xml`, which names
the actions the component recognises, grouped into sections:

<!--include: core/components/com_blog/config/access.xml:9-18-->

The grid an administrator edits is a field in `config.xml` like any other,
of `type="rules"`, pointing at a section of that file:

```xml
<field name="rules" type="rules" label="JCONFIG_PERMISSIONS_LABEL"
	class="inputbox" validate="rules" filter="rules"
	component="com_answers" section="component" />
```

See [Users](../05-basics/05-user.md) for how a controller then checks an action.

## Where the values end up

Saved parameters are JSON in the `params` column of the extension's
`#__extensions` row. A module instance's parameters are in `#__modules`
instead, because the same module can be published several times with
different settings.

Nothing writes those columns at install time. Until an administrator saves
the settings screen the column is empty, and every `$params->get()` returns
its second argument — **not** the `default` in the manifest. Always pass a
default in code:

```php
$limit = (int) $params->get('introlength', 300);
```

A migration can seed the column instead, with the `saveParams` macro, if a
sensible value matters before anyone visits the screen.

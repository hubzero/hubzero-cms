<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/templates/packaging
source-id: 3513
-->
# Packaging

A finished template has two manifests. `templateDetails.xml` tells the CMS what
the template offers — its parameters, its module positions and its metadata.
`composer.json` tells Composer how to fetch and place it. Neither of them
installs anything on its own; a [migration](01-migrations.md) does the actual
registering.

## What the tree should look like

```
app/templates/mytemplate/
    css/
    html/                      Output overrides
    img/
    js/
    language/
        en-GB/
            en-GB.tpl_mytemplate.ini
    migrations/
        Migration…TplMytemplate.php
    component.php
    error.php
    index.php
    composer.json
    templateDetails.xml
    template_thumbnail.png
    favicon.ico
```

[Structure](03-structure.md) says which of these are required and which are
conventions.

### The thumbnail

`template_thumbnail.png` is what the administrator's template list shows.
206 pixels wide; `kimera`'s is 206×150 and `kameleon`'s 206×118, so the height
is not fixed. PNG, and it must be that exact filename —
[`TemplatesHelper::thumb()`](../../../core/components/com_templates/helpers/utilities.php)
looks for nothing else.

Put an optional `template_preview.png` beside it and the thumbnail becomes a
link that opens the full-size image in a modal.

## templateDetails.xml

Contrary to what older documentation said, this file is not deprecated.
`com_templates`, `com_modules` and `com_installer` all read it, and a template
without one is configurable only by editing files.

A complete example:

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="template" version="2.5">
	<name>mytemplate</name>
	<creationDate>2026-09-09</creationDate>
	<author>Jane Doe</author>
	<authorEmail>jane@example.org</authorEmail>
	<authorUrl>example.org</authorUrl>
	<copyright>Copyright (c) 2026 Example University</copyright>
	<license>http://opensource.org/licenses/MIT MIT</license>
	<version>1.0</version>
	<description>Site template for Example Hub</description>
	<files>
		<filename>index.php</filename>
		<filename>component.php</filename>
		<filename>error.php</filename>
		<filename>css/index.css</filename>
		<filename>js/hub.js</filename>
		<filename>template_thumbnail.png</filename>
	</files>
	<languages>
		<language tag="en-GB">en-GB.tpl_mytemplate.ini</language>
	</languages>
	<positions>
		<position>notices</position>
		<position>search</position>
		<position>left</position>
		<position>right</position>
		<position>footer</position>
	</positions>
	<config>
		<fields name="params">
			<fieldset name="basic">
				<field name="header" type="list" default="light"
				       label="TPL_MYTEMPLATE_FIELD_HEADER_LABEL"
				       description="TPL_MYTEMPLATE_FIELD_HEADER_DESC">
					<option value="light">TPL_MYTEMPLATE_FIELD_HEADER_LIGHT</option>
					<option value="dark">TPL_MYTEMPLATE_FIELD_HEADER_DARK</option>
				</field>
			</fieldset>
		</fields>
	</config>
</extension>
```

> **Note:** The repository is MIT licensed, and so are all the shipped
> templates. Older copies of this page showed `<license>GNU/GPL</license>`;
> that is out of date. Use whatever licence your own template is actually
> under — the CMS does not read this element, but people do.

### The root element

`<extension type="template">` is the current form and what every shipped
template except `lucent` uses. `<install type="template">` is the legacy form
and is still accepted; `lucent` uses it. `<metafile>` is for languages only.
Anything else and the parser gives up and the template shows no metadata.

The `type` attribute must say `template`. `kameleon` also carries
`client="administrator"`, but nothing reads that attribute — the client is
whatever `addTemplateEntry()` wrote into `#__extensions`.

### What each element is for

| Element | Read by | Effect |
|---|---|---|
| `<name>`, `<version>`, `<description>`, `<author>`, `<authorEmail>`, `<authorUrl>`, `<copyright>`, `<creationDate>` | `com_templates`, `com_installer` | Shown in the template and extension lists. Missing `author` and `creationDate` display as *Unknown*. |
| `<positions>` | `com_modules` | Fills the position dropdown when someone places a module. |
| `<config>` | `com_templates` | Becomes the style-editing form. |
| `<languages>` | Nothing | The language file is found by [naming convention](02-languages.md), not by this list. |
| `<files>` | Nothing | Left over from the old XML installer. `kimera` lists 18 files out of the 140 it ships; `lucent` and `welcome` list none. Harmless either way. |
| `<license>` | Nothing | Informational. |

### Positions

```xml
<positions>
	<position>introblock</position>
	<position value="user3">Utility bar</position>
</positions>
```

`com_modules` reads the element text as the position name. Give a `value`
attribute and the text becomes the label instead. With neither, the
administrator sees a label looked up as
`TPL_{TEMPLATE}_POSITION_{POSITION}` — define that key in your
[language file](02-languages.md) and the list reads as English.

> **Warning:** Nothing checks the manifest against `index.php`. `kimera`
> declares `banner` and `introblock` that its layout never includes, and its
> layout includes `breadcrumbs` and `endpage` that the manifest never declares.
> A module placed in an undeclared position still renders; it is just not
> offered in the dropdown. Keep the two lists in step yourself.

### Parameters

The `<config>` block is an ordinary
[`Hubzero\Form`](../../../core/libraries/Hubzero/Form) fieldset definition, and
[Parameters](../07-extensions/02-parameters.md) covers the field types. Two
things are specific to templates:

- `label` and `description` are language keys, resolved from the template's own
  language file.
- `com_templates` looks for `config/config.xml` inside the template first and
  falls back to `templateDetails.xml`. Either works; nothing shipped uses the
  separate file.

Read the values back in a layout with `$this->params->get('header')`.

## composer.json

This is what makes the template installable as a package. `kimera`'s, whole:

<!--include: core/templates/kimera/composer.json-->

| Key | Notes |
|---|---|
| `name` | `{vendor}/tpl_{template}`. The vendor is yours, not `hubzero`. |
| `type` | Must be one of `hubzero-component`, `hubzero-module`, `hubzero-plugin`, `hubzero-template`. This is what tells the hub's installer where the package belongs. |
| `license` | An [SPDX identifier](https://spdx.org/licenses/). The shipped templates say `MIT`. |
| `extra.install-directory` | Where the files should land, relative to `app/`. |
| `require` | The shipped templates pin `"php": "^5.4"`, which is long obsolete; set something honest for your own. |

## Installing

There is no XML installer any more. Installation goes through Composer.
**Extensions → Extension Manager** in the administrator opens `com_installer`,
which has these screens:

| Screen | Controller | What it does |
|---|---|---|
| **Hubzero Core** | `manage` | Lists installed extensions; enable, disable, remove |
| **Core Migrations** | `migrations` | Runs pending `up()` methods |
| **Custom Extensions** | `customexts` | Installs an extension from a Git repository |
| **Warnings** | `warnings` | Environment checks |
| Packages | `packages` | Lists what the registered repositories offer, and installs one |
| Repositories | `repositories` | Registers a Composer repository |

Installing a packaged template is:

1. **Repositories** registers the source your package comes from. The screen
   edits the `repositories` block of the hub's `app/composer.json`.
2. **Packages** installs it.
   [`Hubzero\Utility\Composer`](../../../core/libraries/Hubzero/Utility/Composer.php)
   runs Composer with `app/` as its working directory, so the require lands in
   `app/composer.json` and the files under `app/`.
3. **Core Migrations** runs the template's `up()`, which writes the
   `#__extensions` and `#__template_styles` rows. Until this step the template
   exists on disk and is invisible to the CMS.
4. **Hubzero Core** lists the result and lets an administrator enable or
   disable it.

> **Warning:** Both Composer screens are unfinished. Nothing links to them —
> `Helpers\Installer::addSubmenu()` lists only Hubzero Core, Custom Extensions
> and Warnings — so you reach them by typing the URL
> `/administrator/index.php?option=com_installer&controller=packages`, after
> which tabs link to the other. Every language key their views use is
> undefined as well, so their labels render as raw
> `COM_INSTALLER_PACKAGES_…` keys. Copying the directory in by hand and running
> the migration is the reliable route today.

> **Note:** Both screens refuse to run and show a warning instead if the hub
> has no `app/composer.json`.

Copying the directory in by hand and then running the migration works just as
well, and is what you will do while developing. The
[deployment chapter](../07-extensions/04-deployext.md) covers packaging a
finished extension for other people.

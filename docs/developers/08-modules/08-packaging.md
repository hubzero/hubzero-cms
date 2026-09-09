<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/modules/packaging
-->
# Packaging

A module is distributed as its own directory, exactly as it will sit on disk
after installation. Two manifests describe it: `composer.json`, which the
package manager reads, and `mod_example.xml`, which the CMS reads for the name,
the client, and the parameter form shown in the Module Manager.

## Package layout

```
mod_example/
    assets/css/mod_example.css
    language/en-GB/en-GB.mod_example.ini
    language/en-GB/en-GB.mod_example.sys.ini
    migrations/Migration20260101000000ModExample.php
    tmpl/default.php
    tmpl/index.html
    composer.json
    helper.php
    index.html
    mod_example.php
    mod_example.xml
```

Nothing is rearranged on install. Whatever is in the package appears under
`app/modules/mod_example` or `core/modules/mod_example`.

## The Composer manifest

```json
{
	"name": "myorg/mod_example",
	"description": "Example module for the Hubzero CMS",
	"type": "hubzero-module",
	"keywords": ["hubzero"],
	"homepage": "https://example.org",
	"license": "MIT",
	"require": {
		"php": "^5.4"
	}
}
```

The `type` must be `hubzero-module`. The recognised types are
`hubzero-component`, `hubzero-module`, `hubzero-plugin`, and
`hubzero-template`; the installer uses the type to decide where the package
belongs. A module that depends on a component declares it, as `mod_mygroups`
does with `"hubzero/com_groups": "dev-master"` — the module reads
`Components\Groups\Models\Recent`, and installing it without the component
would break at render time.

Unlike a plugin, a module needs no `extra.install-directory`: the package name
already determines the directory.

## The XML manifest

The XML manifest is what the CMS itself reads. It is required, it is named
after the module, and it is not listed in its own `<files>` block.

<!--include: core/modules/mod_mygroups/mod_mygroups.xml:1-17-->

| Element or attribute | Meaning |
|---|---|
| `type="module"` | Required; identifies the extension type. |
| `client="site"` | `site` or `administrator`. Must agree with the `$client` argument in the registration migration. |
| `version` | The manifest format version, not the module's version. |
| `<name>` | The element name, `mod_example`. Used as the default title of new instances. |
| `<description>` | A language key, resolved from the `.sys.ini` file. |
| `<files>` | Every file to install. The `module` attribute on one `<filename>` marks the entry point. |
| `<languages>` | Translation files to copy into place. |
| `<config>` | The parameter form. |

## Parameters

`<config>` holds one `<fields name="params">` element containing one or more
`<fieldset>`s, which become the tabs of the Module Manager's edit form. Each
`<field>` is one parameter, readable afterwards as
`$this->params->get('name')`:

<!--include: core/modules/mod_mygroups/mod_mygroups.xml:18-41-->

`label` and `description` are language keys. `default` is what
`params->get()` returns for an instance saved before the field existed only if
you pass it yourself as the second argument — the manifest default is applied
by the form, not by the Registry.

Two field names are conventional across almost every module:

- `moduleclass_sfx` — a suffix the chrome function appends to the wrapper's
  CSS class, so an administrator can style one instance differently.
- `cache` and `cache_time` — read by `getCacheContent()`. See
  [Helpers](04-helpers.md).

The `member_dashboard="1"` attribute seen on some fields marks a parameter as
editable by a member when the module appears on their dashboard, rather than
only by an administrator.

## Installing

An administrator installs the package through **Extensions → Install** in the
administrative interface, or a developer copies the directory into
`app/modules` by hand. Either way the module is not usable until its
migration has run and registered it — see [Migrations](01-migrations.md) — and
not visible until an administrator creates an instance and assigns it to a
position.

> **Note:** Copying the files without running the migration leaves the module
> invisible: `Loader::all()` joins `#__modules` to `#__extensions` and requires
> an enabled row there.

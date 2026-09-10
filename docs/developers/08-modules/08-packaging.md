<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/modules/packaging
-->
# Packaging

A module is distributed as its own directory, exactly as it will sit on disk
after installation. Two manifests describe it: `composer.json`, which the
package manager reads, and `mod_upcoming_bookings.xml`, which the CMS reads
for the name, the client, and the parameter form shown in the Module Manager.

The XML is the one that changes what an administrator sees. Every field you
want on the module's edit screen has to be declared there; a parameter your
code reads but the manifest does not declare has no way of being set.

## Package layout

```
mod_upcoming_bookings/
    assets/css/mod_upcoming_bookings.css
    language/en-GB/en-GB.mod_upcoming_bookings.ini
    language/en-GB/en-GB.mod_upcoming_bookings.sys.ini
    migrations/Migration20260101000000ModUpcomingBookings.php
    tmpl/default.php
    tmpl/index.html
    composer.json
    helper.php
    index.html
    mod_upcoming_bookings.php
    mod_upcoming_bookings.xml
```

Nothing is rearranged on install. Whatever is in the package appears under
`app/modules/mod_upcoming_bookings`.

## The Composer manifest

```json
{
	"name": "myorg/mod_upcoming_bookings",
	"description": "A user's next instrument reservations",
	"type": "hubzero-module",
	"keywords": ["hubzero"],
	"homepage": "https://example.org",
	"license": "MIT",
	"require": {
		"php": "^5.4",
		"myorg/com_bookings": "dev-master"
	}
}
```

The `type` must be `hubzero-module`. The recognised types are
`hubzero-component`, `hubzero-module`, `hubzero-plugin`, and
`hubzero-template`; the installer uses the type to decide where the package
belongs.

Declare the component you read from, as above. `mod_mygroups` does the same
with `"hubzero/com_groups": "dev-master"`, because it reads
`Components\Groups\Models\Recent`. This is documentation with teeth in one
direction only — nothing at render time checks it, so a module installed
without its component fails when someone loads a page, with a "class not
found" fatal naming a component nobody realised was missing.

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
| `<name>` | The element name, `mod_upcoming_bookings`. Used as the default title of new instances. |
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

`mod_upcoming_bookings` needs two fields of its own and the conventional
suffix:

```xml
<config>
	<fields name="params">
		<fieldset name="basic">
			<field name="moduleclass_sfx" type="text" default=""
				label="MOD_UPCOMING_BOOKINGS_PARAM_CLASS_LABEL"
				description="MOD_UPCOMING_BOOKINGS_PARAM_CLASS_DESC" />
			<field name="limit" type="text" default="5"
				label="MOD_UPCOMING_BOOKINGS_PARAM_LIMIT_LABEL"
				description="MOD_UPCOMING_BOOKINGS_PARAM_LIMIT_DESC" />
			<field name="instrument_id" type="text" default="0"
				label="MOD_UPCOMING_BOOKINGS_PARAM_INSTRUMENT_LABEL"
				description="MOD_UPCOMING_BOOKINGS_PARAM_INSTRUMENT_DESC" />
		</fieldset>
	</fields>
</config>
```

`limit` and `instrument_id` are what let one module do two jobs. An instance
in the sidebar with `instrument_id` at `0` lists everything the user has
booked; a second instance on a lab page narrows the same code to one
instrument. Parameters are the cheapest way to avoid writing a second module.

`label` and `description` are language keys. `default` is what
`params->get()` returns for an instance saved before the field existed only if
you pass it yourself as the second argument — the manifest default is applied
by the form, not by the Registry. So `default="5"` here and
`$this->params->get('limit', 5)` in the class: write both, or an older
instance reads null. See [Helpers](04-helpers.md#parameters).

Two field names are conventional across almost every module:

- `moduleclass_sfx` — a suffix the chrome function appends to the wrapper's
  CSS class, so an administrator can style one instance differently.
- `cache` and `cache_time` — read by `getCacheContent()`. Declare these only
  if your module's output is the same for every visitor. The cache key is the
  instance id alone, so on a per-user module such as `mod_upcoming_bookings`
  they hand one researcher's reservations to the next visitor. See
  [Helpers](04-helpers.md#caching).

The `member_dashboard="1"` attribute seen on some fields marks a parameter as
editable by a member when the module appears on their dashboard, rather than
only by an administrator.

## Installing

There is no package installer. The **Install**, **Update**, **Discover** and
**Database** tabs the older documentation names do not exist in this release;
only orphan language strings remain. See the
[extension manager](../../managers/10-extensions/04-extension-manager.md) for
what the screen actually offers.

A module therefore arrives through the git-backed **Custom Extensions** flow,
or by copying the directory into `app/modules` by hand. Either way the module
is not usable until its
migration has run and registered it — see [Migrations](01-migrations.md) — and
not visible until an administrator creates an instance and assigns it to a
position.

> **Note:** Copying the files without running the migration leaves the module
> invisible: `Loader::all()` joins `#__modules` to `#__extensions` and requires
> an enabled row there.

Say all three in your README, because none of them announces itself:

1. run the migration;
2. create an instance in the Module Manager;
3. assign it to a position the site's template actually renders — see
   [Loading](09-loading.md#positions-belong-to-the-template).

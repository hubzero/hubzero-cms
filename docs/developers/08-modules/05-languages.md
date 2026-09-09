<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/modules/languages
-->
# Languages

Every string a module puts on the page should come from a translation file, so
that a hub can change the wording without editing the module and so that other
languages can be added later.

## Where the files go

```
core/modules/mod_mygroups/
    language/
        en-GB/
            en-GB.mod_mygroups.ini
            en-GB.mod_mygroups.sys.ini
```

The directory is the language tag, and the file name is the tag, a dot, and
the module's full element name — prefix included. The `.sys.ini` file holds
the handful of strings the CMS itself needs before the module runs: the name
and description shown in the Module Manager and the installer. Everything the
module renders goes in the plain `.ini`.

## When they are loaded

You do not load them. [`Hubzero\Module\Loader::render()`](../../../core/libraries/Hubzero/Module/Loader.php)
loads the module's language file immediately before including the entry file,
trying two locations and stopping at the first that produces strings:

1. `app/bootstrap/{client}/language/{tag}/{tag}.mod_example.ini`
2. `{module directory}/language/{tag}/{tag}.mod_example.ini`

The first path is the hub's own override directory, where `{client}` is `site`
or `administrator`. A hub that wants to reword one string in a shipped module
puts a file there containing only that key; because the override is tried
first and loading stops on success, the file must then contain **every** key
the module uses, not just the changed one.

The translator also loads the default language before the requested one unless
`debug_lang` is set, so a key missing from a partial translation falls back to
`en-GB` rather than rendering raw.

## Writing the file

```ini
; @package    hubzero-cms
; @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
; @license    http://opensource.org/licenses/MIT MIT

; Note : All ini files need to be saved as UTF-8 - No BOM

MOD_MYGROUPS_RECENT="Recent"
MOD_MYGROUPS_ALL="All"
MOD_MYGROUPS_NO_RECENT_GROUPS="No recently visited groups found."
MOD_MYGROUPS_PARAM_LIMIT_LABEL="# to display"
MOD_MYGROUPS_PARAM_LIMIT_DESC="The number of items to display."
```

Rules the parser enforces or the convention expects:

- Keys are uppercase, words separated by underscores, and start with
  `MOD_{MODULENAME}_` so they cannot collide with another extension's.
- Values are double-quoted. Files are parsed with `parse_ini_file()` in raw
  mode, so a literal double quote inside a value is escaped as `\"`.
- Comments start with a semicolon.
- Keys used in the manifest — the `label` and `description` attributes on
  `<field>`, and the `<description>` element — are looked up in the same file,
  so parameter labels are translatable too.

## Using a string

`Lang::txt()` takes a key and returns the translated string, passing any extra
arguments through `sprintf`:

```php
<p><?php echo Lang::txt('MOD_MYGROUPS_NO_RECENT_GROUPS'); ?></p>
<p><?php echo Lang::txt('MOD_EXAMPLE_COUNT', count($this->rows)); ?></p>
```

with the placeholder in the value:

```ini
MOD_EXAMPLE_COUNT="Showing %s items"
```

A key with no translation is returned unchanged, which is how a missing string
shows up on the page as `MOD_EXAMPLE_COUNT` in capitals rather than as an
error.

> **Note:** In a layout, `Lang` needs no import — layouts run in the global
> namespace. In `helper.php`, which declares `namespace Modules\Example`, you
> must write `use Lang;` at the top of the file or the call fatals. See
> [Facades](../03-foundation/04-facades.md).

## Registering the file

List the file in the manifest so the installer copies it:

```xml
<languages>
    <language tag="en-GB">en-GB.mod_mygroups.ini</language>
</languages>
```

See [Languages](../07-extensions/03-languages.md) for how the language tag is
chosen for a request and how additional languages are installed.

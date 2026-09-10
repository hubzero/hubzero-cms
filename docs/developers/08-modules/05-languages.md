<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/modules/languages
-->
# Languages

Every string a module puts on the page should come from a translation file, so
that a hub can change the wording without editing the module and so that other
languages can be added later.

That second reason matters more for a module than for a component. A module
sits in the furniture of every page, so its wording is exactly what a hub
wants to adjust — "Upcoming bookings" here, "Your instrument time" there. If
the string is in the layout, the hub has to fork the module to change it.

## Where the files go

```
app/modules/mod_upcoming_bookings/
    language/
        en-GB/
            en-GB.mod_upcoming_bookings.ini
            en-GB.mod_upcoming_bookings.sys.ini
```

The directory is the language tag, and the file name is the tag, a dot, and
the module's full element name — prefix included. The `.sys.ini` file holds
the handful of strings the CMS itself needs before the module runs: the name
and description shown in the Module Manager and the installer. Everything the
module renders goes in the plain `.ini`.

The `.sys.ini` needs exactly two keys, and the first of them is not obvious:

```ini
MOD_UPCOMING_BOOKINGS="Upcoming bookings"
MOD_UPCOMING_BOOKINGS_XML_DESCRIPTION="Lists the current user's next instrument reservations."
```

The Module Manager's **Select a Module Type** screen runs the extension's
*name* — which `addModuleEntry()` set to the element, `mod_upcoming_bookings`
— through `Lang::txt()`, and the manifest's `<description>` through it as
well. So a key spelled like the element name is what gives the module a
readable title. Omit the file and the screen offers a module called
`mod_upcoming_bookings` described as
`MOD_UPCOMING_BOOKINGS_XML_DESCRIPTION`. Nothing errors; it just looks
unfinished.

## When they are loaded

You do not load them. [`Hubzero\Module\Loader::render()`](../../../core/libraries/Hubzero/Module/Loader.php)
loads the module's language file immediately before including the entry file,
trying two locations and stopping at the first that produces strings:

1. `app/bootstrap/{client}/language/{tag}/{tag}.mod_upcoming_bookings.ini`
2. `{module directory}/language/{tag}/{tag}.mod_upcoming_bookings.ini`

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

MOD_UPCOMING_BOOKINGS_HEADING="Upcoming bookings"
MOD_UPCOMING_BOOKINGS_NONE="You have no instrument time booked."
MOD_UPCOMING_BOOKINGS_SLOT="%s until %s"
MOD_UPCOMING_BOOKINGS_PARAM_LIMIT_LABEL="# to display"
MOD_UPCOMING_BOOKINGS_PARAM_LIMIT_DESC="The number of reservations to display."
MOD_UPCOMING_BOOKINGS_PARAM_INSTRUMENT_LABEL="Instrument"
MOD_UPCOMING_BOOKINGS_PARAM_INSTRUMENT_DESC="Limit to one instrument, or 0 for all."
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
<p><?php echo Lang::txt('MOD_UPCOMING_BOOKINGS_NONE'); ?></p>
<p><?php echo Lang::txt('MOD_UPCOMING_BOOKINGS_SLOT', $starts, $ends); ?></p>
```

with the placeholders in the value:

```ini
MOD_UPCOMING_BOOKINGS_SLOT="%s until %s"
```

A key with no translation is returned unchanged, which is how a missing string
shows up on the page as `MOD_UPCOMING_BOOKINGS_SLOT` in capitals rather than
as an error. That is the failure to watch for: capitals in the sidebar mean a
typo in the key, a stale cached copy of the module's output, or a file
whose name does not match the module element.

> **Note:** In a layout, `Lang` needs no import — layouts run in the global
> namespace. In `helper.php`, which declares
> `namespace Modules\UpcomingBookings`, you must write `use Lang;` at the top
> of the file or the call fatals. See
> [Facades](../03-foundation/06-facades.md).

## Registering the file

List the file in the manifest so the installer copies it:

```xml
<languages>
    <language tag="en-GB">en-GB.mod_upcoming_bookings.ini</language>
</languages>
```

See [Languages](../07-extensions/03-languages.md) for how the language tag is
chosen for a request and how additional languages are installed.

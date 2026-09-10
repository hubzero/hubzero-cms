<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/basics/languages
-->
# Languages

Every string a member sees comes out of an INI file through `Lang::txt()`.
Doing it that way is not only about translation: it is what lets an
administrator reword a page without editing a view, and what lets a hub keep
its wording through an upgrade.

The `Lang` facade resolves
[`Hubzero\Language\Translator`](../../../core/libraries/Hubzero/Language/Translator.php).

## The failure to know about first

**An undefined key is returned unchanged.** Nothing is logged, nothing
throws, and the page renders the key:

```php
// COM_BOOKINGS_HOLD_RELEASED is defined nowhere
echo Lang::txt('COM_BOOKINGS_HOLD_RELEASED');
```

```
COM_BOOKINGS_HOLD_RELEASED
```

That is the whole failure. It looks like a shouting constant in the middle
of a sentence, and it survives review because the path that renders it is
usually an error branch, a confirmation email, or a screen only an
administrator sees.

It is not rare. `php tools/lint/undefined-language-keys.php` currently
reports **444 keys** the tree asks for that no `en-GB` file anywhere
defines, and the
[PHP lint workflow](../../../.github/workflows/php-lint.yml) runs it against
that number as a ceiling rather than against zero:

```yaml
run: php tools/lint/undefined-language-keys.php --quiet --max=444
```

So the build fails when your change makes it 445. Three rules keep you off
that list.

### 1. Define the key

Add it to the extension's INI file in the same change that adds the
`Lang::txt()` call. Nothing else in the toolchain will remind you: the file
parses, PHP is happy, and the view renders.

```ini
COM_BOOKINGS_HOLD_RELEASED = "Your hold on %s expired and the slot is free again."
```

### 2. Put it in the file the code actually loads

This is the one the linter cannot catch. A key counts as defined if *any*
`en-GB` file under `core/` or `app/` defines it, but only a handful of files
load on any given request. A key defined in

```
core/components/com_bookings/admin/language/en-GB/en-GB.com_bookings.ini
```

and used from a site view passes the linter and still renders raw in the
browser, because the site request never loads the admin file. The same trap
catches a string defined in a component's file and used from a plugin, or
a `.sys.ini` string used outside the extension manager.

Match the file to the code that reads it:

| Reading code | File |
|---|---|
| Site controller or view | `<component>/site/language/en-GB/en-GB.com_x.ini` |
| Admin controller or view | `<component>/admin/language/en-GB/en-GB.com_x.ini` |
| Plugin | `<plugin>/language/en-GB/en-GB.plg_<group>_<name>.ini` |
| Module | `<module>/language/en-GB/en-GB.mod_x.ini` |
| Used by every client | `core/bootstrap/Site/language/en-GB/en-GB.ini` and its siblings |

Needing the same string on both faces of a component means defining it
twice, once in each file. That is the intended answer, not a shared file.

### 3. Match the placeholder count

Extra arguments go to `sprintf()`. Under PHP 8 a format string with more
placeholders than arguments raises `ArgumentCountError`, which nothing here
catches:

```ini
COM_BOOKINGS_CONFIRMED = "%s is booked for %s"
```

```php
// Fatal: ArgumentCountError: 3 arguments are required, 2 given
echo Lang::txt('COM_BOOKINGS_CONFIRMED', $instrument->get('name'));
```

A blank page from a translation call is nearly always this. Note that it
takes a **defined** key to produce it — while the key is missing the string
has no placeholders at all, so the same line renders the key quietly and
starts fataling only once somebody defines it. Count the `%s` in the INI
file against the arguments at every call site when you edit a string.

> **Warning:** A single boolean argument is not a `sprintf()` argument.
> `txt($string, $jsSafe)` and `txt($string, $jsSafe, $interpretBackSlashes)`
> are separate signatures, so `Lang::txt('COM_BOOKINGS_STATE', $isOpen)`
> with a boolean `$isOpen` sets escaping options and substitutes nothing.
> Cast the value, or pass a string.

## Where the files live

One file per extension, per language, named for the language tag and the
extension:

```
core/components/com_blog/site/language/en-GB/en-GB.com_blog.ini
core/components/com_blog/admin/language/en-GB/en-GB.com_blog.ini
core/plugins/cron/support/language/en-GB/en-GB.plg_cron_support.ini
core/modules/mod_login/language/en-GB/en-GB.mod_login.ini
core/bootstrap/Site/language/en-GB/en-GB.ini
```

The last is the shared set — `JYES`, `JACTION_DELETE`, `DATE_FORMAT_HZ1`
and the rest — loaded for every request. The others are loaded on demand,
and you do not have to ask:

- A controller extending `Hubzero\Component\SiteController` or
  `AdminController` loads its component's file in its constructor, unless
  something already did.
- A module's file is loaded by `Hubzero\Module\Loader` before the module
  renders.
- A plugin's file is **not** loaded automatically unless the plugin sets
  `protected $_autoloadLanguage = true;`. Otherwise call
  `$this->loadLanguage()` — cron plugins do this first thing in
  `onCronEvents()`, because their labels have to be translated before the
  administrator ever sees them.

`Lang::load($extension, $basePath, $lang = null, $reload = false, $default =
true)` is the underlying call if you need a file the framework will not
fetch for you.

[Languages](../07-extensions/03-languages.md) in the extensions section
covers what an extension has to *ship* — the manifest entries, the `.sys.ini`
file, the directory layout.

## Writing the file

Key/value pairs, values always double-quoted:

```ini
; @package  hubzero-cms
; Note : All ini files need to be saved as UTF-8 - No BOM

COM_BOOKINGS_INSTRUMENTS = "Instruments"
COM_BOOKINGS_BOOKING_SAVED = "Booking saved"
COM_BOOKINGS_BOOKED_BY = "Booked by %s on %s"
```

The parser is PHP's, so its rules apply. `NULL`, `yes`, `no`, `TRUE` and
`FALSE` are reserved and must not be used as keys. The characters
`{}|&~![()"` have meaning inside an unquoted value; quoting every value
avoids the question. Comments start with `;`.

## Naming keys

Prefix by extension type and extension name:

| Extension | Prefix | Example |
|---|---|---|
| Component | `COM_` | `COM_BOOKINGS_BOOKING_SAVED` |
| Module | `MOD_` | `MOD_LOGIN_REMEMBER_ME` |
| Plugin | `PLG_` | `PLG_CRON_SUPPORT_CLOSE_PENDING` |
| Template | `TPL_` | `TPL_SYSTEM_LOGOUT` |

The prefix is not cosmetic. All loaded strings share one flat array, and a
file loaded later overwrites a key already in it. A component whose view
renders a module — and the module's file loads at that point — will find its
own unprefixed `MYLINE` replaced by the module's. Prefixed keys cannot
collide.

Name the key for what the string *is*, not for what it says.
`COM_BOOKINGS_SAVE_FAILED` survives a rewording; `COM_BOOKINGS_SORRY` does
not.

## Translating

```php
use Lang;

echo Lang::txt('COM_BOOKINGS_INSTRUMENTS');
```

Extra arguments are passed to `sprintf()`, after the string has been looked
up:

```php
// COM_BOOKINGS_BOOKED_BY = "Booked by %s on %s"
echo Lang::txt('COM_BOOKINGS_BOOKED_BY', $member->get('name'), $date);
```

Numbered placeholders in the form `[[%1:name]]` are rewritten to `%1$s`
before the `sprintf()`, so a translator can reorder arguments that English
happens to put in a particular order.

| Method | What it does |
|---|---|
| `txt($string, ...)` | Translate, then `sprintf()` any extra arguments |
| `txts($string, $n, ...)` | Plural form: tries `KEY_<n>` and the language's plural suffixes, falls back to `KEY` |
| `alt($string, $alt, ...)` | Uses `KEY_ALT` if that key exists, else `KEY` |
| `hasKey($string)` | Whether a key is loaded |
| `script($string)` | Queue a string for the JavaScript translation object |
| `getTag()` / `getName()` / `isRTL()` | About the current language |
| `transliterate($string)` | ASCII-fold a string, using the language's own rules |

`hasKey()` is the guard for a key assembled at runtime, which is the one
case the linter cannot check for you:

```php
$key = 'COM_BOOKINGS_STATUS_' . strtoupper($booking->get('status'));

echo Lang::hasKey($key) ? Lang::txt($key) : $booking->get('status');
```

Prefer a fixed `switch` over a constructed key wherever you can. A key built
from a database value is a key nothing can audit, and it becomes undefined
the day somebody adds a status.

`txts()` is the one to reach for wherever a count appears:

```ini
COM_BOOKINGS_SLOTS_1 = "1 slot free"
COM_BOOKINGS_SLOTS = "%s slots free"
```

```php
echo Lang::txts('COM_BOOKINGS_SLOTS', $total);
```

## Overrides

A hub can replace any string without touching the extension. Overrides are
per client, and live in:

```
app/bootstrap/site/language/overrides/en-GB.override.ini
app/bootstrap/administrator/language/overrides/en-GB.override.ini
app/bootstrap/cli/language/overrides/en-GB.override.ini
app/bootstrap/api/language/overrides/en-GB.override.ini
```

The translator reads the override file in its constructor and re-applies it
after **every** extension file it loads. An override therefore always wins,
whatever loads afterwards.

To change a string, find its key and add a line:

```bash
grep -rn "Just one more thing" core/components/
# core/components/com_projects/site/language/en-GB/en-GB.com_projects.ini:193:COM_PROJECTS_SETUP_BEFORE_COMPLETE="Just one more thing..."
```

```ini
; app/bootstrap/site/language/overrides/en-GB.override.ini
COM_PROJECTS_SETUP_BEFORE_COMPLETE = "One more thing before you start."
```

The administrator can do the same from **Extensions → Language Manager →
Overrides**, which writes the same file. Its **Search Text You Want to
Change** box resolves a piece of wording back to the key that produced it.

> **Note:** Language overrides are the right answer to "we want this worded
> differently". A view override for a wording change duplicates a file that
> will then drift from the original at the next upgrade.

## Checking your work

```bash
php tools/lint/undefined-language-keys.php core/components/com_bookings
```

Run it against your own extension before you push, and against nothing at
all — the whole of `core/` — to see where the ceiling stands. A clean run is
necessary and not sufficient: it reads literal keys with PHP's tokenizer, so
keys assembled at runtime are skipped, and it does not know which file a
given request loads.

For a file that will not parse, `Lang::debugFile($path)` returns a count of
parse errors in one file. Turning on `debug_lang` in the global
configuration marks untranslated strings on the rendered page; `getOrphans()`
and `getUsed()` back that report, and the **System - Debug** plugin renders
it. See [debugging](08-debugging.md).

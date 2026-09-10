<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/extensions/languages
source-id: 3469
imported: 2026-09-09
source-state: unpublished
-->
# Languages

Every string an extension shows comes out of an INI file, looked up by key.
This page covers what an extension has to ship: which files, where, named
what, with keys named how. [Languages](../05-basics/04-languages.md) covers the
`Lang` API — `txt()`, `txts()`, plurals, overrides — and is the page to read
before writing view code.

Two reasons to bother, even on a hub that will only ever run in English. A hub
can reword any string your extension shows without editing your code, so an
administrator who dislikes your wording does not end up with a local patch
that the next update destroys. And a sentence in a file is a sentence someone
can find; a sentence in a layout is not.

## The files

One directory per language tag, one file per extension inside it. The tag
appears twice: as the directory name and as the start of the filename.

```
core/components/com_blog/site/language/en-GB/en-GB.com_blog.ini
core/components/com_blog/admin/language/en-GB/en-GB.com_blog.ini
core/components/com_blog/admin/language/en-GB/en-GB.com_blog.sys.ini
core/components/com_blog/api/language/en-GB/en-GB.com_blog.ini
core/modules/mod_login/language/en-GB/en-GB.mod_login.ini
core/plugins/system/debug/language/en-GB/en-GB.plg_system_debug.ini
core/templates/kimera/language/en-GB/en-GB.tpl_kimera.ini
```

A component has one file per face, and **only the face that is running is
loaded**. The site loads `site/language/{tag}/{tag}.com_bookings.ini`; the
administrator loads `admin/language/{tag}/{tag}.com_bookings.ini` plus the
`.sys.ini` beside it. `Component::render()` builds the path from
`PATH_COMPONENT`, which is the component directory plus the current client,
so the other file is never read. A module, plugin or template has one file,
because it has one face.

> **Warning:** A string both faces use has to be defined in **both** files.
> Nothing merges them and nothing warns. Define
> `COM_BOOKINGS_RESERVATION_SAVED` in `site/language/` only, and the
> administrator screen that saves a reservation displays the literal text
> `COM_BOOKINGS_RESERVATION_SAVED` — while `grep -r` across the tree finds the
> key defined and looks entirely correct. When a key renders as itself, the
> first question is not *is it defined* but *is it defined in the file this
> client loads*.

The same rule decides where a parameter's `label` goes: an extension's
settings screen runs in the administrator client, so the labels in a
component's `config/config.xml` are resolved from its **administrator** file
even though the parameter may only be read by site code.

The tag is a language and a region joined by a **hyphen**, following
[BCP 47](https://www.rfc-editor.org/info/bcp47): `en-GB`, `en-US`, `fr-FR`,
`pt-BR`. Not an underscore. `Hubzero\Language\Translator` builds the path
from the tag literally, so `en_US` produces a file nothing will ever look
for.

> **Note:** Hubzero ships `en-GB` and nothing else. The spelling in the
> strings is US English despite the tag; that is inherited and not worth
> fighting.

### The `.sys.ini` file

The administrator interface has to name an extension in a list before that
extension has run — in the Extension Manager, the Plugin Manager, the module
type picker. The strings for that live in a second file with `.sys` before
the extension:

```
en-GB.com_blog.sys.ini
en-GB.mod_login.sys.ini
en-GB.plg_system_debug.sys.ini
```

Put the extension's `<name>` and `<description>` keys — the ones its XML
manifest refers to — in there, and everything else in the ordinary file.
Ship a `.sys.ini` for any extension an administrator will see listed. Leave it
out and your extension appears in those lists as `COM_BOOKINGS`, which is
correct behaviour and looks like a bug.

For a component the `.sys.ini` belongs in the **administrator** language
directory, because that is the client doing the listing.

### Declaring them in the manifest

The manifest lists the files, per face:

```xml
<languages folder="site">
	<language tag="en-GB">en-GB.com_blog.ini</language>
</languages>
<administration>
	<languages folder="admin">
		<language tag="en-GB">en-GB.com_blog.ini</language>
		<language tag="en-GB">en-GB.com_blog.sys.ini</language>
	</languages>
</administration>
```

Like the `<files>` block, this list is inherited from a package installer that
no longer exists. Files are found by path, not by this declaration, so an
omission here breaks nothing and a listing here fixes nothing.

## Writing the file

Key/value pairs. Always quote the value:

```ini
; @package  hubzero-cms
; Note : All ini files need to be saved as UTF-8 - No BOM

COM_EXAMPLE_ENTRIES = "Entries"
COM_EXAMPLE_ENTRY_SAVED = "Entry saved"
COM_EXAMPLE_POSTED_BY = "Posted by %s on %s"
```

It is parsed by PHP, so PHP's rules apply:

- `NULL`, `yes`, `no`, `TRUE` and `FALSE` are reserved and cannot be keys.
- `{}|&~![()"` have meaning in an unquoted value. Quoting every value makes
  the question moot.
- Comments start with `;`.
- UTF-8, no byte order mark.

## Naming keys

Uppercase, words separated by underscores, prefixed with the extension type
and the extension name:

| Extension | Prefix | Example |
|---|---|---|
| Component | `COM_` | `COM_BLOG_ENTRY_SAVED` |
| Module | `MOD_` | `MOD_LOGIN_REMEMBER_ME` |
| Plugin | `PLG_` | `PLG_CRON_SUPPORT_CLOSE_PENDING` |
| Template | `TPL_` | `TPL_KIMERA_FIELD_HEADER_LABEL` |

The prefix is not a tidiness rule; it is the only thing preventing a whole
class of bug. Every loaded string goes into **one flat array**, and a file
loaded later overwrites keys already in it. So:

```ini
; mymodule
MYLINE = "Your Line"
```

```ini
; mycomponent
MYLINE = "My Line"
```

```php
// A component view that renders a module
echo Module::byPosition('mymodule');   // loads the module's file here
echo Lang::txt('MYLINE');              // "Your Line"
```

The component's own string is gone, replaced by the module's, and the fault
appears only on pages where that module happens to be published. Prefixed
keys — `MOD_MYMODULE_MYLINE` and `COM_MYCOMPONENT_MYLINE` — cannot collide,
whatever loads in what order.

Beyond the prefix, name for what the string *is*, not what it says. A key
called `COM_EXAMPLE_SAVE_FAILED` survives a rewording; `COM_EXAMPLE_SORRY`
does not.

## When the file is loaded

You rarely load one yourself:

- A controller extending `Hubzero\Component\SiteController` or
  `AdminController` loads its component's file in its constructor;
  `Hubzero\Component\Loader` has already loaded it by then in the usual
  case.
- `Hubzero\Module\Loader` loads a module's file before the module renders.
- A **plugin's file is not loaded automatically.** Set
  `protected $_autoloadLanguage = true;` on the plugin class, or call
  `$this->loadLanguage()` before you translate anything. Forget it and every
  string the plugin shows renders as its key — the same symptom as a missing
  definition, from an entirely different cause.

## Checking your work

A key with no definition is not an error: `Lang::txt()` returns the key
unchanged, so the page renders `COM_EXAMPLE_SAVE_FAILED` in place of a
sentence. That fails quietly, and it fails hardest on the paths nobody
exercises.

```bash
php tools/lint/undefined-language-keys.php core/components/com_example
```

That reports every literal key the code asks for that no `en-GB` file
anywhere defines. It cannot check keys assembled at runtime
(`'COM_EXAMPLE_' . strtoupper($type)`), so a clean run is necessary and not
sufficient.

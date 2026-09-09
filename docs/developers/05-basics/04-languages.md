<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/basics/languages
-->
# Languages

Every string a member sees comes out of an INI file through `Lang::txt()`.
Doing it that way is not only about translation: it is what lets an
administrator reword a page without editing a view, and what lets a hub keep
its wording through an upgrade.

The `Lang` facade resolves
[`Hubzero\Language\Translator`](../../../core/libraries/Hubzero/Language/Translator.php).

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

## Writing the file

Key/value pairs, values always double-quoted:

```ini
; @package  hubzero-cms
; Note : All ini files need to be saved as UTF-8 - No BOM

COM_BLOG_ENTRIES = "Entries"
COM_BLOG_ENTRY_SAVED = "Entry saved"
COM_BLOG_POSTED_BY = "Posted by %s on %s"
```

The parser is PHP's, so its rules apply. `NULL`, `yes`, `no`, `TRUE` and
`FALSE` are reserved and must not be used as keys. The characters
`{}|&~![()"` have meaning inside an unquoted value; quoting every value
avoids the question. Comments start with `;`.

## Naming keys

Prefix by extension type and extension name:

| Extension | Prefix | Example |
|---|---|---|
| Component | `COM_` | `COM_BLOG_ENTRY_SAVED` |
| Module | `MOD_` | `MOD_LOGIN_REMEMBER_ME` |
| Plugin | `PLG_` | `PLG_CRON_SUPPORT_CLOSE_PENDING` |
| Template | `TPL_` | `TPL_SYSTEM_LOGOUT` |

The prefix is not cosmetic. All loaded strings share one flat array, and a
file loaded later overwrites a key already in it. A component whose view
renders a module — and the module's file loads at that point — will find its
own unprefixed `MYLINE` replaced by the module's. Prefixed keys cannot
collide.

## Translating

```php
use Lang;

echo Lang::txt('COM_BLOG_ENTRIES');
```

A key with no entry in any loaded file is returned unchanged, so a typo
shows up as a shouting constant on the page rather than an empty space.

Extra arguments are passed to `sprintf()`, after the string has been looked
up:

```php
// COM_BLOG_POSTED_BY = "Posted by %s on %s"
echo Lang::txt('COM_BLOG_POSTED_BY', $author, $date);
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

`txts()` is the one to reach for wherever a count appears:

```ini
COM_BLOG_COMMENTS_1 = "1 comment"
COM_BLOG_COMMENTS = "%s comments"
```

```php
echo Lang::txts('COM_BLOG_COMMENTS', $total);
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

Two things go wrong: a key used in a view but never defined, and a file that
does not parse. `php tools/lint/undefined-language-keys.php` catches the
first across the tree. For the second, `Lang::debugFile($path)` returns a
count of parse errors in one file, and turning on `debug_lang` in the global
configuration marks untranslated strings on the rendered page —
`getOrphans()` and `getUsed()` back the report.

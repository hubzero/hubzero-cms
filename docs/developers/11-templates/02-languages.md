<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/templates/languages
source-id: 3505
modified: 2019-02-26
-->
# Languages

Every string a template prints should come from a language file, so the
template can be translated without being edited. A template's strings live in
its own `language` directory and are loaded for you.

## Why bother

Two reasons, and the second is the one that catches people.

A template is the part of the hub that prints the most bare English: *Log in*,
*Search*, *Skip to main content*, *Help*. If those are literals in `index.php`,
the hub cannot be translated no matter what anyone does to the components.

More immediately, a hub that installs your template can replace any string in
it *without editing your files* — by dropping a file into its own
`app/bootstrap/site/language/` directory. That is the supported way for one hub
to say **Reserve** where `northgate` says **Book**. A hardcoded string forces a
fork of the template.

## Where the files go

```
app/templates/northgate/
    language/
        en-GB/
            en-GB.tpl_northgate.ini
```

The directory is `language`, singular. Inside it, one directory per language
tag, and inside that a file named `{tag}.tpl_{template}.ini`. The `tpl_`
prefix and the `.ini` extension are both required:
[`Translator::load()`](../../../core/libraries/Hubzero/Language/Translator.php)
builds the filename as `{basePath}/language/{tag}/{tag}.{extension}.ini`, and
the extension it is given for a template is `tpl_` plus the template's
directory name.

All four site templates and `kameleon` ship one. Nothing else in a template
tree is scanned for strings.

> **Warning:** `muse scaffolding copy template` rewrites file *contents* only,
> and only at the top level of the copied directory — `language/` is a
> subdirectory, so its file arrives untouched and still named
> `en-GB.tpl_kimera.ini`, still full of `TPL_KIMERA_*` keys. Rename the file to
> match the new template and rewrite the key prefix, or none of its strings
> load.

## Writing one

A language file is key/value pairs. Keys are case-insensitive — they are
upper-cased before lookup — but the convention is upper case throughout, words
separated by underscores, prefixed `TPL_{TEMPLATE}_`:

<!--include: core/templates/welcome/language/en-GB/en-GB.tpl_welcome.ini:1-20-->

The prefix is not enforced. It exists to keep one extension's keys from
colliding with another's, because every loaded file merges into one flat table
of strings. Two extensions that both define `TITLE` will fight over it.

Files must be UTF-8 with no byte order mark.

## Loading

You do not need to load the template's own file. The document loads it while
fetching the template:

<!--include: core/libraries/Hubzero/Document/Type/Html.php:501-504-->

Note the pair of calls. The first looks under
`app/bootstrap/{client}/language/{tag}/`, which is where a hub puts a
*replacement* for a shipped template's strings; the second falls back to the
template's own directory. Whichever loads first wins, so a hub can override
individual templates' strings without touching `core/`.

To load some other extension's strings, call `Lang::load()` yourself. A
template layout that prints a `com_bookings` string needs this, because nothing
loads a component's language file for a page that is not running that
component:

```php
<?php
defined('_HZEXEC_') or die();

// From app/bootstrap/site/language/en-GB/en-GB.com_bookings.ini
Lang::load('com_bookings');

// From a specific directory
Lang::load('com_bookings', PATH_APP . '/components/com_bookings/site');
```

The signature is
`load($extension = 'hubzero', $basePath = PATH_APP, $lang = null, $reload = false, $default = true)`.
`PATH_APP` and `PATH_CORE` are special-cased: passing either on its own means
`{path}/bootstrap/{client}`, not the path itself. Any other base path is used
as given, with `/language/{tag}` appended.

`$default` — true by default — makes the loader fall back to `en-GB` when the
current language has no file of its own.

## Printing a string

```php
<p><?php echo Lang::txt('TPL_NORTHGATE_BOOK_AN_INSTRUMENT'); ?></p>
```

Extra arguments are passed through `sprintf`, so a file can hold format
strings:

```ini
TPL_WELCOME_CONGRATS="Congratulations, you are now running HUBzero %s."
```

```php
echo Lang::txt('TPL_WELCOME_CONGRATS', $version);
```

### What failure looks like

A key with no translation is returned unchanged. There is no error and no blank
space: the page shows the literal `TPL_NORTHGATE_BOOK_AN_INSTRUMENT` where the
words should be. Every cause produces the same symptom —

- the file is named for the template it was copied from;
- the key is defined but the file is in `languages/` rather than `language/`;
- the key is misspelled in the layout;
- the string is a component's and nobody called `Lang::load()`.

Turn on **Debug Language** in Global Configuration to tell them apart: found
strings are wrapped in `**asterisks**` and missing ones in `??question marks??`.
A key with no asterisks and no question marks is not being looked up at all.

## Strings the administrator sees

Two things outside the template itself are translated, and they do not load the
same file.

**Parameter labels.** The `label` and `description` attributes in the
`<config>` block of [`templateDetails.xml`](10-packaging.md) are language keys.
[`com_templates`](../../../core/components/com_templates/admin/controllers/styles.php)
loads the template's **main** `tpl_{template}` file before it builds the
style-editing form, so `TPL_NORTHGATE_FIELD_HEADER_LABEL` resolves there.

**Module position names.** When
[`com_modules`](../../../core/components/com_modules/admin/controllers/modules.php)
builds the position list it reads `<positions>` from every installed template's
manifest and, for each, loads only `tpl_{template}.sys` — the `.sys.ini` file,
never the main one. It then looks for a key named
`TPL_{TEMPLATE}_POSITION_{POSITION}`, upper-cased, and falls back to
`COM_MODULES_POSITION_{POSITION}` from its own language file if the template's
key is not defined.

> **Warning:** No shipped Hubzero template has a `.sys.ini`, so no shipped
> template's `TPL_*_POSITION_*` key is ever loaded. Every position label you see
> in the administrator today comes from the `COM_MODULES_POSITION_*` fallback,
> or renders as the raw key when there is no fallback either. If you want your
> own positions named, ship
> `language/en-GB/en-GB.tpl_northgate.sys.ini` — a `.sys.ini` holds the subset
> of strings the administrator interface needs before the extension itself runs.
> Putting the keys in the main file has no effect on this list.

> **Tip:** The [Languages](../07-extensions/03-languages.md) chapter covers the
> same file format for components, modules and plugins.

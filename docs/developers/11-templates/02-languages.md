<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/templates/languages
source-id: 3505
modified: 2019-02-26
-->
# Languages

Every string a template prints should come from a language file, so the
template can be translated without being edited. A template's strings live in
its own `language` directory and are loaded for you.

## Where the files go

```
core/templates/kimera/
    language/
        en-GB/
            en-GB.tpl_kimera.ini
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

> **Warning:** `muse scaffolding copy template` rewrites file *contents* but
> not filenames, so a template copied from `kimera` still has a file called
> `en-GB.tpl_kimera.ini`. Rename it to match the new template or none of its
> strings will load.

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

To load some other extension's strings, call `Lang::load()` yourself:

```php
<?php
defined('_HZEXEC_') or die();

// From app/bootstrap/site/language/en-GB/en-GB.com_example.ini
Lang::load('com_example');

// From a specific directory
Lang::load('com_example', PATH_CORE . '/components/com_example/site');
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
<p><?php echo Lang::txt('TPL_KIMERA_LOGIN'); ?></p>
```

Extra arguments are passed through `sprintf`, so a file can hold format
strings:

```ini
TPL_WELCOME_CONGRATS="Congratulations, you are now running HUBzero %s."
```

```php
echo Lang::txt('TPL_WELCOME_CONGRATS', $version);
```

A key with no translation is returned unchanged, which is why an untranslated
page shows `TPL_KIMERA_LOGIN` rather than an empty space. Turn on **Debug
Language** in Global Configuration to make this obvious: found strings are
wrapped in `**asterisks**` and missing ones in `??question marks??`.

## Strings the administrator sees

Two things outside the template itself are translated from the same file.

**Parameter labels.** The `label` and `description` attributes in the
`<config>` block of [`templateDetails.xml`](10-packaging.md) are language keys.
`com_templates` loads `tpl_{template}` before it builds the style-editing form,
so `TPL_KIMERA_FIELD_HEADER_LABEL` resolves there.

**Module position names.** When `com_modules` builds the position list it reads
`<positions>` from the manifest and looks for a key named
`TPL_{TEMPLATE}_POSITION_{POSITION}`, upper-cased. Define
`TPL_MYTEMPLATE_POSITION_INTROBLOCK` to give the `introblock` position a
readable name; without it the administrator sees the raw position name.

Both of those lookups prefer a `.sys.ini` file — `en-GB.tpl_kimera.sys.ini` —
and fall back to the main file. `.sys.ini` is the Joomla convention for the
subset of strings the administrator needs before the extension runs. No shipped
Hubzero template has one, and nothing breaks without it.

> **Tip:** The [Languages](../07-extensions/03-languages.md) chapter covers the
> same file format for components, modules and plugins.

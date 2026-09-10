<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/components/languages
-->
# Languages

Every string a component shows a person comes out of an `.ini` file through
`Lang::txt()`. No component in the tree hard-codes display text, and neither
should yours: a hub can override any string without touching your code, and a
translator can supply another language without reading it.

## The files

Language files belong to a client, so each client directory has its own:

```
core/components/com_kb/
    site/language/en-GB/
        en-GB.com_kb.ini
    admin/language/en-GB/
        en-GB.com_kb.ini
        en-GB.com_kb.sys.ini
        en-GB.com_kb.menu.ini
```

The name is `{tag}.{extension}.ini` inside a directory named for the tag. The
loader looks in `{client directory}/language/{tag}/`, so the path is fixed
once the client is.

## The format

Key/value pairs, one per line, values in double quotes. Lines beginning with a
semicolon are comments:

<!--include: core/components/com_kb/site/language/en-GB/en-GB.com_kb.ini:7-27-->

Keys may contain letters, digits, and underscores, but no spaces. Convention is
uppercase, words separated by underscores, prefixed with the component name:
`COM_KB_WAS_THIS_HELPFUL`. Nothing enforces the prefix, but every language file
loaded into a request shares one flat table of strings, so an unprefixed
`SEARCH` will collide with somebody else's.

> **Note:** Lookup uppercases the key before matching, so `Lang::txt('com_kb_yes')`
> and `Lang::txt('COM_KB_YES')` find the same string. The file itself is
> conventionally uppercase.

## Translating

```php
echo Lang::txt('COM_KB_ARTICLES');
```

A key that is not defined is returned unchanged, which is why a missing
translation shows up as a screaming uppercase key rather than a blank space.

Any further arguments are passed to `sprintf()`, so a string can carry
placeholders:

```ini
COM_KB_FOUND_THIS_HELPFUL="%s out of %s people found this helpful."
```

```php
echo Lang::txt('COM_KB_FOUND_THIS_HELPFUL', $helpful, $total);
```

Number the placeholders — `%1$s`, `%2$s` — whenever a translator might need to
reorder them.

## When files are loaded

`Component::render()` loads two files before your entry point runs, in this
order:

1. `{client directory}/language/{tag}/{tag}.com_kb.ini` — the component's own
   strings.
2. `PATH_APP/bootstrap/{client}/language/{tag}/{tag}.com_kb.ini` — the hub's
   overrides.

The second is loaded after the first, so a hub can redefine any single string
by putting just that key in its own file. Nothing is copied and nothing needs
to be complete.

`SiteController`'s constructor loads the component's file from the client
directory as well, but only if nothing has loaded it yet — which covers a
controller instantiated outside the normal render path.

Requesting a non-default language loads the default language first, so a
partial translation falls back to English string by string rather than showing
raw keys.

## The system file

`{tag}.com_kb.sys.ini` in the **administrator** language directory holds the
strings the platform needs when your component is not the one running: its
name in the Extensions manager, and the labels for the menu item types it
offers.

<!--include: core/components/com_kb/admin/language/en-GB/en-GB.com_kb.sys.ini:7-21-->

`COM_KB` is the component's name wherever the platform lists extensions. The
`COM_KB_ARTICLES_VIEW_DEFAULT_TITLE` form is what the menu manager falls back
to when a view offers no layout metadata: `{COMPONENT}_{VIEW}_VIEW_DEFAULT_TITLE`.
When the layout does have an `.xml` file beside it, its `title` attribute is
used instead — and that attribute is itself passed through `Lang::txt()`, so it
may be a key. See [Views](07-views.md).

> **Note:** `{tag}.com_kb.menu.ini` is a leftover. Nothing in the codebase
> loads a `.menu.ini` file; the administrator menu takes its labels from
> `.sys.ini`. Several components still ship one. Do not add one to a new
> component.

## Adding a language

A translation is the same tree with a different tag: `fr-FR/fr-FR.com_kb.ini`
beside the `en-GB` directory. The general mechanics — installing a language
pack, the tag list, plural rules — are covered in
[Languages](../07-extensions/03-languages.md) and
[Languages](../05-basics/04-languages.md).

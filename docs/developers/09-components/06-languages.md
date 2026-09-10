<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/components/languages
-->
# Languages

Every string a component shows a person comes out of an `.ini` file through
`Lang::txt()`. No component in the tree hard-codes display text, and neither
should yours: a hub can override any string without touching your code, and a
translator can supply another language without reading it.

The habit costs nothing while you are writing and is expensive to add later,
because retrofitting it means finding every sentence in every layout.

## One file per client

Language files belong to a **client**, and the client that is running loads
only its own. This is the detail that catches people:

```
app/components/com_bookings/
    site/language/en-GB/
        en-GB.com_bookings.ini          the site loads this, and only this
    admin/language/en-GB/
        en-GB.com_bookings.ini          the administrator loads this
        en-GB.com_bookings.sys.ini      and this, for lists of extensions
```

The name is `{tag}.{extension}.ini` inside a directory named for the tag. The
loader looks in `{client directory}/language/{tag}/`, so the path is fixed
once the client is.

> **Warning:** A string used on both sides has to be in **both** files. There
> is no shared file and nothing merges the two. Put
> `COM_BOOKINGS_RESERVATION_SAVED` only in `site/language/` and the
> administrator screen that saves a reservation renders the literal text
> `COM_BOOKINGS_RESERVATION_SAVED` — while `grep -r COM_BOOKINGS_RESERVATION_SAVED`
> across the tree finds it defined, twice over, and looks fine. When a key
> renders as itself, check *which* file it is in before you check anything
> else.

Duplicating a handful of strings is the cost of the split. It buys a site that
does not carry the administrator's vocabulary into every page it renders.

## The format

Key/value pairs, one per line, values in double quotes. Lines beginning with a
semicolon are comments:

<!--include: core/components/com_kb/site/language/en-GB/en-GB.com_kb.ini:7-27-->

Keys may contain letters, digits, and underscores, but no spaces. Convention is
uppercase, words separated by underscores, prefixed with the component name:
`COM_BOOKINGS_INSTRUMENT_UNAVAILABLE`. Nothing enforces the prefix, but every
language file loaded into a request shares one flat table of strings, so an
unprefixed `SEARCH` will collide with somebody else's — and the collision
appears only on the pages where both are loaded. See
[Languages](../07-extensions/03-languages.md#naming-keys).

> **Note:** Lookup uppercases the key before matching, so
> `Lang::txt('com_bookings_yes')` and `Lang::txt('COM_BOOKINGS_YES')` find the
> same string. The file itself is conventionally uppercase.

## Translating

```php
echo Lang::txt('COM_BOOKINGS_INSTRUMENTS');
```

A key that is not defined is returned unchanged, which is why a missing
translation shows up as a screaming uppercase key rather than a blank space.
Nothing is logged, so the only way to find the ones you have not exercised is
to look:

```bash
php tools/lint/undefined-language-keys.php app/components/com_bookings
```

Any further arguments are passed to `sprintf()`, so a string can carry
placeholders:

```ini
COM_BOOKINGS_SLOT_TAKEN="%s is already booked from %s until %s."
```

```php
echo Lang::txt('COM_BOOKINGS_SLOT_TAKEN', $instrument->title, $from, $until);
```

Number the placeholders — `%1$s`, `%2$s` — whenever a translator might need to
reorder them. Getting the count wrong is a PHP warning from `sprintf()` and a
sentence with a hole in it, not an exception.

## When files are loaded

`Component::render()` loads two files before your entry point runs, in this
order:

1. `{client directory}/language/{tag}/{tag}.com_bookings.ini` — the component's
   own strings, from the client that is running.
2. `PATH_APP/bootstrap/{client}/language/{tag}/{tag}.com_bookings.ini` — the
   hub's overrides.

The second is loaded after the first, so a hub can redefine any single string
by putting just that key in its own file. Nothing is copied and nothing needs
to be complete. That is the supported way for a hub to reword your component;
tell hub administrators about it rather than letting them edit your `.ini`,
which an update overwrites.

`SiteController`'s constructor loads the component's file from the client
directory as well, but only if nothing has loaded it yet — which covers a
controller instantiated outside the normal render path, from a module or a
plugin.

Requesting a non-default language loads the default language first, so a
partial translation falls back to English string by string rather than showing
raw keys.

## The system file

`{tag}.com_bookings.sys.ini` in the **administrator** language directory holds
the strings the platform needs when your component is not the one running: its
name in the Extensions manager, and the labels for the menu item types it
offers. Leave it out and your component appears in the administrator's lists
as `COM_BOOKINGS`.

<!--include: core/components/com_kb/admin/language/en-GB/en-GB.com_kb.sys.ini:7-19-->

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

A translation is the same tree with a different tag: `fr-FR/fr-FR.com_bookings.ini`
beside the `en-GB` directory. The tag is a language and a region joined by a
hyphen; the path is built from it literally, so `fr_FR` produces a file
nothing looks for. The general mechanics — the tag list, the `.sys.ini`
convention, plural rules — are covered in
[Languages](../07-extensions/03-languages.md) and
[Languages](../05-basics/04-languages.md).

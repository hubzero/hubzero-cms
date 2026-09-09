<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/plugins/languages
-->
# Languages

Strings a plugin renders — and the labels on its parameter form — come from
translation files inside the plugin directory. Unlike modules, a plugin's
language file is not loaded for you unless you ask.

## Where the files go

```
core/plugins/members/blog/
    language/
        en-GB/
            en-GB.plg_members_blog.ini
            en-GB.plg_members_blog.sys.ini
```

The directory is the language tag. The file name is the tag, a dot, and
`plg_{group}_{name}` — the same string used everywhere else the plugin is
identified.

The `.sys.ini` file holds only what the CMS needs before the plugin runs: the
name and description shown in the Plugin Manager and in the installer. It is
loaded by the administrative interface, not by the plugin. Everything the
plugin itself renders belongs in the plain `.ini`. A plugin that renders
nothing — `plg_content_formatwiki` is one — ships only the `.sys.ini`.

## Loading them

The base constructor loads nothing by default, because most plugins have no
strings and loading a file per plugin per request would be wasted work. Opt in
with a property:

```php
class plgMembersBlog extends \Hubzero\Plugin\Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
    protected $_autoloadLanguage = true;
}
```

With that set, the constructor calls `loadLanguage()` for you. You can also
call it yourself, later, from the one method that needs the strings:

```php
public function onMembers($user, $member, $option, $areas)
{
    $this->loadLanguage();
    // ...
}
```

## Where it looks

`loadLanguage($extension = '', $basePath = PATH_APP)` defaults `$extension` to
`plg_{group}_{name}` and then tries three locations, stopping at the first
that yields strings:

1. `$basePath/language/{tag}/{tag}.plg_group_name.ini` — the argument you
   passed. `PATH_APP` and `PATH_CORE` are rewritten by the translator to
   `{path}/bootstrap/{client}`, the hub's own override directory.
2. `app/plugins/{group}/{name}/language/{tag}/…`
3. `core/plugins/{group}/{name}/language/{tag}/…`

The constructor's automatic call passes
`PATH_APP/bootstrap/{client}` explicitly, so the order is the same either way:
hub override, then `app/`, then `core/`.

> **Warning:** Loading stops at the first file that parses. A hub override in
> `app/bootstrap/site/language/en-GB/` therefore replaces the plugin's file
> rather than merging with it, and must contain every key the plugin uses.

The translator loads the site's default language before the requested one
unless `debug_lang` is on, so a key absent from a partial translation still
resolves through `en-GB`.

## Overriding the search

`loadLanguage()` is a normal method and a plugin may override it.
`plgGroupsForum` does, so that a super group can ship its own strings from
inside the group's own directory:

```php
public function loadLanguage($extension = '', $basePath = PATH_APP)
{
    if (empty($extension))
    {
        $extension = 'plg_' . $this->_type . '_' . $this->_name;
    }

    $group = \Hubzero\User\Group::getInstance(Request::getCmd('cn'));
    if ($group && $group->isSuperGroup())
    {
        $basePath = PATH_APP . DS . 'site' . DS . 'groups' . DS . $group->get('gidNumber');
    }

    // ... then the normal chain
}
```

## Writing the file

```ini
; @package    hubzero-cms
; @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
; @license    http://opensource.org/licenses/MIT MIT

; Note : All ini files need to be saved as UTF-8 - No BOM

PLG_MEMBERS_BLOG="Blog"
PLG_MEMBERS_BLOG_BROWSE="Browse"
PLG_MEMBERS_BLOG_NUM_COMMENTS="%s comments"
```

Keys are uppercase with underscores and start with `PLG_{GROUP}_{NAME}_` so
they cannot collide with another extension's. Values are double-quoted; the
files are parsed with `parse_ini_file()` in raw mode, so a literal double
quote inside a value is escaped as `\"`. Comments start with a semicolon.

## Using a string

```php
Document::setTitle(Document::getTitle() . ': ' . Lang::txt('PLG_MEMBERS_BLOG'));
```

`Lang::txt()` passes extra arguments through `sprintf`, and returns the key
unchanged when there is no translation — which is why a missing string appears
on the page in capitals rather than as an error.

> **Note:** Core plugin classes are in the global namespace, so `Lang` needs
> no import. A namespaced helper class beside the plugin does need `use Lang;`.
> See [Facades](../03-foundation/04-facades.md).

## Registering the file

List it in the manifest so the installer copies it:

```xml
<languages>
    <language tag="en-GB">en-GB.plg_members_blog.ini</language>
</languages>
```

See [Languages](../07-extensions/03-languages.md) for how the tag for a
request is chosen and how further languages are installed.

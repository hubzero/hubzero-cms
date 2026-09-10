<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/plugins/languages
-->
# Languages

Every string a plugin puts in front of a person — the subject line of the mail
`plg_bookings_notify` sends, the tab title a group plugin adds, the labels on
its own parameter form — comes from a translation file inside the plugin
directory. Putting them there is not only about translating: it is also what
lets a hub change your wording without editing your code.

Unlike modules, a plugin's language file is not loaded for you unless you ask.
That one difference is the whole of this chapter's trap.

## Where the files go

```
app/plugins/bookings/notify/
    language/
        en-GB/
            en-GB.plg_bookings_notify.ini
            en-GB.plg_bookings_notify.sys.ini
```

The directory is the language tag. The file name is the tag, a dot, and
`plg_{group}_{name}` — the same string used everywhere else the plugin is
identified.

The `.sys.ini` file holds only what the CMS needs before the plugin runs: the
name and description shown in the Plugin Manager. It is loaded by the
administrative interface, not by the plugin. Everything the plugin itself
renders belongs in the plain `.ini`. A plugin that renders nothing —
`plg_content_formatwiki` is one — ships only the `.sys.ini`.

## Loading them

The base constructor loads nothing by default, because most plugins have no
strings and loading a file per plugin per request would be wasted work. Opt in
with a property:

```php
class plgBookingsNotify extends \Hubzero\Plugin\Plugin
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
call it yourself, later, from the one method that needs the strings — which is
the better choice for a plugin in a group that is imported on every request,
or one like `plg_bookings_notify` whose strings are needed on the rare request
that creates a reservation:

```php
public function onReservationCreate($reservation)
{
	$this->loadLanguage();
	// ...
}
```

> **Warning:** Forget both and nothing breaks loudly. `Lang::txt()` returns the
> key it was given when there is no translation, so the mail goes out with
> `PLG_BOOKINGS_NOTIFY_SUBJECT` as its subject line and the group tab is
> labelled `PLG_GROUPS_FORUM`. A raw key in capitals on a page or in an email
> means one of two things: the file was never loaded, or the key is not in it.

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
resolves through `en-GB`. Turning `debug_lang` on in the site configuration is
the fastest way to find keys that are not resolving: it stops that fallback,
wraps every string it did find in `**`, and every one it did not in `??`.

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

That is a super group feature, not a general pattern. A plugin that is not
serving a group has no reason to override the search.

## Writing the file

```ini
; @package    hubzero-cms
; @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
; @license    http://opensource.org/licenses/MIT MIT

; Note : All ini files need to be saved as UTF-8 - No BOM

PLG_BOOKINGS_NOTIFY="Bookings - Notify"
PLG_BOOKINGS_NOTIFY_SUBJECT="New reservation for %s"
PLG_BOOKINGS_NOTIFY_BOOKED_BY="Booked by %s"
PLG_BOOKINGS_NOTIFY_SEND_FAILED="Failed to mail the reservation notice to %s"
PLG_BOOKINGS_NOTIFY_MANAGER_EMAIL="Manager address"
```

Keys are uppercase with underscores and start with `PLG_{GROUP}_{NAME}_` so
they cannot collide with another extension's — the string table is one flat
namespace for the whole request, and the last file loaded wins. Values are
double-quoted; the files are parsed with `parse_ini_file()` in raw mode, so a
literal double quote inside a value is escaped as `\"`. Comments start with a
semicolon.

## Using a string

```php
$subject = Lang::txt('PLG_BOOKINGS_NOTIFY_SUBJECT', $reservation->instrument->get('title'));
```

`Lang::txt()` passes extra arguments through `sprintf`, and returns the key
unchanged when there is no translation — which is why a missing string appears
on the page in capitals rather than as an error. A key with a `%s` in it and
no argument passed comes out with the `%s` still in it, for the same reason.

> **Note:** The plugin entry class is in the root namespace, so `Lang` needs no
> import there. A *namespaced* helper class beside the plugin is a different
> case, and not the simple one: an unimported facade in a namespaced file
> usually resolves anyway, through a fallback in the alias autoloader, and
> then fails in a few specific situations. Import it. See
> [Facades](../03-foundation/06-facades.md#importing-a-facade), and run
> `php tools/lint/undefined-language-keys.php app/plugins/bookings/notify` to
> find keys with no string behind them.

## Registering the file

List it in the manifest so the installer copies it:

```xml
<languages>
	<language tag="en-GB">en-GB.plg_bookings_notify.ini</language>
	<language tag="en-GB">en-GB.plg_bookings_notify.sys.ini</language>
</languages>
```

See [Languages](../07-extensions/03-languages.md) for how the tag for a
request is chosen and how further languages are installed.

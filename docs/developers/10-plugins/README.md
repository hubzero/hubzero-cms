<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/plugins
-->
# Plugins

A plugin is a class whose public methods are bound to event names. Something
elsewhere triggers an event; every enabled plugin with a matching method runs,
and what those methods return is collected and handed back to the caller. That
is the entire mechanism. It is how the login form finds its authentication
providers, how a group page grows a Forum tab, and how wiki markup becomes
HTML.

Reach for a plugin when you want to *react* to something a component already
does, and you do not want to edit that component to do it. The component
triggers an event because it does not know, and should not know, what a
particular hub wants to happen next. If instead the thing you are adding owns
a URL and a screen of its own, you want a component; if it draws a box beside
somebody else's page, you want a module. The
[table of the four kinds](../07-extensions/README.md#which-kind-do-you-want)
is the quick way to decide.

> **Warning:** **A plugin with no row in `#__extensions` never loads at all.**
> [`Hubzero\Plugin\Loader::all()`](../../../core/libraries/Hubzero/Plugin/Loader.php)
> builds its list from that table and never looks at the filesystem, so a
> plugin sitting on disk that no migration registered is not disabled — it is
> invisible. Nothing is required, nothing is constructed, nothing is bound to
> an event, and there is no error, no log line and no entry in the Plugin
> Manager. Of the four extension kinds, this is the one that fails most
> silently; see
> [what a missing row costs](../07-extensions/README.md#how-an-extension-is-found).
> Writing the migration is step one, not step last. See
> [Migrations](01-migrations.md).

## The example carried through this section

The rest of this book builds `com_bookings`, a component that books a lab's
instruments. When it saves a new reservation it triggers one event of its
own:

```php
Event::trigger('bookings.onReservationCreate', array($reservation));
```

The component does not send mail. This lab wants the instrument's manager
emailed whenever somebody books, so that behaviour goes in a plugin —
`plg_bookings_notify` — which every chapter here builds a piece of. Another
hub running the same component might answer the same event by writing to a
shared calendar, or not install a plugin at all and get nothing. That choice
belongs to whoever runs the hub, which is the point of triggering an event
instead of sending the mail from the component.

## The smallest working thing

Three files under `app/plugins/bookings/notify/`, and one command.

`notify.php`:

```php
<?php
// No direct access
defined('_HZEXEC_') or die();

class plgBookingsNotify extends \Hubzero\Plugin\Plugin
{
	public function onReservationCreate($reservation)
	{
		$to = $this->params->get('manager_email');

		if (!$to)
		{
			return;
		}

		$message = new \Hubzero\Mail\Message();
		$message->setSubject('New reservation')
		        ->addFrom(Config::get('mailfrom'), Config::get('sitename'))
		        ->addTo($to)
		        ->addPart('A new reservation was created.', 'text/plain');

		$message->send();
	}
}
```

`notify.xml` — the manifest, which the Plugin Manager reads for the display
name and the parameter form. `migrations/Migration20260210000000PlgBookingsNotify.php`:

```php
<?php

use Hubzero\Content\Migration\Base;

defined('_HZEXEC_') or die();

class Migration20260210000000PlgBookingsNotify extends Base
{
	public function up()
	{
		$this->addPluginEntry('bookings', 'notify');
	}

	public function down()
	{
		$this->deletePluginEntry('bookings', 'notify');
	}
}
```

Then:

```bash
php core/bin/muse migration -f
```

Nothing else is declared anywhere. The method name `onReservationCreate` is
the whole registration, and the row the migration wrote is what makes the
loader find the file. The later chapters add the language file, the
parameters, and a view for the mail body.

## Groups

A plugin's group is the directory it sits in, and it is also the `folder`
column of the plugin's extension row and the prefix on the events it hears.
The group decides which events the plugin is loaded for and nothing else; it
is not a type in any stronger sense. The groups shipped in `core/plugins`
are:

```
answers         cron            members         tags
antispam        editors         metadata        tools
authentication  editors-xtd     newsletter      update
authfactors     extension       oaipmh          usage
blog            filesystem      projects        user
captcha         geocode         publications    whatsnew
cart            groups          resources       wiki
citation        handlers        search          xmessage
content         hubzero         support
courses         mail            system
```

Some are general: `system` plugins run on every request, `content` plugins
transform text on its way to the page, `user` plugins react to account
changes, `cron` plugins register scheduled jobs. Most of the rest belong to
one component — `groups`, `members`, `projects`, `resources`,
`publications` — and add a tab, a panel, or a step to it.

A hub adds its own group, as `com_bookings` does, simply by creating the
directory and triggering events prefixed with its name. There is no register
of groups to add to. Because of that there is also nothing to catch a typo:
see [Group and directory must match](02-structure.md#group-and-directory-must-match).

## What a plugin is made of

```
core/plugins/members/blog/
    assets/css/blog.css
    assets/js/blog.js
    language/en-GB/en-GB.plg_members_blog.ini
    language/en-GB/en-GB.plg_members_blog.sys.ini
    migrations/Migration20170831000000PlgMembersBlog.php
    views/browse/tmpl/default.php
    blog.php
    blog.xml
    composer.json
```

Only `blog.php` and `blog.xml` are required, plus the migration if the plugin
is ever to run. The class inside `blog.php` is `plgMembersBlog`, it extends
[`Hubzero\Plugin\Plugin`](../../../core/libraries/Hubzero/Plugin/Plugin.php), and
its public `onMembersAreas()` and `onMembers()` methods are what
`com_members` triggers.

## How a method becomes a listener

[`Hubzero\Plugin\Loader`](../../../core/libraries/Hubzero/Plugin/Loader.php)
reads the enabled plugins out of `#__extensions`, requires each file, and
constructs the class.
[`Hubzero\Events\Dispatcher::addListener()`](../../../core/libraries/Hubzero/Events/Dispatcher.php)
then calls `get_class_methods()` on the instance and registers the object
under *every* public method name it finds. When an event of that name is
triggered, the method is called with the event's arguments.

> **Warning:** Every public method becomes a listener, including ones you did
> not mean to expose. Keep helpers `protected` or `private`; a public
> `display()` will be called for any event named `display`.

## Which events exist

The authoritative list is generated from the source tree: the
[events reference](../../reference/events/README.md) covers 279 events across
41 groups, and for each one names the call sites that fire it, the arguments
they pass, and the plugins that already listen. Read the page for the group
you are extending before you invent a method name — half the time the
extension point you want is already there.

## In this section

- [Migrations](01-migrations.md) — registering the plugin. Read this first.
- [Structure](02-structure.md) — the directory layout and class naming.
- [Controllers](03-controllers.md) — the plugin class, and the events it can
  answer.
- [Languages](04-languages.md) — translation files and autoloading.
- [Views](05-views.md) — returning HTML from a plugin.
- [Assets](06-assets.md) — CSS, JavaScript, and images.
- [Configuration](07-configuration.md) — parameters, global and per-object.
- [Packaging](08-packaging.md) — the Composer and XML manifests, and how a
  plugin reaches a running hub.
- [Loading](09-loading.md) — importing groups, triggering events, and reading
  the responses.

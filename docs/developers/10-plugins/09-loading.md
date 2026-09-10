<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/plugins/loading
-->
# Loading

Nothing loads a plugin until something needs it. A group is imported — its
files required, its classes constructed, its public methods registered as
listeners — and only then can an event reach it.

This chapter is the other half of the section: not how to write a plugin, but
how a component reaches one. Read it when you are adding an extension point to
your own component, as `com_bookings` does, and when you are working out why a
plugin you have written is not being called.

## Importing a group

```php
Plugin::import('bookings');
```

That requires and constructs every enabled plugin registered in the `bookings`
group — taking each one's directory from `app/plugins` when it exists there
and `core/plugins` otherwise — and registers each instance with the
application's dispatcher. The `Plugin` facade fronts
[`Hubzero\Plugin\Loader`](../../../core/libraries/Hubzero/Plugin/Loader.php),
whose other useful methods are:

| Call | Result |
|---|---|
| `Plugin::import($type, $plugin = null, $autocreate = true)` | Import a whole group, or one plugin from it. |
| `Plugin::byType($type, $plugin = null)` | The raw extension rows for a group, or one row. |
| `Plugin::params($type, $plugin)` | A `Registry` of one plugin's parameters. |
| `Plugin::isEnabled($type, $plugin = null)` | Whether the group, or the plugin, has an enabled row. |
| `Plugin::path($type, $plugin = null)` | The directory, `app/` before `core/`. |
| `Plugin::all()` | Every enabled plugin row on the hub. |

Most components never call `import()`. Triggering a prefixed event does it for
you, and that is the form to use — see [Dot notation](#dot-notation) below.
Call `import()` directly only when you need something the trigger cannot give
you: one named plugin rather than the whole group
(`Plugin::import('mw', $app->name)` in `com_tools`), the group loaded before
some other code triggers into it (`com_login` with `authentication`), or a
group imported with autocreation off so you can construct the classes yourself
(`Hubzero\Html\Editor` with `editors-xtd`).

## What `all()` selects

`Plugin::all()` runs one query and caches the result for the request. It
returns rows from `#__extensions` where `type = 'plugin'`, `enabled >= 1`,
`state >= 0`, and `access` is one of the current user's authorised view
levels, ordered by `ordering` ascending.

Each of those four conditions is a way for a plugin to be silently absent, and
so is the absence of the row itself.

> **Note:** A plugin whose access level the visitor does not hold is not
> merely inert — it is never loaded, so its events never fire and its
> constructor never runs. A plugin that works when you are logged in as an
> administrator and does nothing for a guest is nearly always this.

> **Warning:** The result is also cached *across* requests, under a key made
> from the visitor's view levels, for `cachetime` minutes — fifteen by
> default. The administrator client forces the cache handler to `none`, so the
> Plugin Manager shows a change immediately while the site can go on ignoring
> it for a quarter of an hour. Clear the cache, or turn caching off, before
> concluding that enabling a plugin did not work.

Importing the same group twice is cheap: `import()` remembers which groups it
has done, and `Loader::init()` skips any plugin whose class is already
declared rather than constructing a second instance. One consequence is worth
knowing: a plugin file that some other code has already `require`d — an
`editors-xtd` button, say — is passed over by a later import of its group and
never gets registered as a listener.

## Triggering an event

```php
$results = Event::trigger('bookings.onReservationCreate', array($reservation));
```

`Event` is the facade for
[`Hubzero\Events\Dispatcher`](../../../core/libraries/Hubzero/Events/Dispatcher.php).
`trigger()` takes an event name — or an `Event` object — and an array of
arguments, calls every registered listener, and returns an array of their
return values.

## Dot notation

Writing the group name into the event does the import for you:

```php
$results = Event::trigger('bookings.onReservationCreate', array($reservation));
```

The dispatcher splits the name at the first dot, treats the left half as a
listener group, imports it through the registered loader, and then triggers
the right half. This is the form used almost everywhere in the CMS —
`content.onContentPrepare`, `groups.onGroupAreas`, `user.onUserLogin` — and
it is why event handlers are named `onContentPrepare` and not
`onContentPrepareContent`.

Naming a group nothing has ever heard of is not an error. `bookings` is a
group because `com_bookings` says so; the loader looks for rows with
`folder = 'bookings'`, finds however many there are, and gets on with it.

> **Warning:** An event name with no dot loads no group at all. It reaches
> only listeners something else has already imported this request — in
> practice the `system` group, which is loaded early — and nothing else.
> `Event::trigger('onReservationCreate', …)` therefore works on some pages and
> not others. If a plugin's method is never called, check the prefix on the
> trigger before anything else.

> **Warning:** The group prefix only decides what gets *imported*. Once
> imported, all listeners live in one dispatcher, so triggering
> `bookings.onReservationCreate` will also reach a `system` plugin that
> happens to define `onReservationCreate`. Prefix your event names with
> something specific to the extension that owns them, and check the
> [events reference](../../reference/events/README.md) — 279 events across 41
> groups — before you settle on a name.

## Arguments and responses

Arguments are attached to the event by array key and spread across the
listener's parameters in order, so a handler's signature must match the array
the trigger builds. See [Controllers](03-controllers.md#how-arguments-arrive).

Every non-null return value is appended to the event's response array, in the
order the listeners ran. A listener that returns nothing contributes nothing —
which is how a plugin declines to answer an event it is registered for.
`plg_bookings_notify` returns nothing at all, so `com_bookings` gets an empty
array back and has nothing to do with it:

```php
Event::trigger('bookings.onReservationCreate', array($reservation));
```

A component that wants an answer collects one:

```php
$results = Event::trigger('members.onMembersAreas', array($user, $member));

foreach ($results as $area)
{
	// one entry per plugin that answered
}
```

Concatenating the array is a common idiom when the responses are HTML:

```php
$results = Event::trigger('wiki.onWikiParseText', array($content, $params, true, true));
$content = implode('', $results);
```

> **Note:** An empty array does not distinguish "no plugin is installed" from
> "every plugin returned null" from "the group name was wrong". There is no
> error to catch and nothing is logged. That is the cost of the extension
> point, and it is why the first thing to check is always the extension row.

## Ordering

Listeners run in priority order, then in registration order.
`Hubzero\Events\Priority` defines `MIN` (-3) through `MAX` (3) around
`NORMAL` (0), and plugins are all registered at `NORMAL`, so in practice the
order is the `ordering` column of `#__extensions` — which is what an
administrator changes by reordering the Plugin Manager list.

## Stopping propagation

A plugin can prevent the remaining listeners from running by stopping the
event. The event object is assigned to `$this->event` on the plugin before its
method is called:

```php
class plgSystemExample extends \Hubzero\Plugin\Plugin
{
	public function onAfterRoute()
	{
		if ($this->handled())
		{
			$this->event->stop();
		}
	}
}
```

`trigger()` checks `isStopped()` after each listener and breaks out of the
loop. Responses already collected are still returned; listeners that had not
yet run contribute nothing. Stopping an event is rude to every plugin ordered
after yours, and there is no way for them to find out it happened. Use it for
a plugin that genuinely takes over — an authentication factor, a request the
plugin has answered itself — and not to skip work.

## Closures

The dispatcher is not restricted to plugins. Any object can be added with
`Event::listen($object)`, and a closure can be registered if you name the
events it should answer, since a closure has no method name to infer one
from:

```php
Event::listen(function($event)
{
	// ...
}, array('onAfterRoute' => \Hubzero\Events\Priority::HIGH));
```

The closure receives the `Event` object itself, not the unpacked arguments;
read them with `$event->getArgument('name')` or `$event['name']`. This is the
one way to get a listener at a priority other than `NORMAL`, and the one way
to listen from code that is not a plugin — a
[service provider](../03-foundation/07-providers.md), for instance. It is not
a substitute for a plugin: nothing an administrator can see lists it, and
nothing can turn it off.

<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/plugins/controllers
-->
# Controllers

A plugin has one class, in the file named after it, extending
[`Hubzero\Plugin\Plugin`](../../../core/libraries/Hubzero/Plugin/Plugin.php). It
is called a controller here only by analogy with a component: it has no tasks,
no routing, and nothing it can decide to render. Its public methods are its
entire interface. Each one is registered under its own name as an event
listener and is called when an event of that name fires.

That is a smaller surface than a component controller, and the work of writing
a plugin is mostly deciding which events to answer and keeping everything else
out of the public interface.

## The smallest working thing

`plg_bookings_notify` answers one event and returns nothing:

```php
<?php
// No direct access
defined('_HZEXEC_') or die();

class plgBookingsNotify extends \Hubzero\Plugin\Plugin
{
	/**
	 * Email the lab manager when a reservation is created.
	 *
	 * @param   object  $reservation  Components\Bookings\Models\Reservation
	 * @return  void
	 */
	public function onReservationCreate($reservation)
	{
		if (!($to = $this->params->get('manager_email')))
		{
			return;
		}

		$body = $this->view('message', 'email')
			->set('reservation', $reservation)
			->loadTemplate();

		$message = new \Hubzero\Mail\Message();
		$message->setSubject(Lang::txt('PLG_BOOKINGS_NOTIFY_SUBJECT', $reservation->instrument->get('title')))
		        ->addFrom(Config::get('mailfrom'), Config::get('sitename'))
		        ->addTo($to)
		        ->addHeader('X-Component', 'com_bookings')
		        ->addPart($body, 'text/plain');

		if (!$message->send())
		{
			$this->setError(Lang::txt('PLG_BOOKINGS_NOTIFY_SEND_FAILED', $to));
		}
	}
}
```

Nothing registers `onReservationCreate` anywhere else. The method name is the
registration, and `com_bookings` triggering `bookings.onReservationCreate` is
what calls it.

## A real one

`plgContentFormatwiki` is the same shape with two methods:

<!--include: core/plugins/content/formatwiki/formatwiki.php:8-15-->

It answers `onContentBeforeSave` and `onContentPrepare`, and keeps its two
helpers, `_isWiki()` and `_key()`, private — which is not a style preference.
See the warning below.

## What the base class gives you

| Member | What it is |
|---|---|
| `$params` | `Hubzero\Config\Registry` of the plugin's parameters, built in the constructor. |
| `$event` | The `Hubzero\Events\Event` currently being dispatched to this plugin. |
| `$option` | `com_` plus the group name. Legacy, and wrong for any group that is not a component; do not rely on it. |
| `$_name`, `$_type` | The plugin's element and group, both `protected`. |
| `$_autoloadLanguage` | Set `true` to load the language file in the constructor. |
| `loadLanguage($extension = '', $basePath = PATH_APP)` | Loads a language file. See [Languages](04-languages.md). |
| `view($layout = 'default', $name = '')` | Returns a `Hubzero\Plugin\View`. See [Views](05-views.md). |
| `css()`, `js()`, `img()` | Asset helpers. See [Assets](06-assets.md). |
| `Plugin::getParams($name, $folder)` | Static; reads one plugin's params straight from the database. |

Because `Plugin` extends `Hubzero\Base\Obj`, which uses the `ErrorBag` trait,
`setError()`, `getError()`, and `getErrors()` are available too, and plugins
use them to hand a message back to the component that triggered them. The
component has to ask: an error set in a plugin goes nowhere on its own.

## The constructor

You rarely need one. If you write one, call the parent — it is what populates
`$params`, `$_name`, and `$_type`, and what loads the language file:

```php
public function __construct(&$subject, $config)
{
	parent::__construct($subject, $config);

	// Extra initialisation
}
```

`$subject` is the dispatcher the plugin was created with, retained only for
backward compatibility. `$config` is the plugin's `#__extensions` row cast to
an array, so `$config['name']`, `$config['type']`, and `$config['params']` are
what the parent reads.

The constructor runs when the group is imported, whether or not any of the
plugin's events ever fire. Keep it cheap: no queries, no HTTP. A `system`
plugin's constructor runs on every request on the hub.

## How methods become listeners

`Hubzero\Events\Dispatcher::addListener()` calls `get_class_methods()` on the
plugin instance and registers it once per public method name. There is no list
of events to declare and no annotation to write — the method name *is* the
registration.

> **Warning:** Every public method is registered, and that includes the
> seventeen a plugin inherits without writing: `__construct`, `loadLanguage`,
> `getParams`, `view`, `__toString`, `def`, `get`, `getProperties`, `set`,
> `setProperties`, `getError`, `getErrors`, `setError`, `setErrors`, `css`,
> `js` and `img`. Every plugin on the hub is a listener for an event called
> `get`. Add a public `display()` and it becomes a listener for `display`.
> Name your handlers `onSomething`, and make every helper `protected` or
> `private`.

The failure in the other direction is quieter still. A handler that is
`private`, or misspelled — `onReservationCreated` for
`bookings.onReservationCreate` — is simply not registered. The event fires,
`trigger()` returns an empty array, and nothing is logged. When a plugin does
not appear to run, check in this order: the extension row exists and is
enabled, the group directory matches the `folder` column, the method is
public, and the method name matches the event exactly, case included.

## How arguments arrive

The dispatcher does not call your method with the raw arguments. It calls the
listener with the `Event` object, and
[`Hubzero\Events\WrappedListener`](../../../core/libraries/Hubzero/Events/WrappedListener.php)
unpacks it: it assigns the event to `$this->event` on your plugin, takes
`$event->getArguments()`, re-indexes it if the keys are associative, and
spreads the values across your method's parameters in order.

So a trigger of

```php
Event::trigger('content.onContentPrepare', array($context, &$article, &$params, $page));
```

reaches

```php
public function onContentPrepare($context, &$article, &$params, $page = 0)
```

with the four values in the order they were given. Give every parameter after
the ones you require a default, because a caller may pass fewer — and because
several core events are triggered from more than one place with different
argument counts. A missing default is an `ArgumentCountError` raised inside
the dispatcher, which reads as a framework fault rather than a plugin one.

> **Note:** Arguments are spread positionally for four or fewer and passed
> through `call_user_func_array` for five or more. A `&$article` parameter
> still works because the argument is an object, and objects are handles; do
> not expect to modify a scalar argument in place, because what the listener
> receives is the dispatcher's copy.

Whatever the method returns is collected into the event's response array;
returning nothing contributes nothing, which is how a plugin declines an event
it is registered for. See [Loading](09-loading.md).

## Which events to answer

Event names are prefixed with the plugin group when triggered, in
`group.eventName` form; the method is named after the half on the right. The
lifecycle events, all in the `system` group, are the ones every hub developer
ends up using:

| Event | When |
|---|---|
| `system.onAfterInitialise` | The application is built, before routing. |
| `system.onAfterRoute` | The route has been resolved to a component. |
| `system.onAfterDispatch` | The component has run. |
| `system.onBeforeRender` | Before the document is rendered into the template. |
| `system.onAfterRender` | After rendering, before the response is sent. |

For everything else, read the
[events reference](../../reference/events/README.md). It is generated from the
source tree and covers 279 events across 41 groups, with the call sites that
fire each one, the arguments they pass, and the plugins that already listen —
which is both the list of what you can answer and the set of worked examples
for answering it. Start with the page for the group you are extending:
[content](../../reference/events/content.md),
[user](../../reference/events/user.md),
[groups](../../reference/events/groups.md),
[members](../../reference/events/members.md),
[system](../../reference/events/system.md).

> **Note:** A plugin is not restricted to its group's events. All imported
> plugins are registered with the same dispatcher, so a `system` plugin can
> implement `onContentPrepare` and it will fire. The converse is the hazard:
> two groups that pick the same event name will hear each other's triggers.
> The reference is also how you check that the name you are about to invent
> for your own component is not already taken.

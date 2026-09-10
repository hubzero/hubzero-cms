<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/services/events
-->
# Events

Events are how a component lets plugins take part in what it is doing
without knowing anything about them. The component triggers a named event
and gets back whatever the listeners returned; the listeners are the
plugins installed and enabled on the hub.

You trigger one when you want the hub's administrator, not you, to decide
what happens next. A booking component that must email the lab manager on
every reservation should just send the mail. A booking component that
triggers `bookings.onBookingSaved` lets one hub push the reservation into a
calendar, another into an invoicing system, and a third do nothing — none of
which you have to know about, and all of which are turned on and off from
the Plugin Manager.

The cost is that it is one more indirection, and a triggered event with no
plugin listening is silent. Trigger where an extension point is genuinely
wanted; call the code directly where it is not.

The `Event` facade resolves the `dispatcher` binding, which is a
[`Hubzero\Events\Dispatcher`](../../../core/libraries/Hubzero/Events/Dispatcher.php)
— wrapped in a `TraceableDispatcher` when the hub is in debug mode, so the
debug bar can list what fired.

## Triggering

```php
use Event;

$results = Event::trigger('bookings.onBookingSaved', array(&$booking, $isNew));
```

Two things about that line matter.

**The return value is an array.** `trigger()` collects each listener's
return value, skipping nulls, and hands back the collected array — not the
event, and not a single result. Code that treats it as a boolean is testing
"did any listener return something truthy", which is usually not what it
means:

```php
$results = Event::trigger('content.onContentPrepare', array('com_bookings.note', &$row, $params));

foreach ($results as $result)
{
    // ...
}
```

An empty array back means "no plugin in that group returned anything". It
does not mean the event failed, and it does not distinguish "no plugin
listens" from "every plugin returned null". There is no error to catch.

**The arguments are positional.** `trigger()` walks the array and calls
`addArgument($name, $value)` with the array's own keys, so a plain list
produces arguments named `0`, `1`, `2`. The listener is then called with
them spread across its parameters, which is why plugin methods have ordinary
signatures:

```php
public function onBookingSaved($booking, $isNew)
```

Pass an associative array instead and the keys become the argument names —
useful when the listener wants `$event->getArgument('file')` rather than a
positional parameter:

```php
Event::trigger('system.logActivity', [
    'activity' => [...],
    'recipients' => [...]
]);
```

References survive the trip: `array(&$booking, $isNew)` gives listeners a
handle on the caller's object, which is how `onContentPrepare` plugins
rewrite content in place. It also means a plugin you have never seen can
change your model before you save it. Pass by reference only where you mean
to offer that.

## The group prefix

The dot prefix is not decoration. `Event::__construct()` splits the name on
the first dot and keeps the left half as the event's **group**; `trigger()`
then calls `addListeners($group)` before dispatching, and the plugin loader
registered with the dispatcher loads and instantiates every enabled plugin
in that group.

```php
// loads core/plugins/cron/*, then calls onClosePending on each
Event::trigger('cron.onClosePending', array($job));
```

So `content.onContentPrepare` reaches the content plugins,
`members.onMembersAreas` the members plugins, `xmessage.onSendMessage` the
xmessage plugins.

> **Warning:** A name with no prefix — `onBookingSaved` — loads no group at
> all. It reaches only listeners registered some other way; in practice the
> system plugins are loaded early enough that they see these, and nothing
> else does. If you write a plugin, put it in a group and trigger
> `<group>.onSomething`; if your plugin's method is never called, the
> missing prefix on the trigger is the first thing to check. Nothing logs
> it — `trigger()` returns an empty array exactly as it would if the plugin
> were disabled.

> **Note:** Plugin groups are loaded lazily, on first trigger. A plugin's
> `__construct()` and `loadLanguage()` therefore do not run until something
> triggers an event in its group.

## Writing a listener

A plugin extending `Hubzero\Plugin\Plugin` is a listener already: name a
method after the event and it is called. The dispatcher wraps a plain
object in a
[`WrappedListener`](../../../core/libraries/Hubzero/Events/WrappedListener.php),
which sets `$this->event` on the plugin and unpacks the event arguments
into the method's parameters.

To register something else, hand it to the dispatcher:

```php
App::get('dispatcher')->addListener(new SystemListener);
```

With no second argument the object is registered for every event whose name
matches one of its public methods, at `NORMAL` priority. Pass an array to
restrict it and set priorities:

```php
App::get('dispatcher')->addListener(new SystemListener, array(
    'onBeforeRoute' => Hubzero\Events\Priority::HIGH,
    'onAfterRoute'  => Hubzero\Events\Priority::NORMAL
));
```

A closure is also a listener, but it must be told which events it listens
for — there are no method names to infer from — and it receives the `Event`
object rather than unpacked arguments:

```php
App::get('dispatcher')->addListener(
    function($event)
    {
        $foo = $event->getArgument(0);
    },
    array('onBeforeRoute' => Hubzero\Events\Priority::NORMAL)
);
```

<!--include: core/libraries/Hubzero/Events/Priority.php:14-23-->

Listeners with the same priority run in the order they were added.
`listen()` is an alias for `addListener()`. `removeListener()`,
`hasListener()`, `getListeners()`, `countListeners()` and
`clearListeners()` round out the registry.

## The event object

[`Hubzero\Events\Event`](../../../core/libraries/Hubzero/Events/Event.php)
carries the name, the group, the arguments, and the collected responses.

| Method | What it does |
|---|---|
| `getName()` / `getGroup()` | The name after the dot, and the part before it |
| `addArgument($name, $value)` | Set, but only if not already set |
| `setArgument($name, $value)` | Set, overwriting |
| `getArgument($name, $default = null)` | Read one |
| `hasArgument($name)` / `removeArgument($name)` / `clearArguments()` | Test, remove, empty |
| `getArguments()` | All of them |
| `addResponse($data)` / `getResponse()` | The collected return values |
| `stop()` / `resume()` / `isStopped()` | Propagation control |

It also implements `ArrayAccess` and `Countable`, so `$event['foo']` reads
an argument and `count($event)` counts them.

Build one yourself when you want to name arguments up front, then trigger
it:

```php
$event = new Hubzero\Events\Event('bookings.onBookingSaved');
$event->setArgument('booking', $booking);

$results = Event::trigger($event);
```

## Stopping propagation

A listener that has handled an event can keep the rest from running:

```php
public function onBeforeRoute($event)
{
    $event->stop();
}
```

The dispatcher checks `isStopped()` after each listener and breaks out of
the loop. Responses already collected are still returned. Note that a
listener reached through `WrappedListener` gets the event on
`$this->event`, not as a parameter, unless the event has no arguments.

The [events reference](../../reference/events/README.md) lists every event
the tree triggers and every plugin method that answers one.

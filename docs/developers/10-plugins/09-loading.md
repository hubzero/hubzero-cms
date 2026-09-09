<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/plugins/loading
-->
# Loading

Nothing loads a plugin until something needs it. A group is imported — its
files required, its classes constructed, its public methods registered as
listeners — and only then can an event reach it. This page covers importing a
group, triggering an event, and what comes back.

## Importing a group

```php
Plugin::import('groups');
```

That requires and constructs every enabled plugin registered in the `groups`
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

`Plugin::all()` runs one query and caches the result for the request. It
returns rows from `#__extensions` where `type = 'plugin'`, `enabled >= 1`,
`state >= 0`, and `access` is one of the current user's authorised view
levels, ordered by `ordering` ascending.

> **Note:** A plugin whose access level the visitor does not hold is not
> merely inert — it is never loaded, so its events never fire and its
> constructor never runs.

Importing the same group twice is cheap: `import()` remembers which groups it
has done, and even without that, a plugin whose class is already declared is
skipped rather than re-instantiated.

## Triggering an event

```php
$results = Event::trigger('onAlbumAdded', array($artist, $title));
```

`Event` is the facade for
[`Hubzero\Events\Dispatcher`](../../../core/libraries/Hubzero/Events/Dispatcher.php).
`trigger()` takes an event name — or an `Event` object — and an array of
arguments, calls every registered listener, and returns an array of their
return values.

## Dot notation

Writing the group name into the event does the import for you:

```php
$results = Event::trigger('media.onAlbumAdded', array($artist, $title));
```

The dispatcher splits the name at the first dot, treats the left half as a
listener group, imports it through the registered loader, and then triggers
the right half. This is the form used almost everywhere in the CMS —
`content.onContentPrepare`, `groups.onGroupAreas`, `user.onUserLogin` — and
it is why event handlers are named `onContentPrepare` and not
`onContentPrepareContent`.

> **Warning:** The group prefix only decides what gets *imported*. Once
> imported, all listeners live in one dispatcher, so triggering
> `media.onAlbumAdded` will also reach a `system` plugin that happens to
> define `onAlbumAdded`. Prefix your event names with something specific to
> the extension that owns them.

## Arguments and responses

Arguments are attached to the event by array key and spread across the
listener's parameters in order, so a handler's signature must match the array
the trigger builds. See [Controllers](03-controllers.md).

Every non-null return value is appended to the event's response array, in the
order the listeners ran. A listener that returns nothing contributes nothing —
which is how a plugin declines to answer an event it is registered for:

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

## Ordering

Listeners run in priority order, then in registration order.
`Hubzero\Events\Priority` defines `MIN` through `MAX` around `NORMAL`, and
plugins are all registered at `NORMAL`, so in practice the order is the
`ordering` column of `#__extensions` — which is what an administrator changes
by reordering the Plugin Manager list.

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
yet run contribute nothing.

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
read them with `$event->getArgument('name')` or `$event['name']`.

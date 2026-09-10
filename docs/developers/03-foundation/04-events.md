<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
screenshots: none
-->
# Events

Events are how one extension reaches another without either of them naming
the other. A component announces that something happened; whatever plugins
are installed and enabled answer. That is the only extension point the
platform has: components do not call plugins, templates do not call
components, and nothing in the tree looks up another extension's classes to
call a method on them.

You need this page in two situations. You are writing a plugin and want to
know what will call it and when. Or you are writing a component — an
instrument-booking component, say — and you want other people's plugins to
be able to react when a booking is saved, without com_booking knowing they
exist.

The full API of the dispatcher — arguments, priorities, stopping
propagation, registering something that is not a plugin — is in
[Events](../04-services/05-events.md) under Services. This page is the
wiring underneath it: where the dispatcher comes from, how a name reaches a
directory, and why an event sometimes reaches nobody.

## The smallest working pair

A component triggers a named event and reads back what came home:

```php
use Event;

// core/components/com_booking/site/controllers/bookings.php
$results = Event::trigger('booking.onBookingAfterSave', array(&$row, $isNew));
```

A plugin in the matching group answers it by having a method of that name:

```php
// core/plugins/booking/notify/notify.php
defined('_HZEXEC_') or die;

class plgBookingNotify extends \Hubzero\Plugin\Plugin
{
    public function onBookingAfterSave($row, $isNew)
    {
        // ...
        return true;
    }
}
```

Nothing connects those two files. The name does.

The plugin's entry file is `plugins/{group}/{name}/{name}.php` and the loader
`require`s it by that path, so autoloading is not involved in reaching it. It
accepts either of two class names: `plg{Group}{Name}` in the root namespace,
which is what every shipped plugin uses, or `Plugins\{Group}\{Name}`. Helper
classes *inside* the plugin are namespaced `Plugins\{Group}\{Name}\…` and are
autoloaded normally — `Plugins\Content\Formathtml\Parser` is
`core/plugins/content/formathtml/parser.php`. See
[Autoloading](03-autoloading.md).

> **Note:** `trigger()` returns an **array** of what the listeners returned,
> with nulls dropped — not a boolean and not the last result. Treating it as
> a boolean asks "did any plugin return something truthy", which is rarely
> what the caller means.

## Where the dispatcher comes from

The `Event` facade resolves the container key `dispatcher`, which
`EventServiceProvider` binds:

<!--include: core/bootstrap/Site/Providers/EventServiceProvider.php:25-41-->

In debug mode the real dispatcher is wrapped in a `TraceableDispatcher` so
the debug bar can list what fired — the quickest way to see whether an event
you expected actually happened.

Plugins become reachable one step later. `PluginServiceProvider` registers
the plugin loader as a service, then attaches it to the dispatcher in
`boot()`, after every provider has registered:

<!--include: core/bootstrap/Site/Providers/PluginServiceProvider.php:44-50-->

> **Warning:** Anything that triggers an event before the application has
> booted reaches no plugins at all, and fails silently — `trigger()` returns
> an empty array exactly as it does when no plugin answered. This is the
> ordering to suspect first when a plugin that is definitely enabled is
> definitely not being called.

## How a name reaches a directory

The part of the event name before the first dot is its **group**, and the
group is a plugin directory:

| Event name | Group | Plugins loaded from |
|---|---|---|
| `content.onContentPrepare` | `content` | `{app,core}/plugins/content/` |
| `members.onMembersAreas` | `members` | `{app,core}/plugins/members/` |
| `booking.onBookingAfterSave` | `booking` | `{app,core}/plugins/booking/` |
| `onBookingAfterSave` | none | nothing is loaded for it |

`Dispatcher::trigger()` sees the group and calls `addListeners($group)`
before dispatching, which asks the plugin loader for every enabled plugin in
that directory, instantiates each, and binds its public methods by name.
Groups load lazily, on the first trigger, so a plugin's `__construct()` and
its language file do not run until something in its group fires.

An event name with **no** dot loads nothing. It still reaches listeners that
were registered some other way — in practice the `system` plugins, which are
loaded early — which is why a handful of undotted names in the tree appear to
work. Do not rely on it. Give your component's events a group prefix and
create the group.

## Nothing here scans the filesystem

A plugin directory is not a plugin. The loader builds its list from
`#__extensions`, filtered to rows that are enabled, not trashed, and whose
`access` level the current user holds, ordered by `ordering`. A plugin with
no row is invisible; a plugin whose access level the visitor does not have is
not loaded at all, rather than loaded and skipped, so there is nothing for
the plugin itself to guard against.

That row is written by a [migration](../06-database.md#migrations), which is
why every extension ships one. See
[Deploying extensions](../07-extensions/04-deployext.md) and
[Extensions](05-extensions.md#nothing-scans-the-filesystem).

So a new group is three things, in this order:

1. A directory, `core/plugins/booking/` (or `app/plugins/booking/` for one
   hub's own).
2. A plugin in it, with a manifest, and a migration that inserts its
   `#__extensions` row with `folder` set to `booking`.
3. A `Event::trigger('booking.…')` call in the component.

Miss the second and the trigger is a no-op with no error anywhere.

## Naming events for your own component

The convention across the tree is `group.onSubjectVerb`, with the group
matching the plugin directory and the method name repeating the subject:
`onBookingAfterSave`, `onBookingBeforeDelete`, `onBookingView`. The method
name has to be unique enough not to collide, because a plugin's public
methods are bound by name and a plugin may sit in a group that several
components trigger.

Pass the row by reference when you want listeners to be able to change it —
`array(&$row, $isNew)` — and expect them to. That is how content plugins
rewrite text in place.

Two shapes are worth telling apart before you settle on one:

- **A notification.** You trigger it, ignore the return, and carry on.
  Cheapest and hardest to misuse.
- **A question.** You trigger it and act on the answers, which means
  deciding what a plugin returning nothing means, and what two plugins
  returning different answers means. Say so in the docblock, because nothing
  in the mechanism enforces it.

## Finding the events that already exist

Do not read the tree for this. The
[events reference](../../reference/events/README.md) is generated from every
`Event::trigger()` call in the source and lists, per group, each event, the
places that fire it, and the plugins that answer it. It also shows which
events are declared by a plugin but fired by nothing, and which are fired
with no listener anywhere — both of which are worth knowing before you write
against one.

> **Note:** An event that the reference shows as fired in the CMS with zero
> listeners is not necessarily dead; it may be there for hubs to hook. An
> event with listeners but no call site *is* dead, and there are some.

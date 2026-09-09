<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/plugins/controllers
-->
# Controllers

A plugin has one class, in the file named after it, extending
[`Hubzero\Plugin\Plugin`](../../../core/libraries/Hubzero/Plugin/Plugin.php). It
has no tasks and no routing. Its public methods are its interface: each one is
registered under its own name as an event listener, and is called when an
event of that name fires.

## The class

<!--include: core/plugins/content/formatwiki/formatwiki.php:8-15-->

`plgContentFormatwiki` answers two events, `onContentBeforeSave` and
`onContentPrepare`, and keeps its two helpers, `_isWiki()` and `_key()`,
private.

## What the base class gives you

| Member | What it is |
|---|---|
| `$params` | `Hubzero\Config\Registry` of the plugin's parameters, built in the constructor. |
| `$event` | The `Hubzero\Events\Event` currently being dispatched to this plugin. |
| `$option` | `com_` plus the group name. Legacy; do not rely on it. |
| `$_name`, `$_type` | The plugin's element and group, both `protected`. |
| `$_autoloadLanguage` | Set `true` to load the language file in the constructor. |
| `loadLanguage($extension = '', $basePath = PATH_APP)` | Loads a language file. See [Languages](04-languages.md). |
| `view($layout = 'default', $name = '')` | Returns a `Hubzero\Plugin\View`. See [Views](05-views.md). |
| `css()`, `js()`, `img()` | Asset helpers. See [Assets](06-assets.md). |
| `Plugin::getParams($name, $folder)` | Static; reads one plugin's params straight from the database. |

Because `Plugin` extends `Hubzero\Base\Obj`, `setError()`, `getError()`, and
`getErrors()` are available too, and plugins use them to hand a message back
to the component that triggered them.

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
plugin's events ever fire. Keep it cheap: no queries, no HTTP.

## How methods become listeners

`Hubzero\Events\Dispatcher::addListener()` calls `get_class_methods()` on the
plugin instance and registers it once per public method name. There is no
list of events to declare and no annotation to write — the method name *is*
the registration.

> **Warning:** Every public method is registered, inherited ones included. A
> public `display()` will be called for any event named `display`, and a
> public helper named after nothing in particular is harmless only until
> someone triggers an event with that name. Name your event handlers
> `onSomething` and make everything else `protected` or `private`.

## How arguments arrive

The dispatcher does not call your method with the raw arguments. It calls the
listener with the `Event` object, and `Hubzero\Events\WrappedListener` unpacks
it: it assigns the event to `$this->event` on your plugin, takes
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
the ones you require a default, because a caller may pass fewer.

> **Note:** Arguments are passed by value through `call_user_func_array` for
> five or more arguments, and positionally for four or fewer. A `&$article`
> parameter still works because the argument is an object, and objects are
> handles; do not expect to modify a scalar argument in place.

Whatever the method returns is collected into the event's response array. See
[Loading](09-loading.md).

## Events the CMS triggers

Event names are prefixed with the plugin group when triggered, in
`group.eventName` form. The lifecycle events, all in the `system` group, are:

| Event | When |
|---|---|
| `system.onAfterInitialise` | The application is built, before routing. |
| `system.onAfterRoute` | The route has been resolved to a component. |
| `system.onAfterDispatch` | The component has run. |
| `system.onBeforeRender` | Before the document is rendered into the template. |
| `system.onAfterRender` | After rendering, before the response is sent. |

Content plugins see `content.onContentPrepare`, `onContentBeforeSave`,
`onContentAfterSave`, `onContentBeforeDelete`, `onContentAfterDelete`,
`onContentBeforeDisplay`, `onContentAfterDisplay`, `onContentAfterTitle`, and
`onAfterContentSubmission`. User plugins see `user.onUserBeforeSave`,
`onUserAfterSave`, `onUserBeforeDelete`, `onUserAfterDelete`, `onUserLogin`,
`onUserLogout`, `onUserLogoutFailure`, `onUserDeidentify`, and the profile and
password variants. `cron.onCronEvents` registers scheduled jobs;
`antispam.onAntispamDetector` and `onAntispamTrain` drive spam filtering.

Component groups define their own: `groups.onGroupAreas` and `groups.onGroup`,
`members.onMembersAreas` and `members.onMembers`, `projects.onProject`,
`resources.onResourcesSub`, `search.onIndex`, `wiki.onWikiParseText`, and so
on. The authoritative list is the code: search for `Event::trigger(` in the
component you are extending.

> **Note:** A plugin is not restricted to its group's events. All imported
> plugins are registered with the same dispatcher, so a `system` plugin can
> implement `onContentPrepare` and it will fire. The converse is the hazard:
> two groups that pick the same event name will hear each other's triggers.

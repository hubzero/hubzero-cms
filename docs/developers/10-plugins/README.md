<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/plugins
-->
# Plugins

A plugin is a class whose public methods are bound to event names. Something
elsewhere in the CMS triggers an event; every enabled plugin with a matching
method runs, and what those methods return is collected and handed back to the
caller. That is the entire mechanism. It is how the login form finds its
authentication providers, how a group page grows a Forum tab, and how wiki
markup becomes HTML.

Plugins live in `core/plugins` and `app/plugins`, one directory per group and
one directory per plugin inside it.

## Groups

A plugin's group is the directory it sits in. It decides which events the
plugin is loaded for and nothing else; the group is not a type in any stronger
sense. The groups shipped in `core/plugins` are:

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
`publications` — and add a tab, a panel, or a step to it. A hub may add its
own group simply by creating a directory and triggering events prefixed with
its name.

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

Only `blog.php` and `blog.xml` are required. The class inside `blog.php` is
`plgMembersBlog`, it extends
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

## In this section

- [Migrations](01-migrations.md) — registering the plugin.
- [Structure](02-structure.md) — the directory layout and class naming.
- [Controllers](03-controllers.md) — the plugin class, and the events it can
  answer.
- [Languages](04-languages.md) — translation files and autoloading.
- [Views](05-views.md) — returning HTML from a plugin.
- [Assets](06-assets.md) — CSS, JavaScript, and images.
- [Configuration](07-configuration.md) — parameters, global and per-object.
- [Packaging](08-packaging.md) — the Composer and XML manifests.
- [Loading](09-loading.md) — importing groups, triggering events, and reading
  the responses.

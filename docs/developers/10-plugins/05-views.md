<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/plugins/views
-->
# Views

Most plugins return data, not markup. But a plugin that adds a tab to a group
or a panel to a member profile has to return HTML, and that HTML belongs in a
layout file rather than in a string inside the class — because a layout can be
overridden by a template, and a string cannot.

## Where layouts live

```
core/plugins/groups/forum/
    views/
        sections/tmpl/display.php
        categories/tmpl/display.php
        categories/tmpl/edit.php
        threads/tmpl/display.php
```

Under `views` there is one directory per view *name*, and inside it a `tmpl`
directory holding one file per *layout*. Both names are yours to choose;
`default` is the layout used when none is given.

## Creating a view

`$this->view($layout, $name)` on the plugin returns a
[`Hubzero\Plugin\View`](../../../core/libraries/Hubzero/Plugin/View.php)
configured for this plugin. Note the argument order: **layout first, name
second**. `plgGroupsForum` renders `views/sections/tmpl/display.php` with

```php
$this->view = $this->view('display', 'sections');
```

Either argument may be omitted. `$this->view()` gives the `default` layout of
a view named after the plugin itself, so a plugin called `blog` with a single
screen can keep it at `views/blog/tmpl/default.php`.

Constructing the view class directly works too, and is what the helper does
underneath:

```php
$view = new \Hubzero\Plugin\View(array(
    'folder'  => 'groups',
    'element' => 'forum',
    'name'    => 'sections',
    'layout'  => 'display'
));
```

`folder` is the plugin group, `element` is the plugin, `name` is the view
directory, and `layout` is the file inside `tmpl`.

## Passing data and rendering

`set()` assigns a variable and returns the view, so calls chain, and
`loadTemplate()` renders the layout and returns the markup as a string:

<!--include: core/plugins/groups/forum/forum.php:554-565-->

Return that string from your event handler. Use `loadTemplate()`, not
`display()`: `display()` echoes, which puts the output wherever the buffer
happens to be rather than in the response the component asked for.

Assigning to properties works as well as `set()` — `$view->group = $group;` —
and reads the same in the layout.

## Writing a layout

```php
<?php
// No direct access
defined('_HZEXEC_') or die();
?>
<div class="section">
	<h3><?php echo $this->escape($this->group->get('description')); ?></h3>

	<?php foreach ($this->sections as $section) : ?>
		<p><?php echo $this->escape($section->get('title')); ?></p>
	<?php endforeach; ?>
</div>
```

Inside the layout, `$this` is the view. Every variable you `set()` is a
property. `$this->escape()` is available for anything that came from the
database or the request, and layouts run in the global namespace, so `Lang`,
`Route`, `User`, and the rest need no imports.

## Template overrides

`Hubzero\Plugin\View` adds a fallback search path so a template can replace
any layout:

```
{template}/html/plg_{group}_{element}/{view name}/{layout}.php
```

For the forum's sections view under the `kimera` template that is
`core/templates/kimera/html/plg_groups_forum/sections/display.php`. The
plugin's own file is tried first and the template path second. See
[Overrides](../11-templates/09-overrides.md).

## Sub-views

`view()` exists on the view class too, with the same layout-first argument
order, so a layout can render a partial without repeating the folder and
element:

```php
<?php
$this->view('_entry')
     ->set('entry', $entry)
     ->display();
?>
```

That looks for `views/{current view name}/tmpl/_entry.php`. Pass a second
argument to reach a different view directory. Use `display()` here, because
you do want the output echoed into the enclosing layout; use `loadTemplate()`
if you need the string.

## Helpers

Calling an undefined method on a plugin view makes it look for a helper: first
a file `helpers/{method}.php` under the plugin directory, then a class named
`Plugins\{Group}\{Element}\Helpers\{Method}`, falling back to the older
`Plugin{Group}{Element}Helper{Method}`. If the class is invokable it is bound
to the view and called. This is how plugins share a formatting routine between
several layouts without a global function.

> **Note:** A plugin does not have to use views at all. Returning a small
> string of markup from an event handler is legitimate for a one-line
> response, and `plg_content_*` plugins that rewrite article text never build
> a view. Reach for a view when the markup is longer than the logic.

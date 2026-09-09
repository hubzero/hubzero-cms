<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/components/views
-->
# Views

A view is two things: a `Hubzero\Component\View` object that carries data, and
a PHP file — the layout — that turns that data into markup. The controller
fills the object; the layout reads it back as `$this`.

## Where layouts go

```
core/components/com_kb/
    site/views/
        articles/            the view name, matching the controller
            tmpl/
                display.php  the layout, matching the task
                display.xml  menu-item metadata for that layout
                category.php  category.xml
                article.php   article.xml
                _list.php     partials
                _comment.php
                _vote.php
```

The controller has already built a view for you before your task runs: its
name is the controller name and its layout is the task name. A controller
`Articles` running task `article` renders
`site/views/articles/tmpl/article.php`. Nothing needs to be wired up.

A layout whose basename starts with an underscore is a partial. The convention
matters beyond readability — the menu manager ignores layouts with an
underscore in the name when it lists menu item types, so a partial never turns
up as something an administrator can link to.

## Passing data in

`set()` chains, which is why most tasks end in one statement:

```php
$this->view
    ->set('article', $article)
    ->set('category', $category)
    ->setLayout('article')
    ->display();
```

`$this->view->article = $article` works too, and `assign()` takes an array or
an object and copies its public properties across. Names beginning with an
underscore are refused, because those are the view's own.

Four variables are already set for you: `option`, `controller`, `task`, and
`baseurl`.

## Reading data out

```php
<h3><?php echo $this->escape($this->article->get('title')); ?></h3>
<p><?php echo Lang::txt('COM_KB_LAST_MODIFIED'); ?></p>
```

Anything that came from a person goes through `$this->escape()`. Content that
is meant to carry markup goes through the content parser instead — see
[Models](models.md).

> **Note:** A layout file declares no namespace, and `include` does not
> inherit one, so a layout runs in the global namespace. That is why
> `Lang::txt()` and `Route::url()` work in a layout with no `use` statements,
> while the same call in a controller needs one. Do not add a `namespace`
> declaration to a layout.

Every layout starts with the entry guard:

```php
// No direct access
defined('_HZEXEC_') or die();
```

## Choosing a different layout

`setLayout($name)` changes the file without disturbing the data — the usual
case being a failed save falling back to the edit form. It also accepts the
form `template:layout`: the part before the colon is recorded as the layout
template, the part after is the layout name.

`$this->setView($name, $layout)` on the controller replaces the view object
entirely, pointing it at a different view directory. Set your data after that
call.

## Partials

`$this->view($layout, $name = null)` builds a sibling view: same base path,
same `option`, `controller`, and `task`, a different layout. `com_kb` uses it
to render a comment thread:

<!--include: core/components/com_kb/site/views/articles/tmpl/article.php:153-161-->

Pass a second argument to reach a partial in another view's directory.
Data does **not** carry over from the parent — a partial sees only what you
`set()` on it, which is what makes them safe to reuse.

## The search order

`View::loadTemplate()` looks for `{layout}.php` in three directories, in this
order:

1. `{template}/html/{option}/{view name}/` — the active template's override
   directory;
2. `{base path}/views/{view name}/tmpl/`;
3. `{base path}/views/{view name}/`.

The third exists so a view can skip the `tmpl` directory entirely. The first
is how a hub restyles a component without editing it: dropping
`app/templates/hubzero/html/com_kb/articles/article.php` into place replaces
that one layout and leaves the rest of `com_kb` alone. See
[Overrides](../11-templates/09-overrides.md).

If none of the three has the requested layout, the search runs again for
`default.php`. If that is missing too, `InvalidLayoutException` is thrown with
a 404 status.

## Layout metadata

The `.xml` file beside a layout describes it to the menu manager, so an
administrator can create a menu item pointing straight at it:

<!--include: core/components/com_kb/site/views/articles/tmpl/article.xml:9-21-->

`title` is the label in the menu item type list and is passed through
`Lang::txt()`, so it may be a language key. `message` becomes the description.
Add `hidden="true"` to the `layout` element to keep a layout out of the list.
A view with no `.xml` files at all offers no menu item types.

## Building a view by hand

Outside a controller — in a module, a plugin, or a second view within one task
— construct one directly:

```php
$view = new \Hubzero\Component\View(array(
    'base_path' => PATH_COMPONENT,
    'name'      => 'articles',
    'layout'    => 'display'
));

$view->set('archive', $archive)
     ->display();
```

`display()` echoes; casting the view to a string returns the markup instead,
which is what you want when the result has to be embedded rather than emitted.

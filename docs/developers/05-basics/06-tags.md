<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/basics/tags
-->
# Tags

Tags are free text a member attaches to something — a resource, a wiki page,
a booking, another member. Any component can offer them, and they all share
one vocabulary, so a tag applied to a resource and the same tag applied to a
group are the same tag and lead to the same page.

You do not build this yourself. Adding tags to a component is one small
class and two calls:

```php
// core/components/com_bookings/models/tags.php
namespace Components\Bookings\Models;

class Tags extends \Components\Tags\Models\Cloud
{
    protected $_scope = 'bookings';
}
```

```php
// saving
$cloud = new Tags($instrument->get('id'));
$cloud->setTags(Request::getString('tags', ''), User::get('id'));

// displaying
echo $cloud->render();
```

Everything else on this page is detail around those two calls.

## How they are stored

Two tables. `#__tags` holds the vocabulary, one row per distinct tag:

| Column | Notes |
|---|---|
| `id` | |
| `tag` | The normalised form — lowercase, alphanumerics only. Unique |
| `raw_tag` | What the member typed, including case and spacing |
| `description` | Optional, edited by an administrator |
| `admin` | `0` member tag, `1` administrator tag, `2` core tag |
| `created`, `created_by`, `modified`, `modified_by` | |

`#__tags_object` records each attachment:

| Column | Notes |
|---|---|
| `tagid` | The tag |
| `objectid` | The id of the thing tagged |
| `tbl` | The **scope** — which component the object belongs to |
| `taggerid` | Who applied it |
| `taggedon` | When |
| `strength` | Weighting, `1` by default |
| `label` | An optional sub-category within the scope |

`tbl` is what keeps a blog entry with id 77 apart from an event with id 77.
It is the reason every component that tags things declares a scope of its
own.

Because the vocabulary is shared, renaming or merging a tag in the
administrator's Tags screen changes it everywhere at once.

## Declaring a scope

Subclass [`Components\Tags\Models\Cloud`](../../../core/components/com_tags/models/cloud.php)
and set `$_scope`. The whole class is that:

<!--include: core/components/com_blog/models/tags.php:8-25-->

Pick a scope name once and never change it. `#__tags_object` rows are found
by `tbl`, so renaming a scope does not migrate anything: every existing tag
on every existing record silently stops being found, and the records look as
though they were never tagged. Nothing errors.

Do not reuse another component's scope either. Two components sharing a
scope means record 12 in yours and record 12 in theirs carry each other's
tags.

Every component in the tree that tags anything has a `models/tags.php` of
this shape.

## Reading and writing

Construct it with the id of the object being tagged:

```php
use Components\Bookings\Models\Tags;

$cloud = new Tags($instrument->get('id'));
```

`Cloud::__construct($scope_id = 0, $scope = '')` also lets you pass a scope
directly if you would rather not subclass, and
`Cloud::getInstance($scope_id, $scope)` returns a per-request cached one.

Setting the tags on an object is a single call, and it is the same call
whether the object had tags before or not:

```php
$tags = Request::getString('tags', '');

$cloud = new Tags($instrument->get('id'));

if (!$cloud->setTags($tags, User::get('id')))
{
    $this->setError($cloud->getError());
}
```

That guard is what every controller in the tree writes, and it is worth
knowing that it never fires — see the warning below.

`setTags($tag_string, $tagger_id = 0, $admin = 0, $strength = 1, $label =
'')` takes a comma-separated string, works out which tags are new and which
have gone, adds and removes accordingly, and leaves untouched tags alone so
their timestamps survive. With `$tagger_id` left at `0` it uses the current
user.

| Method | What it does |
|---|---|
| `setTags($string, $tagger, $admin, $strength, $label)` | Replace the object's tags with this list |
| `add($tags, $tagger, $admin, $strength, $label)` | Add without removing anything |
| `remove($tags, $tagger)` | Remove the named tags |
| `removeAll($tagger)` | Remove every tag on the object |
| `tags($rtrn, $filters, $clear)` | The tag rows themselves |
| `render($rtrn, $filters, $clear)` | Formatted output — see below |
| `normalize($tag)` | The normalised form of one raw tag |

`add()` creates any tag that is not in the vocabulary yet, then links it.

`add()`, `remove()` and `removeAll()` return `false` and set an error when
the cloud has no `scope_id`, so construct it with an id.

> **Warning:** `setTags()` does not. It calls `add()` and `remove()`,
> discards what they return, and returns `true` regardless. Tagging a
> **new** record before saving it — `new Tags($booking->get('id'))` on an
> unsaved model, which constructs with `0` — therefore reports success and
> stores nothing at all. Save the row first, then tag it with the id
> `save()` assigned, and check the row after if you are unsure.

## Rendering

```php
$cloud = new Tags($entry->get('id'));

echo $cloud->render();
```

| Argument | Returns |
|---|---|
| `render()` or `render('html')` or `render('cloud')` | An `<ol class="tags">` of linked `<li>` items, rendered from com_tags' `_cloud` layout |
| `render('string')` | The raw tags, comma-separated: `My Tag, Your Tag` |
| `render('array')` | A flat array of the **normalised** tag strings: `['mytag', 'yourtag']` |

The HTML form comes from `com_tags`' `_cloud` layout, which reads
`show_sizes`, `show_tag_count` and `show_tags_sort` from
`Component::params('com_tags')` to decide between a plain alphabetical list,
a weighted cloud, and a list with counts. Note that none of those three is
declared in `com_tags/config/config.xml`, so they take their defaults — a
plain alphabetical list — unless a hub sets them another way.

`render('string')` is what an edit form wants, because it round-trips
through `setTags()`:

```php
$tags = $cloud->render('string');
```

Do not use `render('array')` for that. It returns the `tag` column — the
normalised form — so the edit form redisplays the member's *Mass
Spectrometer* as *massspectrometer*. `render('string')` returns `raw_tag`,
which is what they typed.

For the tag rows themselves — id, `raw_tag`, `admin`, `count` — call
`tags('list')` and iterate.

## The tag editor

The autocompleting input every edit form uses comes from the
`hubzero/autocompleter` plugin, reached by event:

```php
$tf = Event::trigger('hubzero.onGetMultiEntry', array(
    array('tags', 'tags', 'actags', '', $this->escape($tags))
));

if (count($tf))
{
    echo $tf[0];
}
else
{
    echo '<input type="text" name="tags" value="' . $this->escape($tags) . '" />';
}
```

The inner array is positional: what to complete (`tags`, `members`,
`groups`), the input's `name`, its `id`, a CSS class, and the current value
as a comma-separated string. The fallback matters — the plugin can be
disabled, and `trigger()` then returns an empty array.

`hubzero.onGetSingleEntry` is the same thing for a field that takes one
value.

<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/basics/tags
-->
# Tags

Tags are free text a member attaches to something — a resource, a wiki page,
a blog entry, another member. Any component can offer them, and they all
share one vocabulary, so a tag applied to a resource and the same tag
applied to a group are the same tag and lead to the same page.

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

Pick a scope name once and never change it; existing rows are found by it.
Every component in the tree that tags anything has a `models/tags.php` of
this shape.

## Reading and writing

Construct it with the id of the object being tagged:

```php
use Components\Blog\Models\Tags;

$cloud = new Tags($entry->get('id'));
```

`Cloud::__construct($scope_id = 0, $scope = '')` also lets you pass a scope
directly if you would rather not subclass, and
`Cloud::getInstance($scope_id, $scope)` returns a per-request cached one.

Setting the tags on an object is a single call, and it is the same call
whether the object had tags before or not:

```php
$tags = Request::getString('tags', '');

$cloud = new Tags($entry->get('id'));

if (!$cloud->setTags($tags, User::get('id')))
{
    $this->setError($cloud->getError());
}
```

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
All four writers return `false` and set an error when the cloud has no
`scope_id`, so construct it with an id.

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

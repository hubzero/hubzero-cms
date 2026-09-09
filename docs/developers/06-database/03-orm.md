<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/database/orm
-->
# ORM

A model that extends
[`Hubzero\Database\Relational`](../../../core/libraries/Hubzero/Database/Relational.php)
maps one database table to one PHP class. It carries a
[query builder](queries.md) internally and forwards to it any method it does
not define itself, so the whole query API is available on the model. What the
model adds on top is validation rules, automatically populated fields,
relationships to other models, and objects instead of `stdClass` rows.

## A model

The smallest useful model is a class with a namespace property:

```php
namespace Components\Blog\Models;

use Hubzero\Database\Relational;

class Entry extends Relational
{
    protected $namespace = 'blog';
}
```

The table name is derived in the constructor as `#__` + namespace + `_` +
the pluralised, lower-cased short class name, so `Entry` with a namespace of
`blog` becomes `#__blog_entries`. Set `protected $table` explicitly when the
real name does not follow that pattern. The primary key defaults to `id` and
is changed with `protected $pk`.

Here is a real model's declarations — a knowledge base article:

<!--include: core/components/com_kb/models/article.php:29-91-->

| Property | Meaning |
|---|---|
| `$namespace` | Table prefix segment used to build the table name |
| `$table` | The table name, when it is not derivable |
| `$tableAlias` | Alias applied to the table in the seeded query |
| `$pk` | Primary key column, default `id` |
| `$rules` | Field name to validation rule (see below) |
| `$always` | Fields regenerated on every save |
| `$initiate` | Fields generated only when the row is created |
| `$renew` | Fields generated only when an existing row is updated |
| `$parsed` | Fields whose content is run through the content parser |
| `$orderBy`, `$orderDir` | Defaults used by `ordered()`, and reported on the result set |

A model needing constructor work overrides `setup()` rather than
`__construct()`; `Relational` calls it at the end of construction.

## Retrieving rows

| Call | Returns |
|---|---|
| `Model::one($id)` | The model with that primary key, or `false` |
| `Model::oneOrFail($id)` | The same, but throws `RuntimeException` when missing |
| `Model::oneOrNew($id)` | The same, but returns an empty model when missing |
| `Model::oneByAlias($alias)` | The row whose `alias` column matches; an empty model when missing |
| `Model::all()` | A model with a fresh query, ready for constraints |
| `Model::blank()` | A new empty model |
| `->rows()` | A `Rows` collection of models |
| `->row()` | One model — an empty one when nothing matched |
| `->latest($limiter = 'created')` | The newest single row by that column |

```php
use Components\Blog\Models\Entry;

$entries = Entry::all()
    ->whereEquals('scope', 'site')
    ->whereEquals('state', Entry::STATE_PUBLISHED)
    ->ordered()
    ->paginated()
    ->rows();

foreach ($entries as $entry)
{
    echo $entry->title;
}
```

Models implement `IteratorAggregate`, so iterating one fetches for you — but
it iterates a *copy*, leaving the original query intact for a later call.
`ArrayAccess` is implemented too, so `$entry['title']` works alongside
`$entry->title` and `$entry->get('title')`.

> **Note:** `all()` accepts a `$columns` argument and ignores it entirely —
> the body is `return self::blank();`. Use `select()` to narrow the columns.

> **Note:** `one()` returns `false`, not an empty model, when the id does not
> exist, because it seeks into the fetched `Rows` collection. Prefer
> `oneOrFail()` or `oneOrNew()` so you always have an object to work with.

`count()` fetches the rows and counts them; `total()` runs a `COUNT()` query
instead and is what you want for pagination. `paginated($start = 'start',
$limit = 'limit')` reads those request variables, sets the limit clause, and
leaves a `Pagination` object on `$model->pagination`. `ordered($orderBy =
'orderby', $orderDir = 'orderdir')` reads the ordering from the request,
remembers it in user state, and understands `relationship.field` notation by
joining the relationship first. `whereIsMine($column = 'created_by')`
constrains to the current user.

### Result collections

`rows()` returns a [`Hubzero\Database\Rows`](../../../core/libraries/Hubzero/Database/Rows.php)
collection, keyed by primary key where possible. Beyond `count()`, `first()`,
`last()`, `next()` and `prev()`, it offers `seek($pk)`, `sort($field, $asc =
true)`, `fieldsByKey($key)`, `pickRandom($n)`, `latest()`, `toArray()`,
`toJson()`, `save()` and `destroyAll()`.

> **Note:** `Rows::search($key, $value)` returns `true` or `false`, not the
> matching model, despite the name.

## Attributes, transformers and parsed fields

Values that came from the database, or that you intend to save, live in the
attributes array. Read them with `get($key, $default = null)` or the magic
property, and set them with `set($key, $value)` or `set(['a' => 1, 'b' => 2])`.
Assigning a public property directly does *not* put it in the attributes and
so does not save it.

A method named `transformFoo()` makes `$model->foo` return its result instead
of the raw attribute. `com_blog` uses one to hand back the entry's parameters
as a `Registry` rather than a JSON string:

<!--include: core/components/com_blog/models/entry.php:566-584-->

A method named `helperFoo()` makes `$model->foo(...)` call it. Listing a field
in `$parsed` makes `$model->field` return the content run through
`Hubzero\Html\Builder\Content::prepare()`, and `$model->field('raw')` return it
with the format comment stripped.

## Validation

`$rules` maps a field name to one rule, or to several separated by `|`. The
built-in rules are `notempty`, `positive`, `nonzero`, `alpha`, `phone` and
`email`. `save()` calls `validate()` first and returns `false` if it fails;
the messages are then on `getErrors()`.

For anything the built-ins do not cover, register a closure from `setup()`
with `addRule($key, $rule)`. The closure receives the whole attributes array
and returns `false` when valid or a message when not:

<!--include: core/components/com_blog/models/entry.php:112-122-->

> **Note:** `Rules::validate()` iterates the *data*, not the rules. A rule on
> a field that was never set on the model is never evaluated, so `notempty`
> does not make a field required on create — it only rejects an empty value
> that was explicitly supplied.

## Automatic fields

For each field named in `$always`, `$initiate` or `$renew`, the model calls a
method named `automatic` plus the field name in studly case, passing the
current attributes, and stores the return value. `$initiate` runs on insert,
`$renew` on update, and `$always` on both.

`Relational` supplies `automaticCreated()` (now, unless already set),
`automaticCreatedBy()` (the current user id, unless already set) and
`automaticAssetId()` (resolves an `#__assets` entry). Everything else you
write yourself; a slug generator is the usual case:

<!--include: core/components/com_kb/models/article.php:113-124-->

## Saving and deleting

```php
$entry = Entry::oneOrNew($id);
$entry->set([
    'title'   => 'Release notes',
    'content' => 'Everything that changed.',
    'scope'   => 'site'
]);

if (!$entry->save())
{
    // $entry->getError() / getErrors() explain why
}
```

`save()` decides between insert and update from whether the primary key is
set, runs the automatics for that direction, filters the attributes down to
real table columns, purges the query cache, sets the new id back on the model,
and triggers `system.onContentSave` (plus `<table>_new` on a create).
`destroy()` removes the row, deleting any associated asset first and
triggering `system.onContentDestroy`.

`saveAndPropagate()` saves the model and then every relationship attached to
it with `attach($relationship, $models)`, stopping and copying the errors up
on the first failure.

`checkout($userId = null)` and `checkin()` set and clear `checked_out` and
`checked_out_time`, but only when those columns exist on the table;
`isCheckedOut()` reports the state.

## Relationships

A relationship is a public method that returns one of these:

| Method | Relationship |
|---|---|
| `oneToOne($model, $childKey = null, $thisKey = null)` | One row on the other side |
| `oneToMany($model, $relatedKey = null, $thisKey = null)` | Many rows on the other side |
| `belongsToOne($model, $thisKey = null, $parentKey = null)` | The inverse — this row's parent |
| `manyToMany($model, $associativeTable = null, $thisKey = null, $relatedKey = null)` | Many-to-many through a join table |
| `oneToManyThrough($model, $through, $relatedKey = null, $localKey = null)` | Many-to-many where the join table has its own model |
| `oneShiftsToMany($model, $relatedKey = 'scope_id', $shifter = 'scope', $thisKey = null)` | One-to-many where the child also stores which type of parent it has |
| `manyShiftsToMany($model, $associativeTable = null, $thisKey = 'scope_id', $shifter = 'scope', $relatedKey = null)` | The many-to-many equivalent |
| `shifter($shifter = 'scope', $thisKey = 'scope_id')` | The inverse of `oneShiftsToMany` — resolves the parent class from the shifter column |

`$model` is a class name. It is resolved first as given, then against the
current model's own namespace, so a sibling model can be named bare and
anything else needs its full namespaced name. Keys default from the model
names: `oneToMany` looks for `<modelname>_id` on the related table,
`belongsToOne` looks for `<parentmodelname>_id` on this one, and
`manyToMany` guesses an associative table of `#__<namespace>_<name>_<name>`
with the two names sorted alphabetically, so both sides agree.

The knowledge base article declares three:

<!--include: core/components/com_kb/models/article.php:192-200-->

```php
public function comments()
{
    return $this->oneToMany('Comment', 'entry_id');
}

public function votes()
{
    return $this->oneShiftsToMany('Vote', 'object_id', 'type');
}
```

Access them as properties — `$article->comments`, `$article->creator` — and
the model fetches the related rows once and keeps them. Call them as methods
instead when you want to constrain the related query before fetching:
`$article->comments()->whereEquals('state', 1)->rows()`.

`manyToMany` relationships add `connect($ids)`, `disconnect($ids)` and
`sync($ids)` for maintaining the associative table; `sync()` inserts what is
missing and deletes what should no longer be there. `oneToMany` adds
`save($data)`, `saveAll($models)` and `destroyAll()`.

### Eager loading and constraining

`including()` fetches named relationships alongside the main result set,
avoiding one query per row. It accepts nested names with dots, and a
`[name, closure]` pair to constrain the related query:

```php
$articles = Article::all()
    ->including('creator', ['comments', function ($comment) {
        $comment->whereEquals('state', 1);
    }])
    ->rows();
```

`whereRelatedHas($relationship, $constraint)`, its `orWhereRelatedHas()` twin,
and `whereRelatedHasCount($relationship, $count = 1, $depth = 0, $operator =
'>=')` narrow the main query by what exists on the other side. `forwardTo()`
adds relationships to search when an attribute is missing on this model.

Relationships can also be added from outside the class —
`Relational::registerRelationship($name, $closure)` registers one at runtime,
which is how plugins bolt a relationship onto a core model.

## Connections and caching

Models use the connection in `Relational::$connection`, which is null by
default and so falls through to `App::get('db')`.
`Relational::setDefaultConnection($driver)` points every model at another
driver — useful in tests. `disableCaching()` and `enableCaching()` control
whether a model's fetches consult the query cache, and `purgeCache()` empties
it.

## Trees

[`Hubzero\Database\Nested`](../../../core/libraries/Hubzero/Database/Nested.php)
extends `Relational` for nested-set trees, adding `saveAsRoot()`,
`saveAsChildOf($parent)`, `saveAsFirstChildOf()`, `saveAsLastChildOf()`,
`children()` (which is `descendants(1)`) and `descendants($level = null)`. Its
`destroy()` cascades: it removes the node, then every descendant, then closes
the gap left in the tree.

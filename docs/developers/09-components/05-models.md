<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/components/models
-->
# Models

A model represents one thing the component stores: an article, a category, a
comment. It owns the table, the validation rules, the relationships to other
models, and any behaviour that belongs to the record rather than to a screen.

Models live in `com_{componentname}/models/`, above the client directories,
because the site, the administrator, and the API all use the same ones. A file
`models/article.php` holds `Components\Kb\Models\Article`, and the class loader
finds it from the class name alone — no `require` is needed.

## The ORM model

New models extend
[`Hubzero\Database\Relational`](../../../core/libraries/Hubzero/Database/Relational.php).
The class declares what makes it different from every other model and inherits
the rest:

<!--include: core/components/com_kb/models/article.php:29-91-->

| Property | Meaning |
|---|---|
| `$namespace` | the table prefix after `#__` |
| `$table` | the table name, when the derived one is wrong |
| `$orderBy`, `$orderDir` | the default sort |
| `$rules` | per-field validation, checked on save |
| `$always` | fields recomputed on every save |
| `$initiate` | fields filled once, when the row is created |
| `$parsed` | fields the content parser may be run over |

The table name is derived, not declared. The constructor pluralises the
lowercased class name and prefixes it with `#__` and the namespace, so
`Article` with `$namespace = 'kb'` is `#__kb_articles`. Set `$table`
explicitly when a legacy table does not fit that pattern.

`$rules` names a validator per field — `notempty`, `positive|nonzero`, and so
on. `save()` returns false and fills the error bag when one fails, which is
why a save task reads:

```php
if (!$row->save())
{
    Notify::error($row->getError());
    return $this->editTask($row);
}
```

`$always` and `$initiate` are filled by convention: a field named `alias` in
either list looks for an `automaticAlias($data)` method on the model, `created`
for `automaticCreated()`, and so on. `com_kb`'s `Article` defines
`automaticAlias()` to slugify the title, so the alias is maintained without a
controller ever setting it.

`setup()` runs after construction. `com_kb` uses it to merge the article's own
`params` over the component's, giving `$article->params` as one registry.

## Relationships

Relationships are methods that return a relation object:

<!--include: core/components/com_kb/models/article.php:247-266-->

`belongsToOne()`, `oneToMany()`, `manyToMany()`, `oneShiftsToMany()`, and `oneToManyThrough()` are
the ones in common use. Call the method to get the related rows,
`$article->comments()->rows()`, or read it as a property,
`$article->creator->get('name')`, and the query runs lazily.

## Querying

`Relational` is also the query builder. Static calls start a query and instance
calls chain onto it:

```php
$rows = Article::all()
    ->whereEquals('category', $id)
    ->whereEquals('state', Article::STATE_PUBLISHED)
    ->ordered('filter_order', 'filter_order_Dir')
    ->paginated('limitstart', 'limit')
    ->rows();

$one = Article::oneOrNew($id);       // existing row, or a blank one
$one = Article::oneOrFail($id);      // or throw
```

`ordered()` and `paginated()` take the *names* of request variables, read the
sort column, direction, and page from the request, and remember them in model
state — which is why an administrator list controller passes
`'filter_order'` and `'limitstart'` rather than values. Full details are in the
[ORM](../06-database.md#orm) chapter.

## Plain model classes

Not everything that lives in `models/` is a `Relational`. `com_kb`'s
[`Archive`](../../../core/components/com_kb/models/archive.php) is an ordinary
class that assembles cross-model queries — the category list with article
counts, the most popular articles — and has no table of its own. Controllers
build one in `execute()` and hand it to the view.

That is the right shape whenever the thing you are modelling is a view over
several tables rather than a row in one.

## The older base class

`Hubzero\Base\Model` predates the ORM and is still present; older components
extend it. It wraps a `Hubzero\Database\Table` object, exposes state constants,
and offers `get()`/`set()` through `Hubzero\Base\Obj`. Do not use it for new
work — `Relational` does the same job with far less code, and new tables should
be modelled with it.

<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/components/models
-->
# Models

A model represents one thing the component stores: an instrument, a
reservation, a comment. It owns the table, the validation rules, the
relationships to other models, and any behaviour that belongs to the record
rather than to a screen.

Put the rules here, not in the controller. `com_bookings` has two ways to
create a reservation — a user booking a slot on the site, and a lab manager
adding one in the administrator — and a rule that lives on the model is
enforced by both. A rule written into `saveTask()` is enforced by one.

Models live in `com_{componentname}/models/`, above the client directories,
because the site, the administrator, and the API all use the same ones. A file
`models/reservation.php` holds `Components\Bookings\Models\Reservation`, and
the class loader finds it from the class name alone — no `require` is needed.

## The smallest one

```php
namespace Components\Bookings\Models;

use Hubzero\Database\Relational;

class Instrument extends Relational
{
	protected $namespace = 'bookings';
}
```

That is enough to read and write `#__bookings_instruments`:

```php
$instrument = Instrument::oneOrFail($id);
$instrument->set('title', 'Confocal microscope');
$instrument->save();
```

## The ORM model

New models extend
[`Hubzero\Database\Relational`](../../../core/libraries/Hubzero/Database/Relational.php).
The class declares what makes it different from every other model and inherits
the rest. `com_kb`'s `Article` shows the full set:

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

### The table name is derived

The constructor pluralises the lowercased class name and prefixes it with
`#__` and the namespace:

```php
$namespace = (!$this->namespace ? '' : $this->namespace . '_');
$plural    = \Hubzero\Utility\Inflector::pluralize(strtolower($this->getModelName()));
$this->table = $this->table ?: '#__' . $namespace . $plural;
```

So `Reservation` with `$namespace = 'bookings'` is `#__bookings_reservations`,
and `Instrument` is `#__bookings_instruments`. Set `$table` explicitly when an
inherited table does not fit that pattern.

> **Warning:** Two mistakes here look identical from the outside — a fatal
> from the database driver naming a table you have never heard of.
> Forgetting `$namespace` gives `#__reservations`. An irregular plural the
> inflector does not agree with — a class `Equipment`, a class `Analysis` —
> gives something you did not write in the migration. Whichever it is, the
> table in the migration and the table the model derives have to be the same
> string; when in doubt set `$table` and stop guessing.

### Validation

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

Checking the return value is not optional. `save()` does not throw on a failed
rule; ignore the `false` and the task redirects with a success message over a
record that was never written.

`$always` and `$initiate` are filled by convention: a field named `alias` in
either list looks for an `automaticAlias($data)` method on the model, `created`
for `automaticCreated()`, and so on. `com_kb`'s `Article` defines
`automaticAlias()` to slugify the title, so the alias is maintained without a
controller ever setting it. `com_bookings` uses the same hook for a
reservation's `created` and `created_by`.

`setup()` runs after construction. `com_kb` uses it to merge the article's own
`params` over the component's, giving `$article->params` as one registry.

## Relationships

Relationships are methods that return a relation object:

<!--include: core/components/com_kb/models/article.php:247-266-->

`belongsToOne()`, `oneToMany()`, `manyToMany()`, `oneShiftsToMany()`, and
`oneToManyThrough()` are the ones in common use. For `com_bookings`, an
`Instrument` has many `Reservation`s and a `Reservation` belongs to one
`Instrument`:

```php
public function reservations()
{
	return $this->oneToMany('Reservation', 'instrument_id');
}
```

Call the method to get the related rows,
`$instrument->reservations()->rows()`, or read it as a property,
`$reservation->instrument->get('title')`, and the query runs lazily.

Lazily is the word to watch. Reading a relation as a property inside a loop
over a list runs one query per row, and the page is slow for reasons the code
does not show. See the [ORM](../06-database.md#orm) chapter for eager loading.

## Querying

`Relational` is also the query builder. Static calls start a query and instance
calls chain onto it:

```php
$rows = Reservation::all()
	->whereEquals('instrument_id', $id)
	->whereEquals('state', Reservation::STATE_PUBLISHED)
	->ordered('filter_order', 'filter_order_Dir')
	->paginated('limitstart', 'limit')
	->rows();

$one = Reservation::oneOrNew($id);       // existing row, or a blank one
$one = Reservation::oneOrFail($id);      // or throw
```

`ordered()` and `paginated()` take the *names* of request variables, read the
sort column, direction, and page from the request, and remember them in model
state — which is why an administrator list controller passes
`'filter_order'` and `'limitstart'` rather than values. Passing a column name
straight into `ordered()` sorts by whatever request variable happens to carry
that name, which is usually none, so the list comes back in the default order
and nothing complains. Full details are in the
[ORM](../06-database.md#orm) chapter.

## Events a save fires

`save()` triggers two events, and they are the hook a hub uses to react to a
component's records without editing the component:

```php
if ($this->isNew())
{
	\Event::trigger($this->getTableName() . '_new', ['model' => $this]);
}

\Event::trigger('system.onContentSave', array($this->getTableName(), $this));
```

`system.onContentSave` carries a group — the part before the dot — so the
dispatcher loads the `system` plugin group before firing it. That is how
`plg_system_content` gets a chance to index the record.

> **Warning:** `{table}_new` carries **no** group, because the table name has
> no dot in it. The dispatcher therefore loads nothing, and the event reaches
> only listeners some other code has already imported this request. A plugin
> that answers `#__bookings_reservations_new` will fire on some pages and not
> others depending on what else ran first. Do not build on it.

## Firing your own event

To let a hub react to a reservation reliably, trigger an event yourself, with
a group of your own:

```php
Event::trigger('bookings.onReservationCreate', array($reservation));
```

The dispatcher splits on the dot, loads every enabled plugin whose
`#__extensions` row has `folder = 'bookings'`, and calls
`onReservationCreate($reservation)` on each — the arguments array is spread
onto the method positionally. Nothing else has to be declared; the group is
whatever you name.

A plugin answering it is one class in
`app/plugins/bookings/notify/notify.php` with its own migration. See
[Events](../03-foundation/04-events.md), [Plugins](../10-plugins/README.md) and
the [events reference](../../reference/events/README.md).

The failure here is quiet in the usual way: a plugin whose directory exists
but whose `#__extensions` row does not is never loaded, so the event fires,
nothing answers, and `Event::trigger()` returns an empty array.

## Plain model classes

Not everything that lives in `models/` is a `Relational`. `com_kb`'s
[`Archive`](../../../core/components/com_kb/models/archive.php) is an ordinary
class that assembles cross-model queries — the category list with article
counts, the most popular articles — and has no table of its own. Controllers
build one in `execute()` and hand it to the view.

That is the right shape whenever the thing you are modelling is a view over
several tables rather than a row in one: for `com_bookings`, "the calendar for
this week" is an `Archive`, not a record.

## The older base class

`Hubzero\Base\Model` predates the ORM and is still present; older components
extend it. It wraps a `Hubzero\Database\Table` object, exposes state constants,
and offers `get()`/`set()` through `Hubzero\Base\Obj`. Do not use it for new
work — `Relational` does the same job with far less code, and new tables should
be modelled with it. If you are reading a component that uses it, you are
reading inherited code, not a pattern to copy.

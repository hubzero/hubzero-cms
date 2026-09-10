<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/components/configuration
-->
# Configuration

A component has two XML files in its `config` directory. `config.xml` declares
the settings an administrator can change; `access.xml` declares the things a
group can be allowed to do. Neither is required, and a component with no
settings and no permissions of its own needs neither.

```
app/components/com_bookings/
    config/
        config.xml
        access.xml
```

Declare a parameter when a hub will reasonably want a different value from
yours — how far ahead `com_bookings` lets people book, whether a reservation
needs approval. Do not declare one for every constant in the component: each
is a screen an administrator has to understand, and a value your code has to
keep working for.

Permissions are the other half. Anything a lab manager may do and a user may
not is an action in `access.xml`, checked with `User::authorise()`. A
component that decides by checking `User::get('username')` against a list has
made a rule no hub can change.

## `config.xml`

The root element is `<config>`, with `<fieldset>` children directly inside it
— not wrapped in a `<fields>` element the way a module or plugin manifest
wraps them. Each `<fieldset>` becomes a tab on the component's Options screen,
and each `<field>` becomes a control. `com_kb`'s is short enough to read
whole:

<!--include: core/components/com_kb/config/config.xml:9-41-->

`label` and `description` are language keys, resolved from the component's
**administrator** language file — the Options screen runs in the administrator
client. Write them as keys, not as English; a hub running in another language
sees whatever you put there. A key defined only in `site/language/` renders on
the Options screen as itself. See [Languages](06-languages.md).

Field `type` names a class in `core/libraries/Hubzero/Form/Fields` —
`text`, `textarea`, `radio`, `checkboxes`, `number`, `password`, `filelist`,
`folderlist`, `accesslevel`, `usergroup`, `editor`, `tags`, and about thirty
more. `list` is the exception: PHP will not allow a class called `List`, so the
form aliases it to `Select`. A `list` carries `<option>` children whose bodies
are language keys too.

> **Note:** A `type` the form cannot resolve falls back to `text` silently.
> [`Form::loadField()`](../../../core/libraries/Hubzero/Form/Form.php) calls
> `loadFieldType('text')` when the named class is not found, so a misspelled
> type shows up as a plain text box, not as an error — and an administrator
> then types free text into what should have been a drop-down. Check the name
> against `core/libraries/Hubzero/Form/Fields`; the full list is in
> [Parameters](../07-extensions/02-parameters.md#field-types).

The last fieldset is the same in every component that has permissions:

```xml
<fieldset name="permissions" label="JCONFIG_PERMISSIONS_LABEL" description="JCONFIG_PERMISSIONS_DESC">
    <field name="rules" type="rules" label="JCONFIG_PERMISSIONS_LABEL"
           class="inputbox" validate="rules" filter="rules"
           component="com_bookings" section="component" />
</fieldset>
```

The `rules` field reads `access.xml` for the component named in its
`component` attribute and renders the permission grid. Change the component
name when you copy this block — leave `com_kb` in it and the administrator
edits the knowledge base's permissions from your Options screen, which is a
bug nobody notices until the wrong people can do the wrong thing.

`com_config` reads `config.xml` from `{component path}/config/config.xml`, and
the administrator reaches the form from the toolbar:

```php
Toolbar::preferences($this->option, '550');
```

## `access.xml`

Actions are grouped into sections. The `component` section applies to the
component as a whole; further sections apply to individual records.

<!--include: core/components/com_kb/config/access.xml:9-33-->

`title` and `description` are language keys again; the `JACTION_*` and
`COM_CATEGORIES_ACCESS_*` ones shown here are defined by the platform, so a
component that only needs the standard actions writes no new strings.

The action names are conventions the platform relies on:

| Action | Means |
|---|---|
| `core.admin` | may change this component's permissions |
| `core.manage` | may open this component in the administrator |
| `core.create` | may add a record |
| `core.edit` | may edit any record |
| `core.edit.own` | may edit a record they created |
| `core.edit.state` | may publish, unpublish, archive, trash |
| `core.delete` | may delete a record |

Check them with `User::authorise()`, against the component or against one
record:

```php
User::authorise('core.manage', 'com_bookings');             // the component
User::authorise('core.edit', 'com_bookings.instrument.42'); // one instrument
```

The component-level asset those checks hang off is created by
`addComponentEntry()` in the migration. Without it there is no row to consult
and no permissions to edit — see [Migrations](01-migrations.md). Per-record
assets are a component's own business; most components only check at
component level and keep record ownership in a `created_by` column.

`com_kb` wraps the component-level checks in a helper so its views can ask
once and reuse the answer — see [Helpers](04-helpers.md).

## Reading settings back

`Component::params()` returns a `Hubzero\Config\Registry`:

```php
$params = Component::params('com_bookings');

echo $params->get('max_days_ahead', 30);
```

Inside a controller the same registry is already on hand as `$this->config`,
set by the constructor.

> **Warning:** The registry holds only what is stored in the component's
> `#__extensions` row. The `default` attributes in `config.xml` are used to
> populate the *form*, not the registry, so until an administrator opens
> Options and saves, every setting is absent and `get()` returns its second
> argument — or `null` if you did not pass one. On a freshly installed
> component that means `(int) $params->get('max_days_ahead')` is `0`, and a
> loop bounded by it does nothing at all. Always pass a default in code, and
> make it the same value as the one in `config.xml`. A migration can seed the
> column instead, with the `saveParams` macro.

## Per-record settings

A record may carry its own parameters in a `params` column, overriding the
component's for that record alone. `com_kb`'s `Article` merges them in
`setup()`, so a view reads one registry and never has to know which level a
value came from:

```php
$params = new Registry($this->get('params'));

$this->params = Component::params('com_bookings');
$this->params->merge($params);
```

Merge in that direction — component first, record over the top — or a record
with an empty `params` column wipes out the component's settings for that
record.

## The manifest `<params>` block

`kb.xml` and other component manifests still carry a `<params>` element
listing settings in an older format. It is inherited, and it is not what the
Options screen reads — that is `config.xml`. A setting added to `<params>`
never appears anywhere. `com_kb`'s administrator edit form does still parse the
manifest, through `Hubzero\Html\Parameter`, to render the per-article
parameter controls. Do not add one to a new component; put the settings in
`config.xml` and the per-record form fields in `models/forms/`.

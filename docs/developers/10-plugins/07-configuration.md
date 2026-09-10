<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/plugins/configuration
-->
# Configuration

Parameters are how a plugin asks the hub's administrator a question it cannot
answer itself. `plg_bookings_notify` cannot know which address the lab's
manager reads, and hard-coding it would make the plugin useful on exactly one
hub. So it declares a `manager_email` field, the administrator fills it in
from the Plugin Manager, and the plugin reads it back.

The route is: declared in the XML manifest, edited in the Plugin Manager,
stored as JSON in the `params` column of the plugin's `#__extensions` row, and
read back as a `Hubzero\Config\Registry` on `$this->params`. Every step of
that lives in the row — which is one more reason a plugin without one is not
merely inert. There is nowhere for its settings to be.

## Declaring fields

The manifest's `<config>` element holds one `<fields name="params">`
containing one or more `<fieldset>`s. Each `<field>` is one parameter:

```xml
<config>
	<fields name="params">
		<fieldset name="basic">
			<field name="manager_email" type="text" size="40" default=""
				label="PLG_BOOKINGS_NOTIFY_MANAGER_EMAIL"
				description="PLG_BOOKINGS_NOTIFY_MANAGER_EMAIL_DESC" />
			<field name="include_notes" type="radio" default="0"
				label="PLG_BOOKINGS_NOTIFY_INCLUDE_NOTES"
				description="PLG_BOOKINGS_NOTIFY_INCLUDE_NOTES_DESC">
				<option value="0">JNO</option>
				<option value="1">JYES</option>
			</field>
		</fieldset>
	</fields>
</config>
```

The shipped `plg_content_formatwiki` is the same shape:

<!--include: core/plugins/content/formatwiki/formatwiki.xml:20-33-->

| Attribute | Meaning |
|---|---|
| `name` | The key you read back. |
| `type` | The form control: `text`, `list`, `radio`, `textarea`, `spacer`, and the rest of the shipped field types. |
| `default` | The value the form shows for a new plugin. |
| `label`, `description` | Displayed text. Normally language keys, resolved from the plugin's `.ini`; a literal string is also accepted and several core plugins use one. |

`<field type="spacer" />` inserts a gap and takes no value. See
[Parameters](../07-extensions/02-parameters.md) for the full field type list,
which is shared with components and modules.

> **Warning:** Each fieldset becomes a collapsible panel, and the panel's
> heading is `$fieldSet->label` if the fieldset declares one and
> `COM_PLUGINS_{NAME}_FIELDSET_LABEL` otherwise. Only `basic` and `advanced`
> have that string defined. Name a fieldset anything else without a `label`
> attribute — `<fieldset name="mail">` — and the administrator sees the literal
> text `COM_PLUGINS_MAIL_FIELDSET_LABEL` above your fields. A dozen shipped
> plugins do this, `plg_groups_forum` among them. Either use `basic`, or give
> the fieldset a `label` of your own.

## Reading parameters in the plugin

Every plugin instance has its parameters ready before any event fires: the
constructor takes them from the `#__extensions` row and wraps them in a
`Registry`.

```php
public function onReservationCreate($reservation)
{
	if (!($to = $this->params->get('manager_email')))
	{
		return;
	}

	$notes = $this->params->get('include_notes', 0);
	// ...
}
```

Always read through `get()` with a default, and check the value before using
it. `default` in the manifest is what the *form* shows; it is not written to
the row until somebody saves the form, and a plugin registered by a migration
before anyone opened that form has no value for the field at all. So does a
plugin registered before you added the field in a later release:

```php
$limit = (int) $this->params->get('display_limit', 50);
```

A missing parameter therefore reads as `null`, not as the manifest default,
and a plugin that trusts the manifest fails on exactly the hubs that never
touched its settings — which is most of them.

## Reading another plugin's parameters

The `Plugin` facade fronts
[`Hubzero\Plugin\Loader`](../../../core/libraries/Hubzero/Plugin/Loader.php), and
`params()` takes the group and the plugin name, in that order, returning a
`Registry`:

```php
$params = Plugin::params('bookings', 'notify');

echo $params->get('manager_email');
```

`Plugin::byType('authentication')` returns the raw rows for a whole group,
each with a `params` string on it — which is what `mod_login` uses to find
each authentication provider's `display_name`:

```php
foreach (Plugin::byType('authentication') as $p)
{
	$pparams = new Registry($p->params);
	$display = $pparams->get('display_name', ucfirst($p->name));
}
```

`Hubzero\Plugin\Plugin::getParams($name, $folder)` is a static shortcut that
queries the table directly.

> **Warning:** Two things about it. Its arguments are the other way round from
> `Plugin::params()` — name first, folder second — and the two calls read the
> same as each other, so a swap does not look wrong. And its query carries
> `AND enabled=1`, so a disabled plugin's parameters come back as an empty
> `Registry` rather than as the stored values. Both failures produce defaults
> where you expected settings, and neither raises anything.

## Per-object parameters

Some plugins need different settings for each group, project, or member they
run for, not one setting for the whole site. That is what
[`Hubzero\Plugin\Params`](../../../core/libraries/Hubzero/Plugin/Params.php)
provides, backed by the `#__plugin_params` table with `object_id`, `folder`,
and `element` columns.

```php
$this->params = \Hubzero\Plugin\Params::getParams(
	$member->get('id'),
	'members',
	$this->_name
);
```

`getParams($oid, $folder, $element)` reads the plugin's global parameters,
merges the per-object row on top, and returns the result — so an object that
has never been customised falls back to the site-wide settings.
`getCustomParams()` returns only the per-object values and
`getDefaultParams()` only the global ones.

`plgMembersBlog` does exactly this, then sets a few runtime-only values on the
same Registry to carry authorisation decisions into its views:

```php
if ($user->get('id') == $member->get('id'))
{
	$this->params->set('access-edit-comment', true);
	$this->params->set('access-delete-comment', true);
}
```

> **Note:** Overwriting `$this->params` like this replaces the plugin's
> configuration for the rest of the request. It is the established pattern in
> the `groups`, `members`, and `projects` groups, but it means a later method
> on the same instance sees the per-object merge, not the global settings.
> Plugin instances live for the whole request and are shared across every
> trigger in it.

## Changing parameters from a migration

Use `savePluginParams($folder, $element, $params)`, reading the current values
first with `getParams('plg_group_name')` because the macro replaces the column
rather than merging into it. See [Migrations](01-migrations.md#shipping-default-parameters).

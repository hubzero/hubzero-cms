<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/plugins/configuration
-->
# Configuration

A plugin's parameters are declared in its XML manifest, edited by an
administrator in the Plugin Manager, stored as JSON in the `params` column of
its `#__extensions` row, and read back as a `Hubzero\Config\Registry`.

## Declaring fields

The manifest's `<config>` element holds one `<fields name="params">`
containing one or more `<fieldset>`s. Each fieldset becomes a tab on the edit
form; each `<field>` is one parameter:

<!--include: core/plugins/content/formatwiki/formatwiki.xml:20-33-->

| Attribute | Meaning |
|---|---|
| `name` | The key you read back. |
| `type` | The form control: `text`, `list`, `radio`, `textarea`, `spacer`, and the rest of the shipped field types. |
| `default` | The value the form shows for a new plugin. |
| `label`, `description` | Displayed text. Normally language keys, resolved from the plugin's `.ini`; a literal string is also accepted and several core plugins use one. |

`<field type="spacer" />` inserts a gap and takes no value.

A plugin with several groups of settings splits them across fieldsets, as
`plg_groups_forum` does with a `basic` fieldset for access and display and a
`forum` fieldset for threading behaviour.

## Reading parameters in the plugin

Every plugin instance has its parameters ready before any event fires: the
constructor takes them from the `#__extensions` row and wraps them in a
`Registry`.

```php
class plgContentFormatwiki extends \Hubzero\Plugin\Plugin
{
    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        if ($this->params->get('convertFormat'))
        {
            // ...
        }
    }
}
```

Always read through `get()` with a default. A plugin registered before you
added a field has no value for it, and `get('newfield')` returns null:

```php
$limit = (int) $this->params->get('display_limit', 50);
```

## Reading another plugin's parameters

The `Plugin` facade fronts
[`Hubzero\Plugin\Loader`](../../../core/libraries/Hubzero/Plugin/Loader.php), and
`params()` takes the group and the plugin name, in that order, returning a
`Registry`:

```php
$params = Plugin::params('content', 'formatwiki');

echo $params->get('convertFormat');
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
queries the table directly. Note that its arguments are the other way round
from `Plugin::params()`: name first, folder second.

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

`getParams()` reads the plugin's global parameters, merges the per-object row
on top, and returns the result — so an object that has never been customised
falls back to the site-wide settings. `getCustomParams()` returns only the
per-object values and `getDefaultParams()` only the global ones.

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

## Changing parameters from a migration

Use `savePluginParams($folder, $element, $params)`, reading the current values
first with `getParams('plg_group_name')` so you merge rather than replace. See
[Migrations](01-migrations.md).

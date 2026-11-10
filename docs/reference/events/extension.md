<!--
status: generated
source: Event::trigger('extension.*') call sites and core/plugins/extension/
-->

# Extension events

Events in the `extension` group. A plugin in `core/plugins/extension/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `extension.onExtensionAfterDelete`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:769`](../../../core/components/com_modules/admin/controllers/modules.php#L769) with `['com_modules.module', $model->getTableName()]`

No plugin in the source tree listens for this event.

## `extension.onExtensionAfterSave`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:435`](../../../core/components/com_modules/admin/controllers/modules.php#L435) with `[$this->option . '.module', &$model, $model->isNew()]`
- [`core/components/com_templates/admin/controllers/styles.php:328`](../../../core/components/com_templates/admin/controllers/styles.php#L328) with `['com_templates.style', $style, ($fields['id'] ? false : true)],`
- [`core/components/com_templates/models/file.php:154`](../../../core/components/com_templates/models/file.php#L154) with `['com_templates.source', &$this, false]`
- [`core/components/com_templates/models/source.php:150`](../../../core/components/com_templates/models/source.php#L150) with `['com_templates.source', &$this, false]`

No plugin in the source tree listens for this event.

## `extension.onExtensionBeforeDelete`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:760`](../../../core/components/com_modules/admin/controllers/modules.php#L760) with `['com_modules.module', $model->getTableName()]`

No plugin in the source tree listens for this event.

## `extension.onExtensionBeforeSave`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:411`](../../../core/components/com_modules/admin/controllers/modules.php#L411) with `$eventArgs`
- [`core/components/com_templates/admin/controllers/styles.php:264`](../../../core/components/com_templates/admin/controllers/styles.php#L264) with `['com_templates.style', $style, ($fields['id'] ? false : true)],`
- [`core/components/com_templates/models/file.php:132`](../../../core/components/com_templates/models/file.php#L132) with `['com_templates.source', &$data, false]`
- [`core/components/com_templates/models/source.php:128`](../../../core/components/com_templates/models/source.php#L128) with `['com_templates.source', &$data, false]`

No plugin in the source tree listens for this event.

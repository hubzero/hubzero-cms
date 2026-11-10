<!--
status: generated
source: Event::trigger('extension.*') call sites and core/plugins/extension/
-->

# Extension events

Events in the `extension` group. A plugin in `core/plugins/extension/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `extension.onExtensionAfterDelete`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:765`](../../../core/components/com_modules/admin/controllers/modules.php#L765) with `['com_modules.module', $model->getTableName()]`

No plugin in the source tree listens for this event.

## `extension.onExtensionAfterSave`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:432`](../../../core/components/com_modules/admin/controllers/modules.php#L432) with `[$this->option . '.module', &$model, $model->isNew()]`
- [`core/components/com_templates/admin/controllers/styles.php:325`](../../../core/components/com_templates/admin/controllers/styles.php#L325) with `['com_templates.style', $style, ($fields['id'] ? false : true)],`
- [`core/components/com_templates/models/file.php:153`](../../../core/components/com_templates/models/file.php#L153) with `['com_templates.source', &$this, false]`
- [`core/components/com_templates/models/source.php:149`](../../../core/components/com_templates/models/source.php#L149) with `['com_templates.source', &$this, false]`

No plugin in the source tree listens for this event.

## `extension.onExtensionBeforeDelete`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:756`](../../../core/components/com_modules/admin/controllers/modules.php#L756) with `['com_modules.module', $model->getTableName()]`

No plugin in the source tree listens for this event.

## `extension.onExtensionBeforeSave`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:408`](../../../core/components/com_modules/admin/controllers/modules.php#L408) with `$eventArgs`
- [`core/components/com_templates/admin/controllers/styles.php:263`](../../../core/components/com_templates/admin/controllers/styles.php#L263) with `['com_templates.style', $style, ($fields['id'] ? false : true)],`
- [`core/components/com_templates/models/file.php:131`](../../../core/components/com_templates/models/file.php#L131) with `['com_templates.source', &$data, false]`
- [`core/components/com_templates/models/source.php:127`](../../../core/components/com_templates/models/source.php#L127) with `['com_templates.source', &$data, false]`

No plugin in the source tree listens for this event.

<!--
status: generated
source: Event::trigger('extension.*') call sites and core/plugins/extension/
-->

# Extension events

Events in the `extension` group. A plugin in `core/plugins/extension/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `extension.onExtensionAfterDelete`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:777`](../../../core/components/com_modules/admin/controllers/modules.php#L777) with `['com_modules.module', $model->getTableName()]`

No plugin in the source tree listens for this event.

## `extension.onExtensionAfterSave`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:443`](../../../core/components/com_modules/admin/controllers/modules.php#L443) with `[$this->option . '.module', &$model, $model->isNew()]`
- [`core/components/com_templates/admin/controllers/styles.php:334`](../../../core/components/com_templates/admin/controllers/styles.php#L334) with `['com_templates.style', $style, ($fields['id'] ? false : true)]`
- [`core/components/com_templates/models/file.php:149`](../../../core/components/com_templates/models/file.php#L149) with `['com_templates.source', &$this, false]`
- [`core/components/com_templates/models/source.php:148`](../../../core/components/com_templates/models/source.php#L148) with `['com_templates.source', &$this, false]`

No plugin in the source tree listens for this event.

## `extension.onExtensionBeforeDelete`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:768`](../../../core/components/com_modules/admin/controllers/modules.php#L768) with `['com_modules.module', $model->getTableName()]`

No plugin in the source tree listens for this event.

## `extension.onExtensionBeforeSave`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:413`](../../../core/components/com_modules/admin/controllers/modules.php#L413) with `$eventArgs`
- [`core/components/com_templates/admin/controllers/styles.php:265`](../../../core/components/com_templates/admin/controllers/styles.php#L265) with `['com_templates.style', $style, ($fields['id'] ? false : true)]`
- [`core/components/com_templates/models/file.php:127`](../../../core/components/com_templates/models/file.php#L127) with `['com_templates.source', &$data, false]`
- [`core/components/com_templates/models/source.php:126`](../../../core/components/com_templates/models/source.php#L126) with `['com_templates.source', &$data, false]`

No plugin in the source tree listens for this event.

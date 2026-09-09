<!--
status: generated
source: Event::trigger('extension.*') call sites and core/plugins/extension/
-->

# Extension events

Events in the `extension` group. A plugin in `core/plugins/extension/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `extension.onExtensionAfterDelete`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:814`](../../../core/components/com_modules/admin/controllers/modules.php#L814) with `['com_modules.module', $model->getTableName()]`

No plugin in the source tree listens for this event.

## `extension.onExtensionAfterSave`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:452`](../../../core/components/com_modules/admin/controllers/modules.php#L452) with `[$this->_option . '.module', &$model, $model->isNew()]`
- [`core/components/com_templates/admin/controllers/styles.php:344`](../../../core/components/com_templates/admin/controllers/styles.php#L344) with `['com_templates.style', $style, ($fields['id'] ? false : true)]`
- [`core/components/com_templates/models/file.php:161`](../../../core/components/com_templates/models/file.php#L161) with `['com_templates.source', &$table, false]`
- [`core/components/com_templates/models/source.php:166`](../../../core/components/com_templates/models/source.php#L166) with `['com_templates.source', &$table, false]`

No plugin in the source tree listens for this event.

## `extension.onExtensionBeforeDelete`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:804`](../../../core/components/com_modules/admin/controllers/modules.php#L804) with `['com_modules.module', $model->getTableName()]`

No plugin in the source tree listens for this event.

## `extension.onExtensionBeforeSave`

Fired from:

- [`core/components/com_modules/admin/controllers/modules.php:425`](../../../core/components/com_modules/admin/controllers/modules.php#L425) with `[$this->_option . '.module', &$model, $model->isNew()]`
- [`core/components/com_templates/admin/controllers/styles.php:276`](../../../core/components/com_templates/admin/controllers/styles.php#L276) with `['com_templates.style', $style, ($fields['id'] ? false : true)]`
- [`core/components/com_templates/models/file.php:135`](../../../core/components/com_templates/models/file.php#L135) with `['com_templates.source', &$data, false]`
- [`core/components/com_templates/models/source.php:140`](../../../core/components/com_templates/models/source.php#L140) with `['com_templates.source', &$data, false]`

No plugin in the source tree listens for this event.

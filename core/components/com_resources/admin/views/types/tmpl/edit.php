<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('type');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_TYPES') . ': ' . $text, 'resources');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();

Html::behavior('framework', true);
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
$this->css();

$params = new \Hubzero\Config\Registry($this->row->get('params'));
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" id="item-form" name="adminForm" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-type"><?php echo Lang::txt('COM_RESOURCES_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="type[type]" id="field-type" maxlength="100" class="required" value="<?php echo $this->escape(stripslashes($this->row->type)); ?>" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_FIELD_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_RESOURCES_FIELD_ALIAS'); ?>:</label><br />
					<input type="text" name="type[alias]" id="field-alias" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" /><br />
					<span class="hint"><?php echo Lang::txt('COM_RESOURCES_FIELD_ALIAS_HINT'); ?></span>
				</div>
				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_RESOURCES_FIELD_CATEGORY'); ?>:</label><br />
					<?php echo \Components\Resources\Helpers\Html::selectType($this->categories, 'type[category]', $this->row->category, 'category', Lang::txt('COM_RESOURCES_SELECT'), '', '', ''); ?>
				</div>
				<div class="input-wrap">
					<label for="field-contributable"><?php echo Lang::txt('COM_RESOURCES_FIELD_CONTRIBUTABLE'); ?>:</label><br />
					<input type="checkbox" name="type[contributable]" id="field-contributable" value="1"<?php echo ($this->row->contributable) ? ' checked="checked"' : ''; ?> /> <?php echo Lang::txt('COM_RESOURCES_FIELD_CONTRIBUTABLE_EXPLANATION'); ?>
				</div>
				<div class="input-wrap">
					<label for="field-collection"><?php echo Lang::txt('COM_RESOURCES_FIELD_COLLECTION'); ?>:</label><br />
					<input type="checkbox" name="type[collection]" id="field-collection" value="1"<?php echo ($this->row->collection) ? ' checked="checked"' : ''; ?> /> <?php echo Lang::txt('COM_RESOURCES_FIELD_COLLECTION_EXPLANATION'); ?>
				</div>
				<?php if ($this->row->category != 27) { ?>
					<div class="input-wrap">
						<label for="params-linkaction"><?php echo Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION'); ?>:</label><br />
						<select name="params[linkAction]" id="params-linkaction">
							<option value="extension"<?php echo ($params->get('linkAction') == 'extension') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_BY_EXT'); ?></option>
							<option value="external"<?php echo ($params->get('linkAction') == 'external') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_NEW_WINDOW'); ?></option>
							<option value="lightbox"<?php echo ($params->get('linkAction') == 'lightbox') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_LIGHTBOX'); ?></option>
							<option value="download"<?php echo ($params->get('linkAction') == 'download') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_DOWNLOAD'); ?></option>
						</select>
					</div>
					<div class="input-wrap">
						<label for="param-restrict_direct_access"><?php echo Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_HINT'); ?>:</label><br />
						<select name="params[restrict_direct_access]" id="param-link_action">
							<option value="0"<?php if (!$params->get('restrict_direct_access')) { echo ' selected="selected"'; } ?>>
								<?php echo Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_DEFAULT'); ?></option>
							<option value="1"<?php if ($params->get('restrict_direct_access') == 1) { echo ' selected="selected"'; } ?>>
								<?php echo Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_NO'); ?>
							</option>
							<option value="2"<?php if ($params->get('restrict_direct_access') == 2) { echo ' selected="selected"'; } ?>>
								<?php echo Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_YES'); ?>
							</option>
						</select>
					</div>
				<?php } ?>
				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_RESOURCES_FIELD_STATE'); ?>:</label><br />
					<select name="type[state]" id="field-state">
						<option value="0"<?php if ($this->row->state == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php if ($this->row->state == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					</select>
				</div>
				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_RESOURCES_FIELD_DESCIPTION'); ?>:</label><br />
					<?php echo $this->editor('type[description]', stripslashes($this->row->description), 45, 10, 'field-description', array('class' => 'minimal')); ?>
				</div>

				<input type="hidden" name="type[id]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />
			</fieldset>
		</div>
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_RESOURCES_FIELDSET_PLUGINS'); ?></span></legend>

				<table class="admintable">
					<thead>
						<tr>
							<th scope="col"><?php echo Lang::txt('COM_RESOURCES_COL_PLUGIN'); ?></th>
							<th scope="col" colspan="2"><?php echo Lang::txt('COM_RESOURCES_COL_ACTIVE'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					$database = App::get('db');
					$database->setQuery("SELECT * FROM `#__extensions` WHERE `type`='plugin' AND `folder`='resources' AND `enabled`=1");
					$plugins = $database->loadObjectList();

					$found = array();
					$lang = Lang::getRoot();
					foreach ($plugins as $plugin)
					{
						if (in_array('plg_' . $plugin->element, $found))
						{
							continue;
						}
						$path = Plugin::path($plugin->folder, $plugin->element);

						$found[] = 'plg_' . $plugin->element;
						if (strstr($plugin->name, '_'))
						{
							$lang->load($plugin->name . '.sys') ||
							$lang->load($plugin->name . '.sys', PATH_APP . '/plugins/' . $plugin->folder . '/' . $plugin->element) ||
							$lang->load($plugin->name . '.sys', PATH_CORE . '/plugins/' . $plugin->folder . '/' . $plugin->element);
						}
						?>
						<tr>
							<th scope="row"><?php echo strstr($plugin->name, '_') ? Lang::txt(stripslashes($plugin->name)) : stripslashes($plugin->name); ?></th>
							<td><label><input type="radio" name="params[plg_<?php echo $plugin->element; ?>]" value="0"<?php echo ($params->get('plg_' . $plugin->element, 0) == 0) ? ' checked="checked"':''; ?> /> <?php echo Lang::txt('COM_RESOURCES_OFF'); ?></label></td>
							<td><label><input type="radio" name="params[plg_<?php echo $plugin->element; ?>]" value="1"<?php echo ($params->get('plg_' . $plugin->element, 0) == 1) ? ' checked="checked"':''; ?> /> <?php echo Lang::txt('COM_RESOURCES_ON'); ?></label></td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
			</fieldset>
		</div>
	</div>

	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_RESOURCES_TYPES_CUSTOM_FIELDS'); ?></span></legend>

		<table class="admintable" id="fields" data-href="<?php echo Route::url('index.php?option=com_resources&controller=types&no_html=1&task=element&ctrl=fields'); ?>">
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('COM_RESOURCES_TYPES_REORDER'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_RESOURCES_TYPES_FIELD'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_RESOURCES_TYPES_TYPE'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_RESOURCES_TYPES_REQUIRED'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_RESOURCES_TYPES_OPTIONS'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="5">
						<a class="button" id="add-custom-field" href="#addRow">
							<span><?php echo Lang::txt('COM_RESOURCES_NEW_ROW'); ?></span>
						</a>
					</td>
				</tr>
			</tfoot>
			<tbody id="field-items">
			<?php
			include_once Component::path('com_resources') . DS . 'models' . DS . 'elements.php';
			$elements = new \Components\Resources\Models\Elements('', $this->row->customFields);
			$schema = $elements->getSchema();

			if (!is_object($schema))
			{
				$schema = new stdClass();
				$schema->fields = array();
			}

			if (count($schema->fields) <= 0)
			{
				$fs = explode(',', $this->config->get('tagsothr', 'bio,credits,citations,sponsoredby,references,publications'));
				foreach ($fs as $f)
				{
					$f = trim($f);
					$element = new stdClass();
					$element->name        = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($f));
					$element->label       = ucfirst($f);
					$element->type        = 'text';
					$element->required    = '';
					$element->value       = '';
					$element->default     = '';
					$element->description = '';

					$schema->fields[] = $element;
				}
			}

			$i = 0;
			foreach ($schema->fields as $field)
			{
				?>
				<tr>
					<td class="order">
						<span class="handle hasTip" title="<?php echo Lang::txt('COM_RESOURCES_MOVE_HANDLE'); ?>">
							<?php echo Lang::txt('COM_RESOURCES_MOVE_HANDLE'); ?>
						</span>
					</td>
					<td>
						<input type="text" name="fields[<?php echo $i; ?>][title]" value="<?php echo $this->escape(stripslashes($field->label)); ?>" maxlength="255" />
						<input type="hidden" name="fields[<?php echo $i; ?>][name]" value="<?php echo $this->escape(stripslashes($field->name)); ?>" />
					</td>
					<td>
						<select name="fields[<?php echo $i; ?>][type]" id="fields-<?php echo $i; ?>-type">
							<optgroup label="<?php echo Lang::txt('COM_RESOURCES_FIELD_COMMON'); ?>">
								<option value="text"<?php echo ($field->type == 'text') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_RESOURCES_TYPES_TEXT'); ?></option>
								<option value="textarea"<?php echo ($field->type == 'textarea') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_RESOURCES_TYPES_TEXTAREA'); ?></option>
								<option value="list"<?php echo ($field->type == 'list') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_RESOURCES_TYPES_LIST'); ?></option>
								<option value="radio"<?php echo ($field->type == 'radio') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_RESOURCES_TYPES_RADIO'); ?></option>
								<option value="checkbox"<?php echo ($field->type == 'checkbox') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_RESOURCES_TYPES_CHECKBOX'); ?></option>
								<option value="hidden"<?php echo ($field->type == 'hidden') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_RESOURCES_TYPES_HIDDEN'); ?></option>
							</optgroup>
							<optgroup label="<?php echo Lang::txt('COM_RESOURCES_FIELD_PREDEFINED'); ?>">
								<option value="date"<?php echo ($field->type == 'date') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_RESOURCES_FIELD_PREDEFINED_DATE'); ?></option>
								<option value="geo"<?php echo ($field->type == 'geo') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_RESOURCES_FIELD_PREDEFINED_GEO'); ?></option>
								<option value="languages"<?php echo ($field->type == 'languages') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_RESOURCES_FIELD_PREDEFINED_LANG'); ?></option>
							</optgroup>
						</select>
					</td>
					<td>
						<input type="checkbox" name="fields[<?php echo $i; ?>][required]" value="1"<?php echo ($field->required) ? ' checked="checked"':''; ?> />
					</td>
					<td id="fields-<?php echo $i; ?>-options">
						<?php echo $elements->getElementOptions($i, $field, 'fields'); ?>
					</td>
				</tr>
				<?php
				$i++;
			}
			?>
			</tbody>
		</table>
	</fieldset>

	<?php echo Html::input('token'); ?>
</form>

<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$canDo = \Components\Publications\Helpers\Permissions::getActions('category');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATION_CATEGORY') . ': ' . $text, 'category');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}
Toolbar::cancel();

$dcTypes = array(
	'Collection' , 'Dataset' , 'Event' , 'Image' ,
	'InteractivePublication' , 'MovingImage' , 'PhysicalObject' ,
	'Service' , 'Software' , 'Sound' , 'StillImage' , 'Text'
);

$params = $this->row->params;

Html::behavior('framework', true);

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="item-form" name="adminForm">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_CATEGORY_INFORMATION'); ?></span></legend>
				<?php if ($this->row->id) { ?>
					<table>
						<tbody>
							<tr>
								<th><?php echo Lang::txt('ID'); ?></th>
								<td><?php echo $this->row->id; ?></td>
							</tr>
						</tbody>
					</table>
				<?php } ?>
				<div class="input-wrap">
					<label for="field-name"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_NAME'); ?>:<span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="prop[name]" id="field-name" maxlength="100" value="<?php echo $this->escape($this->row->name); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ALIAS'); ?>:</label>
					<input type="text" name="prop[alias]" id="field-alias" maxlength="100" value="<?php echo $this->escape($this->row->alias); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-url_alias"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_URL_ALIAS'); ?>:</label>
					<input type="text" name="prop[url_alias]" id="field-url_alias" maxlength="100" value="<?php echo $this->escape($this->row->url_alias); ?>" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DUBLIN_CORE'); ?>">
					<label for="field-dc_type"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DC_TYPE'); ?>:</label>
					<select name="prop[dc_type]" id="field-dc_type">
						<?php foreach ($dcTypes as $dct) { ?>
						<option value="<?php echo $dct; ?>" <?php if ($this->escape($this->row->dc_type) == $dct) { echo 'selected="selected"'; } ?>><?php echo $dct; ?></option>
						<?php } ?>
					</select>
					<span class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DUBLIN_CORE'); ?></span>
				</div>
				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ABOUT'); ?>:</label>
					<input type="text" name="prop[description]" id="field-description" size="55" maxlength="255" value="<?php echo $this->escape($this->row->description); ?>" />
				</div>

				<input type="hidden" name="prop[id]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />
			</fieldset>
		</div>
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ITEM_CONFIG'); ?></span></legend>

				<fieldset>
					<legend><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_STATUS'); ?></legend>

					<div class="input-wrap">
						<input class="option" name="prop[state]" id="field-state1" type="radio" value="1" <?php echo $this->row->state == 1 ? 'checked="checked"' : ''; ?> />
						<label for="field-state1"><?php echo Lang::txt('COM_PUBLICATIONS_STATUS_ACTIVE'); ?></label>
						<br />
						<input class="option" name="prop[state]" id="field-state0" type="radio" value="0" <?php echo $this->row->state != 1 ? 'checked="checked"' : ''; ?> />
						<label for="field-state0"><?php echo Lang::txt('COM_PUBLICATIONS_STATUS_INACTIVE'); ?></label>
					</div>
				</fieldset>
				<fieldset>
					<legend><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE'); ?></legend>
					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE_HINT'); ?>">
						<span class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE_HINT'); ?></span>

						<input class="option" name="prop[contributable]" id="field-contributable1" type="radio" value="1" <?php echo ($this->row->isContributable()) ? 'checked="checked"' : ''; ?> />
						<label for="field-contributable1"><?php echo Lang::txt('JYES'); ?></label>
						<br />
						<input class="option" name="prop[contributable]" id="field-contributable0" type="radio" value="0" <?php echo (!$this->row->isContributable()) ? 'checked="checked"' : ''; ?> />
						<label for="field-contributable0"><?php echo Lang::txt('JNO'); ?></label>
					</div>
				</fieldset>
			</fieldset>
		</div>
	</div>

	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_CATS_MASTER_TYPE_CONFIG'); ?></span></legend>

				<?php foreach ($this->types as $mt) { ?>
					<fieldset>
						<legend><?php echo $mt; ?></legend>
						<div class="input-wrap">
							<input class="option" name="params[type_<?php echo $mt; ?>]" id="field-type_<?php echo $mt; ?>1" type="radio" value="1" <?php echo ($params->get('type_'.$mt, 1) == 1) ? ' checked="checked"':''; ?> />
							<label for="field-type_<?php echo $mt; ?>1"><?php echo Lang::txt('COM_PUBLICATIONS_INCLUDE_CHOICE'); ?></label>
							<br />
							<input class="option" name="params[type_<?php echo $mt; ?>]" id="field-type_<?php echo $mt; ?>0" type="radio" value="0" <?php echo ($params->get('type_'.$mt, 1) == 0) ? ' checked="checked"':''; ?> />
							<label for="field-type_<?php echo $mt; ?>0"><?php echo Lang::txt('COM_PUBLICATIONS_NOT_APPLICABLE'); ?></label>
						</div>
					</fieldset>
				<?php } ?>
			</fieldset>
		</div>
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_PLUGINS'); ?></span></legend>

				<table class="admintable">
					<thead>
						<tr>
							<th><?php echo Lang::txt('COM_PUBLICATIONS_PLUGIN'); ?></th>
							<th colspan="2"><?php echo Lang::txt('COM_PUBLICATIONS_STATUS_ACTIVE'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					$database = App::get('db');
					$database->setQuery( "SELECT * FROM `#__extensions` WHERE `type`='plugin' AND `folder`='publications'" );

					if ($plugins = $database->loadObjectList())
					{
						$found = array();
						foreach ($plugins as $plugin)
						{
							if (in_array('plg_' . $plugin->element, $found))
							{
								continue;
							}
							$found[] = 'plg_' . $plugin->element;
							if (strstr($plugin->name, '_'))
							{
								Lang::load($plugin->name) ||
								Lang::load($plugin->name, PATH_CORE . DS . 'plugins' . DS . $plugin->folder . DS . $plugin->element);
							}
							?>
							<tr>
								<td><?php echo (strstr($plugin->name, '_')) ? Lang::txt(stripslashes($plugin->name)) : stripslashes(ucfirst($plugin->name)); ?></td>
								<td><label><input type="radio" name="params[plg_<?php echo $plugin->element; ?>]" value="0"<?php echo ($params->get('plg_'.$plugin->element, 0) == 0) ? ' checked="checked"':''; ?> /> <?php echo Lang::txt('JOFF'); ?></label></td>
								<td><label><input type="radio" name="params[plg_<?php echo $plugin->element; ?>]" value="1"<?php echo ($params->get('plg_'.$plugin->element, 0) == 1) ? ' checked="checked"':''; ?> /> <?php echo Lang::txt('JON'); ?></label></td>
							</tr>
							<?php
						}
					}
					?>
					</tbody>
				</table>
			</fieldset>
		</div>
	</div>

	<?php if (!$this->config->get('curation', 0)) { ?>
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_TYPES_CUSTOM_FIELDS'); ?></span></legend>

			<table class="admintable" id="fields" data-href="<?php echo Route::url('index.php?option=com_publications&controller=categories&no_html=1&task=element&ctrl=fields'); ?>">
				<thead>
					<tr>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_TYPES_REORDER'); ?></th>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_TYPES_FIELD'); ?></th>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_TYPES_TYPE'); ?></th>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_TYPES_REQUIRED'); ?></th>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_TYPES_OPTIONS'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="<?php echo '5';//($this->row->id) ? '5' : '4'; ?>">
							<button id="add-custom-field" href="#addRow">
								<span><?php echo Lang::txt('COM_PUBLICATIONS_ADD_ROW'); ?></span>
							</button>
						</td>
					</tr>
				</tfoot>
				<tbody id="field-items">
				<?php
				$this->js('categories.js');

				include_once Component::path('com_publications') . DS . 'models' . DS . 'elements.php';
				$elements = new \Components\Publications\Models\Elements('', $this->row->customFields);
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
						$element->name = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($f));
						$element->label = ucfirst($f);
						$element->type = 'text';
						$element->required = '';
						$element->value = '';
						$element->default = '';
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
							<span class="handle hasTip" title="<?php echo Lang::txt('COM_PUBLICATIONS_MOVE_HANDLE'); ?>">
								<?php echo Lang::txt('COM_PUBLICATIONS_MOVE_HANDLE'); ?>
							</span>
						</td>
						<td>
							<input type="text" name="fields[<?php echo $i; ?>][title]" value="<?php echo $this->escape(stripslashes($field->label)); ?>" maxlength="255" />
							<input type="hidden" name="fields[<?php echo $i; ?>][name]" value="<?php echo $this->escape(stripslashes($field->name)); ?>" />
						</td>
						<td>
							<select name="fields[<?php echo $i; ?>][type]" id="fields-<?php echo $i; ?>-type">
								<optgroup label="<?php echo Lang::txt('COM_PUBLICATIONS_COMMON'); ?>">
									<option value="text"<?php echo ($field->type == 'text') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_TYPES_TEXT'); ?></option>
									<option value="textarea"<?php echo ($field->type == 'textarea') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_TYPES_TEXTAREA'); ?></option>
									<option value="list"<?php echo ($field->type == 'list') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_TYPES_LIST'); ?></option>
									<option value="radio"<?php echo ($field->type == 'radio') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_TYPES_RADIO'); ?></option>
									<option value="checkbox"<?php echo ($field->type == 'checkbox') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_TYPES_CHECKBOX'); ?></option>
									<option value="hidden"<?php echo ($field->type == 'hidden') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_TYPES_HIDDEN'); ?></option>
								</optgroup>
								<optgroup label="<?php echo Lang::txt('COM_PUBLICATIONS_PRE_DEFINED'); ?>">
									<option value="date"<?php echo ($field->type == 'date') ? ' selected="selected"':''; ?>><?php echo Lang::txt('Date'); ?></option>
									<option value="geo"<?php echo ($field->type == 'geo') ? ' selected="selected"':''; ?>><?php echo Lang::txt('Geo Location'); ?></option>
									<option value="languages"<?php echo ($field->type == 'languages') ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_LANGUAGE_LIST'); ?></option>
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
	<?php } ?>
	<?php echo Html::input('token'); ?>
</form>

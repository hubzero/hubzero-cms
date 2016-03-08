<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('type');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_TYPES') . ': ' . $text, 'addedit.png');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();

Html::behavior('framework', true);

$this->css();

$params = new \Hubzero\Config\Registry($this->row->params);
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	<?php echo $this->editor()->save('field-description'); ?>

	// form field validation
	if ($('#field-type').val() == '') {
		alert('<?php echo Lang::txt('COM_RESOURCES_ERROR_MISSING_TITLE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" id="item-form" name="adminForm">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-type"><?php echo Lang::txt('COM_RESOURCES_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="type[type]" id="field-type" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->type)); ?>" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_FIELD_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_RESOURCES_FIELD_ALIAS'); ?>:</label><br />
					<input type="text" name="type[alias]" id="field-alias" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" /><br />
					<span class="hint"><?php echo Lang::txt('COM_RESOURCES_FIELD_ALIAS_HINT'); ?></span>
				</div>
				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_RESOURCES_FIELD_CATEGORY'); ?>:</label><br />
					<?php echo \Components\Resources\Helpers\Html::selectType($this->categories, 'type[category]', $this->row->category, Lang::txt('COM_RESOURCES_SELECT'), '', '', ''); ?>
				</div>
				<div class="input-wrap">
					<label for="field-contributable"><?php echo Lang::txt('COM_RESOURCES_FIELD_CONTRIBUTABLE'); ?>:</label><br />
					<input type="checkbox" name="type[contributable]" id="field-contributable" value="1"<?php echo ($this->row->contributable) ? ' checked="checked"' : ''; ?> /> <?php echo Lang::txt('COM_RESOURCES_FIELD_CONTRIBUTABLE_EXPLANATION'); ?>
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
				<?php } ?>
				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_RESOURCES_FIELD_DESCIPTION'); ?>:</label><br />
					<?php echo $this->editor('description', stripslashes($this->row->description), 45, 10, 'field-description', array('class' => 'minimal')); ?>
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
					foreach ($plugins as $plugin)
					{
						if (in_array('plg_' . $plugin->element, $found))
						{
							continue;
						}
						$found[] = 'plg_' . $plugin->element;
						if (strstr($plugin->name, '_'))
						{
							$lang = Lang::getRoot();
							$lang->load($plugin->name);
						}
						?>
						<tr>
							<th scope="row"><?php echo (strstr($plugin->name, '_') ? Lang::txt(stripslashes($plugin->name)) : stripslashes($plugin->name)); ?></th>
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

		<table class="admintable" id="fields">
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
			include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');
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

		<script type="text/javascript">
			if (!jq) {
				var jq = $;
			}

			var Fields = {
				isIE8: function() {
					var rv = -1,
						ua = navigator.userAgent,
						re = new RegExp("Trident\/([0-9]{1,}[\.0-9]{0,})");
					if (re.exec(ua) != null) {
						rv = parseFloat(RegExp.$1);
					}
					return (rv == 4);
				}
			}

			jQuery(document).ready(function(jq){
				var $ = jq,
					fields = $("#fields");

				if (!fields.length) {
					return;
				}

				$('#add-custom-field').on('click', function (e){
					e.preventDefault();

					if ($.uniform) {
						$.uniform.restore('select');
					}

					var id = 'fields';
					//Fields.addRow('fields');
					var tbody     = document.getElementById(id).tBodies[0],
						counter   = tbody.rows.length,
						newNode   = tbody.rows[0].cloneNode(true),
						replaceme = null;

					var newField = newNode.childNodes;
					for (var i=0;i<newField.length;i++)
					{
						var inputs = newField[i].childNodes;
						for (var k=0;k<inputs.length;k++)
						{
							var theName = inputs[k].name;
							if (theName) {
								tokens = theName.split('[');
								n = tokens[2];
								inputs[k].name = id + '[' + counter + ']['+ n;
								inputs[k].id = id + '-' + counter + '-' + n.replace(']', '');

								if (Fields.isIE8() && inputs[k].type == 'select-one') {
									inputs[k].id = id + '-' + counter + '-' + n.replace(']', '') + '-tmp';
									replaceme = id + '-' + counter + '-' + n.replace(']', '') + '-tmp';
								}
							}
							var n = id + '[' + counter + '][type]';
							var z = id + '[' + counter + '][required]';
							if (inputs[k].value && inputs[k].name != z) {
								inputs[k].value = '';
								inputs[k].selectedIndex = 0;
								inputs[k].selected = false;
							}
							if (inputs[k].checked) {
								inputs[k].checked = false;
							}
						}
						if (newField[i].id) {
							newField[i].id = 'fields-' + counter + '-options';
						}
					}

					tbody.appendChild(newNode);

					// Make a clone of the clone. Why? Because IE 8 is dumb.
					// IE still retains a reference to the original object for change events
					// So, when calling onChange, the event gets fired for the clone AND the
					// original. Cloning the clone seems to fix this.
					if (replaceme) {
						var replace = $(replaceme);
						var select  = $.clone(replace).appendAfter(replace);
						$.remove(replace);
					}

					if ($.uniform) {
						$('select').uniform();
					}

					return false;
				});

				fields
					.on('change', 'select', function (e){
						var i = $(this).attr('name').replace(/^fields\[(\d+)\]\[type\]/g, "$1");
						$.get('/administrator/index.php?option=com_resources&controller=types&no_html=1&task=element&ctrl=fields&type=' + this.value + '&name=' + i, {}, function (response) {
							$('#fields-' + i + '-options').html(response);
							//Fields.initOptions();
						});
					})
					.on('click', '.add-custom-option', function (e){
						e.preventDefault();

						var id = $(this).attr('data-rel');

						if (!id) {
							return;
						}

						var tbody = document.getElementById(id).tBodies[0],
							counter = tbody.rows.length,
							newNode = tbody.rows[0].cloneNode(true),
							newField = newNode.childNodes;

						for (var i=0; i<newField.length; i++)
						{
							var inputs = newField[i].childNodes;
							for (var k=0;k<inputs.length;k++)
							{
								var theName = inputs[k].name;
								if (theName) {
									tokens = theName.split('[');
									n = tokens[2];
									inputs[k].name = 'fields['+id+'][' +n+ '[' + counter + '][label]';
								}
								if (inputs[k].value) {
									inputs[k].value = '';
								}
							}
						}

						tbody.appendChild(newNode);

						return false;
					});

				$("#fields tbody").sortable({
					handle: '.handle',
					helper: function(e, tr) {
						var originals = tr.children();
						var helper    = tr.clone();
						helper.children().each(function(index) {
							// Set helper cell sizes to match the original sizes
							$(this).width(originals.eq(index).width())
						});
						return helper;
					}
				});  //.disableSelection();
			});
		</script>
	</fieldset>

	<?php echo Html::input('token'); ?>
</form>
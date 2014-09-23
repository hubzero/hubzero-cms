<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));
JToolBarHelper::title(JText::_('COM_PUBLICATIONS_PUBLICATION_CATEGORY') . ': [ ' . $text . ' ]', 'addedit.png');
if ($this->row->id)
{
	JToolBarHelper::apply();
}
JToolBarHelper::save();
JToolBarHelper::cancel();

$dcTypes = array(
	'Collection' , 'Dataset' , 'Event' , 'Image' ,
	'InteractivePublication' , 'MovingImage' , 'PhysicalObject' ,
	'Service' , 'Software' , 'Sound' , 'StillImage' , 'Text'
);

$params = new JRegistry($this->row->params);

JHTML::_('behavior.framework', true);

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	submitform( pressbutton );
	return;
}
</script>

<form action="index.php" method="post" id="item-form" name="adminForm">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_CATEGORY_INFORMATION'); ?></span></legend>
<?php if ($this->row->id) { ?>
	<table>
		<tbody>
			<tr>
				<th><?php echo JText::_('ID'); ?></th>
				<td><?php echo $this->row->id; ?></td>
			</tr>
		</tbody>
	</table>
<?php } ?>
			<div class="input-wrap">
				<label for="field-name"><?php echo JText::_('COM_PUBLICATIONS_FIELD_NAME'); ?>:<span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="prop[name]" id="field-name" maxlength="100" value="<?php echo $this->escape($this->row->name); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-alias"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ALIAS'); ?>:</label>
				<input type="text" name="prop[alias]" id="field-alias" maxlength="100" value="<?php echo $this->escape($this->row->alias); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-url_alias"><?php echo JText::_('COM_PUBLICATIONS_FIELD_URL_ALIAS'); ?>:</label>
				<input type="text" name="prop[url_alias]" id="field-url_alias" maxlength="100" value="<?php echo $this->escape($this->row->url_alias); ?>" />
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_PUBLICATIONS_FIELD_DUBLIN_CORE'); ?>">
				<label for="field-dc_type"><?php echo JText::_('COM_PUBLICATIONS_FIELD_DC_TYPE'); ?>:</label>
				<select name="prop[dc_type]" id="field-dc_type">
					<?php foreach ($dcTypes as $dct) { ?>
					<option value="<?php echo $dct; ?>" <?php if ($this->escape($this->row->dc_type) == $dct) { echo 'selected="selected"'; } ?>><?php echo $dct; ?></option>
					<?php } ?>
				</select>
				<span class="hint"><?php echo JText::_('COM_PUBLICATIONS_FIELD_DUBLIN_CORE'); ?></span>
			</div>
			<div class="input-wrap">
				<label for="field-description"><?php echo JText::_('COM_PUBLICATIONS_FIELD_ABOUT'); ?>:</label>
				<input type="text" name="prop[description]" id="field-description" size="55" maxlength="255" value="<?php echo $this->escape($this->row->description); ?>" />
			</div>

			<input type="hidden" name="prop[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
		</fieldset>
	</div>
	<div class="col width-50 fltrt">

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_FIELD_ITEM_CONFIG'); ?></span></legend>

			<fieldset>
				<legend><?php echo JText::_('COM_PUBLICATIONS_FIELD_STATUS'); ?></legend>

				<div class="input-wrap">
					<input class="option" name="prop[state]" id="field-state1" type="radio" value="1" <?php echo $this->row->state == 1 ? 'checked="checked"' : ''; ?> />
					<label for="field-state1"><?php echo JText::_('COM_PUBLICATIONS_STATUS_ACTIVE'); ?></label>
					<br />
					<input class="option" name="prop[state]" id="field-state0" type="radio" value="0" <?php echo $this->row->state != 1 ? 'checked="checked"' : ''; ?> />
					<label for="field-state0"><?php echo JText::_('COM_PUBLICATIONS_STATUS_INACTIVE'); ?></label>
				</div>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE'); ?></legend>
				<div class="input-wrap" data-hint="<?php echo JText::_('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE_HINT'); ?>">
					<span class="hint"><?php echo JText::_('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE_HINT'); ?></span>

					<input class="option" name="prop[contributable]" id="field-contributable1" type="radio" value="1" <?php echo $this->row->contributable == 1 ? 'checked="checked"' : ''; ?> />
					<label for="field-contributable1"><?php echo JText::_('JYES'); ?></label>
					<br />
					<input class="option" name="prop[contributable]" id="field-contributable0" type="radio" value="0" <?php echo $this->row->contributable == 0 ? 'checked="checked"' : ''; ?> />
					<label for="field-contributable0"><?php echo JText::_('JNO'); ?></label>
				</div>
			</fieldset>
		</fieldset>
	</div>
	<div class="clr"></div>

	<div class="col width-50 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_CATS_MASTER_TYPE_CONFIG'); ?></span></legend>

			<?php foreach ($this->types as $mt) { ?>
				<fieldset>
					<legend><?php echo $mt; ?></legend>
					<div class="input-wrap">
						<input class="option" name="params[type_<?php echo $mt; ?>]" id="field-type_<?php echo $mt; ?>1" type="radio" value="1" <?php echo ($params->get('type_'.$mt, 1) == 1) ? ' checked="checked"':''; ?> />
						<label for="field-type_<?php echo $mt; ?>1"><?php echo  JText::_('COM_PUBLICATIONS_INCLUDE_CHOICE'); ?></label>
						<br />
						<input class="option" name="params[type_<?php echo $mt; ?>]" id="field-type_<?php echo $mt; ?>0" type="radio" value="0" <?php echo ($params->get('type_'.$mt, 1) == 0) ? ' checked="checked"':''; ?> />
						<label for="field-type_<?php echo $mt; ?>0"><?php echo JText::_('COM_PUBLICATIONS_NOT_APPLICABLE'); ?></label>
					</div>
				</fieldset>
			<?php } ?>
		</fieldset>
	</div>
	<div class="col width-50 ltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_PLUGINS'); ?></span></legend>

			<table class="admintable">
				<thead>
					<tr>
						<th><?php echo JText::_('COM_PUBLICATIONS_PLUGIN'); ?></th>
						<th colspan="2"><?php echo JText::_('COM_PUBLICATIONS_STATUS_ACTIVE'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$database = JFactory::getDBO();
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
							$lang = JFactory::getLanguage();
							$lang->load($plugin->name);
						}
						?>
						<tr>
							<td><?php echo (strstr($plugin->name, '_') ? JText::_(stripslashes($plugin->name)) : stripslashes(ucfirst($plugin->name))); ?></td>
							<td><label><input type="radio" name="params[plg_<?php echo $plugin->element; ?>]" value="0"<?php echo ($params->get('plg_'.$plugin->element, 0) == 0) ? ' checked="checked"':''; ?> /> off</label></td>
							<td><label><input type="radio" name="params[plg_<?php echo $plugin->element; ?>]" value="1"<?php echo ($params->get('plg_'.$plugin->element, 0) == 1) ? ' checked="checked"':''; ?> /> on</label></td>
						</tr>
						<?php
					}
				}
				?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	<?php if (!$this->config->get('curation', 0)) { ?>
		<div class="col width-100">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_TYPES_CUSTOM_FIELDS'); ?></span></legend>

			<table class="admintable" id="fields">
				<thead>
					<tr>
	<?php //if ($this->row->id) { ?>
						<th><?php echo JText::_('COM_PUBLICATIONS_TYPES_REORDER'); ?></th>
	<?php //} ?>
						<th><?php echo JText::_('COM_PUBLICATIONS_TYPES_FIELD'); ?></th>
						<th><?php echo JText::_('COM_PUBLICATIONS_TYPES_TYPE'); ?></th>
						<th><?php echo JText::_('COM_PUBLICATIONS_TYPES_REQUIRED'); ?></th>
						<th><?php echo JText::_('COM_PUBLICATIONS_TYPES_OPTIONS'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="<?php echo '5';//($this->row->id) ? '5' : '4'; ?>">
							<button id="add-custom-field" href="#addRow">
								<span><?php echo JText::_('COM_PUBLICATIONS_ADD_ROW'); ?></span>
							</button>
						</td>
					</tr>
				</tfoot>
				<tbody id="field-items">
				<?php
				include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications'
					. DS . 'models' . DS . 'elements.php');
				$elements = new PublicationsElements('', $this->row->customFields);
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
							<span class="handle hasTip" title="<?php echo JText::_('COM_PUBLICATIONS_MOVE_HANDLE'); ?>">
								<?php echo JText::_('COM_PUBLICATIONS_MOVE_HANDLE'); ?>
							</span>
						</td>
						<td>
							<input type="text" name="fields[<?php echo $i; ?>][title]" value="<?php echo $this->escape(stripslashes($field->label)); ?>" maxlength="255" />
							<input type="hidden" name="fields[<?php echo $i; ?>][name]" value="<?php echo $this->escape(stripslashes($field->name)); ?>" />
						</td>
						<td>
							<select name="fields[<?php echo $i; ?>][type]" id="fields-<?php echo $i; ?>-type">
								<optgroup label="<?php echo JText::_('COM_PUBLICATIONS_COMMON'); ?>">
									<option value="text"<?php echo ($field->type == 'text') ? ' selected="selected"':''; ?>><?php echo JText::_('COM_PUBLICATIONS_TYPES_TEXT'); ?></option>
									<option value="textarea"<?php echo ($field->type == 'textarea') ? ' selected="selected"':''; ?>><?php echo JText::_('COM_PUBLICATIONS_TYPES_TEXTAREA'); ?></option>
									<option value="list"<?php echo ($field->type == 'list') ? ' selected="selected"':''; ?>><?php echo JText::_('COM_PUBLICATIONS_TYPES_LIST'); ?></option>
									<option value="radio"<?php echo ($field->type == 'radio') ? ' selected="selected"':''; ?>><?php echo JText::_('COM_PUBLICATIONS_TYPES_RADIO'); ?></option>
									<option value="checkbox"<?php echo ($field->type == 'checkbox') ? ' selected="selected"':''; ?>><?php echo JText::_('COM_PUBLICATIONS_TYPES_CHECKBOX'); ?></option>
									<option value="hidden"<?php echo ($field->type == 'hidden') ? ' selected="selected"':''; ?>><?php echo JText::_('COM_PUBLICATIONS_TYPES_HIDDEN'); ?></option>
								</optgroup>
								<optgroup label="<?php echo JText::_('COM_PUBLICATIONS_PRE_DEFINED'); ?>">
									<option value="date"<?php echo ($field->type == 'date') ? ' selected="selected"':''; ?>><?php echo JText::_('Date'); ?></option>
									<option value="geo"<?php echo ($field->type == 'geo') ? ' selected="selected"':''; ?>><?php echo JText::_('Geo Location'); ?></option>
									<option value="languages"<?php echo ($field->type == 'languages') ? ' selected="selected"':''; ?>><?php echo JText::_('COM_PUBLICATIONS_LANGUAGE_LIST'); ?></option>
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

			<!-- <script type="text/javascript" src="/media/system/js/jquery.js"></script>
			<script type="text/javascript" src="/media/system/js/jquery.noconflict.js"></script>
			<script type="text/javascript" src="/media/system/js/jquery.ui.js"></script>  -->
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
					},

					addRow: function(id) {
						var tbody = document.getElementById(id).tBodies[0],
							counter = tbody.rows.length,
							newNode = tbody.rows[0].cloneNode(true),
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
										inputs[k].id = id + '-' + counter + '-' + n.replace(']', '')+'-tmp';
										replaceme = id + '-' + counter + '-' + n.replace(']', '')+'-tmp';
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
								newField[i].id = 'fields-'+counter+'-options';
							}
						}

						tbody.appendChild(newNode);

						// Make a clone of the clone. Why? Because IE 8 is dumb.
						// IE still retains a reference to the original object for change events
						// So, when calling onChange, the event gets fired for the clone AND the
						// original. Cloning the clone seems to fix this.
						if (replaceme) {
							var replace = jq(replaceme);
							var select = jq.clone(replace).appendAfter(replace);
							jq.remove(replace);
						}

						Fields.initSelect();

						//jq('#fields tbody').sortable(); //'enable');

						return false;
					},

					addOption: function(id) {
						var tbody = document.getElementById(id).tBodies[0];
						var counter = tbody.rows.length;
						var newNode = tbody.rows[0].cloneNode(true);

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
									inputs[k].name = 'fields['+id+'][' +n+ '[' + counter + '][label]';
								}
								if (inputs[k].value) {
									inputs[k].value = '';
								}
							}
						}

						tbody.appendChild(newNode);

						return false;
					},

					initOptions: function() {
						jq('.add-custom-option').each(function(i, el){
							jq(el)
								.off('click')
								.on('click', function(e){
									e.preventDefault();

									Fields.addOption(jq(this).attr('rel'));
								});
						});
					},

					timer: 0,

					clear: function() {
						Fields.timer = 0;
					},

					initSelect: function() {
						jq('#fields select').each(function(i, el){
							jq(el)
								.off('change')
								.on('change', function(){
									var i = this.name.replace(/^fields\[(\d+)\]\[type\]/g,"$1");
									jq.get('index.php?option=com_publications&controller=categories&no_html=1&task=element&ctrl=fields&type='+this.value+'&name='+i,{}, function (response){
										jq('#fields-'+i+'-options').html(response);
										Fields.initOptions();
									});
								})
						});
					},

					initialise: function() {
						jq('#add-custom-field').on('click', function (e){
							e.preventDefault();

							Fields.addRow('fields');
						});

						Fields.initSelect();
						Fields.initOptions();

						jq('#fields tbody').sortable();
					}
				}

				jQuery(document).ready(function(jq){
					var $ = jq;

					Fields.initialise();

					$("#fields tbody").sortable({
						handle: '.handle',
						helper: function(e, tr) {
							var $originals = tr.children();
							var $helper = tr.clone();
							$helper.children().each(function(index) {
								// Set helper cell sizes to match the original sizes
								$(this).width($originals.eq(index).width())
							});
							return $helper;
						}
					});  //.disableSelection();
				});
			</script>
		</fieldset>
		</div>
		<div class="clr"></div>
		<?php } ?>
	<?php echo JHTML::_('form.token'); ?>
</form>
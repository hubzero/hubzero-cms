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

// Push some styles to the template
$document =& JFactory::getDocument();
$document->addStyleSheet('components' . DS . $this->option . DS . 'assets' . DS . 'css' . DS . 'resources.css');

$canDo = ResourcesHelper::getActions('type');

$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '">' . JText::_('Resource Type') . '</a>: <small><small>[ ' . $text . ' ]</small></small>', 'addedit.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

$paramsClass = 'JParameter';
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$paramsClass = 'JRegistry';
}

$params = new $paramsClass($this->row->params);
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('item-form');
	
	if (pressbutton == 'canceltype') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	if (form.title.value == '') {
		alert( 'Type must have a title' );
	} else {
		submitform( pressbutton );
	}
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
		var tbody = document.getElementById(id).tBodies[0];
		var counter = tbody.rows.length;
		var newNode = tbody.rows[0].cloneNode(true);

		var replaceme = null;

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
			var replace = $(replaceme);
			var select = Element.clone(replace).injectAfter(replace);
			Element.remove(replace);
		}
		
		Fields.initSelect();
		
		jq('#fields tbody').sortable('enable');

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
		$$('.add-custom-option').each(function(el){
			$(el).removeEvents('click');
			$(el).addEvent('click', function(e){
				new Event(e).stop();
				Fields.addOption(el.getProperty('rel'));
			});
		});
	},
	
	tiner: 0,
	
	clear: function() {
		Fields.timer = 0;
	},
	
	initSelect: function() {
		$$('#fields select').each(function(el){
			$(el).removeEvents('change');
			$(el).addEvent('change', function(){
				var i = this.name.replace(/^fields\[(\d+)\]\[type\]/g,"$1");
				var myAjax1 = new Ajax('index.php?option=com_resources&controller=types&no_html=1&task=element&ctrl=fields&type='+this.value+'&name='+i,{
					update: $('fields-'+i+'-options')
				}).request();
				myAjax1.addEvent('onComplete', function(){
					Fields.initOptions();
				});
			})
		});
	},

	initialise: function() {
		$('add-custom-field').addEvent('click', function(e){
			new Event(e).stop();
			
			Fields.addRow('fields');
		});
		
		Fields.initSelect();
		
		Fields.initOptions();
	}
}


window.addEvent('domready', Fields.initialise);
</script>

<form action="index.php" method="post" id="item-form" name="adminForm">
	<div class="col width-50 fltlft">
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('RESOURCES_TYPES_DETAILS'); ?></span></legend>
		
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="type"><?php echo JText::_('RESOURCES_TYPES_TITLE'); ?>:</label></td>
					<td><input type="text" name="type" id="type" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->type)); ?>" /></td>
				</tr>
				<tr>
					<td class="key"><label for="alias"><?php echo JText::_('Alias'); ?>:</label></td>
					<td>
						<input type="text" name="alias" id="alias" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" /><br />
						<span class="hint"><?php echo JText::_('If no alias provided, one will be generated from the title.'); ?></span>
					</td>
				</tr>
				<tr>
					<td class="key"><label><?php echo JText::_('RESOURCES_TYPES_CATEGORY'); ?>:</label></td>
					<td><?php echo ResourcesHtml::selectType($this->categories, 'category', $this->row->category, '[ select ]', '', '', ''); ?></td>
				</tr>
				<tr>
					<td class="key"><label for="contributable"><?php echo JText::_('RESOURCES_TYPES_CONTRIBUTABLE'); ?>:</label></td>
					<td><input type="checkbox" name="contributable" id="contributable" value="1"<?php echo ($this->row->contributable) ? ' checked="checked"' : ''; ?> /> <?php echo JText::_('RESOURCES_TYPES_CONTRIBUTABLE_EXPLANATION'); ?></td>
				</tr>
<?php if ($this->row->category != 27) { ?>
				<tr>
					<td class="key"><label for="params-linkaction"><?php echo JText::_('Linked file action'); ?>:</label></td>
					<td>
						<select name="params[linkAction]" id="params-linkaction">
							<option value="extension"<?php echo ($params->get('linkAction') == 'extension') ? ' selected="selected"':''; ?>><?php echo JText::_('Determine by file extension'); ?></option>
							<option value="external"<?php echo ($params->get('linkAction') == 'external') ? ' selected="selected"':''; ?>><?php echo JText::_('New window'); ?></option>
							<option value="lightbox"<?php echo ($params->get('linkAction') == 'lightbox') ? ' selected="selected"':''; ?>><?php echo JText::_('Lightbox'); ?></option>
							<option value="download"<?php echo ($params->get('linkAction') == 'download') ? ' selected="selected"':''; ?>><?php echo JText::_('Download'); ?></option>
						</select>
					</td>
				</tr>
<?php } ?>
				<tr>
					<td class="key"><label><?php echo JText::_('RESOURCES_TYPES_DESCIPTION'); ?>:</label></td>
					<td><?php 
						$editor =& JFactory::getEditor();
						echo $editor->display('description', stripslashes($this->row->description), '', '', '45', '10', false);
					?></td>
				</tr>
			</tbody>
		</table>
	
		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />
	</fieldset>
	</div>
	<div class="col width-50 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Plugins'); ?></span></legend>

			<table class="admintable">
				<thead>
					<tr>
						<th><?php echo JText::_('Plugin'); ?></th>
						<th colspan="2"><?php echo JText::_('Active'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php 
				$database =& JFactory::getDBO();
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$database->setQuery( "SELECT * FROM #__extensions WHERE `type`='plugin' AND `folder`='resources'" );
				}
				else
				{
					$database->setQuery( "SELECT * FROM #__plugins WHERE `folder`='resources'" );
				}
				$plugins = $database->loadObjectList();

				foreach ($plugins as $plugin)
				{
					?>
					<tr>
						<td><?php echo stripslashes($plugin->name); ?></td>
						<td><label><input type="radio" name="params[plg_<?php echo $plugin->element; ?>]" value="0"<?php echo ($params->get('plg_'.$plugin->element) == 0) ? ' checked="checked"':''; ?> /> off</label></td>
						<td><label><input type="radio" name="params[plg_<?php echo $plugin->element; ?>]" value="1"<?php echo ($params->get('plg_'.$plugin->element) == 1) ? ' checked="checked"':''; ?> /> on</label></td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<div class="col width-100">
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('RESOURCES_TYPES_CUSTOM_FIELDS'); ?></span></legend>
		
		<table class="admintable" id="fields">
			<thead>
				<tr>
<?php //if ($this->row->id) { ?>
					<th><?php echo JText::_('RESOURCES_TYPES_REORDER'); ?></th>
<?php //} ?>
					<th><?php echo JText::_('RESOURCES_TYPES_FIELD'); ?></th>
					<th><?php echo JText::_('RESOURCES_TYPES_TYPE'); ?></th>
					<th><?php echo JText::_('RESOURCES_TYPES_REQUIRED'); ?></th>
					<th><?php echo JText::_('RESOURCES_TYPES_OPTIONS'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="<?php echo '5';//($this->row->id) ? '5' : '4'; ?>">
						<button id="add-custom-field" href="#addRow">
							<span><?php echo JText::_('+ Add new row'); ?></span>
						</button>
					</td>
				</tr>
			</tfoot>
			<tbody id="field-items">
			<?php 
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');
			$elements = new ResourcesElements('', $this->row->customFields);
			$schema = $elements->getSchema();
			
			if (!is_object($schema))
			{
				$schema = new stdClass();
				$schema->fields = array();
			}

			if (count($schema->fields) <= 0)
			{
				$element = new stdClass();
				$element->name = '';
				$element->label = '';
				$element->type = '';
				$element->required = '';
				$element->value = '';
				$element->default = '';
				$element->description = '';
				
				$schema->fields[] = $element;
			}

			$i = 0;
			foreach ($schema->fields as $field)
			{
				?>
				<tr>
					<td class="order">
						<span class="handle hasTip" title="<?php echo JText::_('RESOURCES_MOVE_HANDLE'); ?>">
							<?php echo JText::_('RESOURCES_MOVE_HANDLE'); ?>
						</span>
					</td>
					<td>
						<input type="text" name="fields[<?php echo $i; ?>][title]" value="<?php echo $this->escape(stripslashes($field->label)); ?>" maxlength="255" />
						<input type="hidden" name="fields[<?php echo $i; ?>][name]" value="<?php echo $this->escape(stripslashes($field->name)); ?>" />
					</td>
					<td>
						<select name="fields[<?php echo $i; ?>][type]" id="fields-<?php echo $i; ?>-type">
							<optgroup label="<?php echo JText::_('Common'); ?>">
								<option value="text"<?php echo ($field->type == 'text') ? ' selected="selected"':''; ?>><?php echo JText::_('RESOURCES_TYPES_TEXT'); ?></option>
								<option value="textarea"<?php echo ($field->type == 'textarea') ? ' selected="selected"':''; ?>><?php echo JText::_('RESOURCES_TYPES_TEXTAREA'); ?></option>
								<option value="list"<?php echo ($field->type == 'list') ? ' selected="selected"':''; ?>><?php echo JText::_('RESOURCES_TYPES_LIST'); ?></option>
								<option value="radio"<?php echo ($field->type == 'radio') ? ' selected="selected"':''; ?>><?php echo JText::_('RESOURCES_TYPES_RADIO'); ?></option>
								<option value="checkbox"<?php echo ($field->type == 'checkbox') ? ' selected="selected"':''; ?>><?php echo JText::_('RESOURCES_TYPES_CHECKBOX'); ?></option>
								<option value="hidden"<?php echo ($field->type == 'hidden') ? ' selected="selected"':''; ?>><?php echo JText::_('RESOURCES_TYPES_HIDDEN'); ?></option>
							</optgroup>
							<optgroup label="<?php echo JText::_('Pre-defined'); ?>">
								<option value="date"<?php echo ($field->type == 'date') ? ' selected="selected"':''; ?>><?php echo JText::_('Date'); ?></option>
								<option value="geo"<?php echo ($field->type == 'geo') ? ' selected="selected"':''; ?>><?php echo JText::_('Geo Location'); ?></option>
								<option value="languages"<?php echo ($field->type == 'languages') ? ' selected="selected"':''; ?>><?php echo JText::_('Language List'); ?></option>
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
		<!-- <script src="components/com_resources/assets/js/xsortables.js"></script> -->
		<script src="/media/system/js/jquery.js"></script> 
		<script src="/media/system/js/jquery.noconflict.js"></script> 
		<script src="/media/system/js/jquery.ui.js"></script> 
		<script>
			if (!jq) {
				var jq = $;
			}
			
			jQuery(document).ready(function(jq){
				var $ = jq;
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

	<?php echo JHTML::_('form.token'); ?>
</form>
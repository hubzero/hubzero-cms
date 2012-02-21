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

$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));
JToolBarHelper::title('<a href="index.php?option=' . $this->option . '">' . JText::_('Resource Type') . '</a>: <small><small>[ ' . $text . ' ]</small></small>', 'addedit.png');
JToolBarHelper::save();
JToolBarHelper::cancel();

$params = new JParameter($this->row->params);
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('adminForm');
	
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

function addCustomField(id)
{
   	new Event(event).stop();

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
                inputs[k].name = id + '[' + counter + ']['+ n;
            }
            var n = id + '[' + counter + '][type]';
            var z = id + '[' + counter + '][status]';
            if (inputs[k].value && inputs[k].name != n && inputs[k].name != z) {
                inputs[k].value = '';
            }
        }
    }
	
	tbody.appendChild(newNode); 
	console.log(newNode);
}
</script>

<form action="index.php" method="post" id="adminForm" name="adminForm">
	<p><?php echo JText::_('RESOURCES_REQUIRED_EXPLANATION'); ?></p>
	
	<fieldset class="adminform">
		<legend><?php echo JText::_('RESOURCES_TYPES_DETAILS'); ?></legend>
		
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="type"><?php echo JText::_('RESOURCES_TYPES_TITLE'); ?>: <span class="required">*</span></label></td>
					<td><input type="text" name="type" id="type" size="30" maxlength="100" value="<?php echo $this->escape($this->row->type); ?>" /></td>
				</tr>
				<tr>
					<td class="key"><label for="alias"><?php echo JText::_('Alias'); ?>:</label></td>
					<td>
						<input type="text" name="alias" id="alias" size="30" maxlength="100" value="<?php echo $this->escape($this->row->alias); ?>" /><br />
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
	<fieldset class="adminform">
		<legend><?php echo JText::_('RESOURCES_TYPES_CUSTOM_FIELDS'); ?></legend>
		
		<table class="admintable" id="custom">
			<thead>
				<tr>
					<th><?php echo JText::_('RESOURCES_TYPES_FIELD'); ?></th>
					<th><?php echo JText::_('RESOURCES_TYPES_TYPE'); ?></th>
					<th><?php echo JText::_('RESOURCES_TYPES_REQUIRED'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php 
			//$fields = $row->customFields;
			$fields = array();
			if (trim($this->row->customFields) != '') {
				$fs = explode("\n", trim($this->row->customFields));
				foreach ($fs as $f)
				{
					$fields[] = explode('=', $f);
				}
			}

			$r = count($fields);
			if ($r > 10) {
				$n = $r;
			} else {
				$n = 10;
			}
			for ($i=0; $i < $n; $i++)
			{
				if ($r == 0 || !isset($fields[$i])) {
					$fields[$i] = array();
					$fields[$i][0] = NULL;
					$fields[$i][1] = NULL;
					$fields[$i][2] = NULL;
					$fields[$i][3] = NULL;
					$fields[$i][4] = NULL;
				}
				?>
				<tr>
					<td><input type="text" name="fields[<?php echo $i; ?>][title]" value="<?php echo $fields[$i][1]; ?>" maxlength="255" /></td>
					<td><select name="fields[<?php echo $i; ?>][type]">
						<option value="text"<?php echo ($fields[$i][2]=='text') ? ' selected="selected"':''; ?>><?php echo JText::_('RESOURCES_TYPES_TEXT'); ?></option>
						<option value="textarea"<?php echo ($fields[$i][2]=='textarea') ? ' selected="selected"':''; ?>><?php echo JText::_('RESOURCES_TYPES_TEXTAREA'); ?></option>
					</select></td>
					<td><input type="checkbox" name="fields[<?php echo $i; ?>][required]" value="1"<?php echo ($fields[$i][3]) ? ' checked="checked"':''; ?> /></td>
				</tr>
				<?php
			}
			?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3"><a id="add-custom-field" onclick="addCustomField('custom');" href="#">+ Add new row</a></td>
				</tr>                                                               
			</tfoot>
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_('Plugins'); ?></legend>
		
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
			$database->setQuery( "SELECT * FROM #__plugins WHERE folder='resources'" );
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

	<?php echo JHTML::_('form.token'); ?>
</form>
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
JToolBarHelper::title(JText::_('Publication Category') . ': [ ' . $text . ' ]', 'addedit.png');
JToolBarHelper::save();
JToolBarHelper::cancel();

$dcTypes = array(
	'Collection' , 'Dataset' , 'Event' , 'Image' ,
 	'InteractiveResource' , 'MovingImage' , 'PhysicalObject' ,
    'Service' , 'Software' , 'Sound' , 'StillImage' , 'Text');

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	submitform( pressbutton );
	return;
}
</script>

<form action="index.php" method="post" id="item-form" name="adminForm">
	<div class="col width-50 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Category Information'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-name"><?php echo JText::_('Name'); ?>:<span class="required">*</span></label></td>
						<td>
							<input type="text" name="fields[name]" id="field-name" size="55" maxlength="100" value="<?php echo $this->escape($this->row->name); ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-alias"><?php echo JText::_('Alias'); ?>:</label></td>
						<td>
							<input type="text" name="fields[alias]" id="field-alias" size="55" maxlength="100" value="<?php echo $this->escape($this->row->alias); ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-url_alias"><?php echo JText::_('URL Alias'); ?>:</label></td>
						<td>
							<input type="text" name="fields[url_alias]" id="field-url_alias" size="55" maxlength="100" value="<?php echo $this->escape($this->row->url_alias); ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-dc_type"><?php echo JText::_('dc:type'); ?>:</label></td>
						<td>
							<select name="fields[dc_type]" id="field-dc_type">
							<?php foreach ($dcTypes as $dct) { ?>
							<option value="<?php echo $dct; ?>" <?php if ($this->escape($this->row->dc_type) == $dct) { echo 'selected="selected"'; } ?>><?php echo $dct; ?></option>
							<?php } ?>
							</select>
							<span class="hint"><?php echo JText::_('DublinCoreMetaData type'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Description'); ?>:</label></td>
						<td><input type="text" name="fields[description]" id="field-description" size="55" maxlength="255" value="<?php echo $this->escape($this->row->description); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Custom Fields'); ?>:</label></td>
						<td><?php 
							$editor = JFactory::getEditor();
							echo $editor->display('fields[customFields]', stripslashes($this->row->customFields), '', '', '50', '5', false);
						?></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Params'); ?>:</label></td>
						<td><?php 
							$editor = JFactory::getEditor();
							echo $editor->display('fields[params]', stripslashes($this->row->params), '', '', '50', '5', false);
						?></td>
					</tr>
				
				</tbody>
			</table>

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
	  <fieldset class="adminform">
		<legend><span><?php echo JText::_('Item Configuration'); ?></span></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><?php echo JText::_('ID'); ?></td>
					<td><?php echo $this->row->id; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('Status'); ?></td>
					<td>
						<label class="block"><input class="option" name="state" type="radio" value="1" <?php echo $this->row->state == 1 ? 'checked="checked"' : ''; ?> /> <?php echo JText::_('Active'); ?>
						</label>
						<label class="block"><input class="option" name="state" type="radio" value="0" <?php echo $this->row->state != 1 ? 'checked="checked"' : ''; ?> /> <?php echo JText::_('Inactive'); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('Contributable'); ?></td>
					<td>
						<span class="hint"><?php echo JText::_('Offer category as a catalogue choice for new publications?'); ?></span>
						<label class="block"><input class="option" name="contributable" type="radio" value="1" <?php echo $this->row->contributable == 1 ? 'checked="checked"' : ''; ?> /> <?php echo JText::_('Yes'); ?>
						</label>
						<label class="block"><input class="option" name="contributable" type="radio" value="0" <?php echo $this->row->contributable == 0 ? 'checked="checked"' : ''; ?> /> <?php echo JText::_('No'); ?>
						</label>
					</td>
				</tr>
			</tbody>
		</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>
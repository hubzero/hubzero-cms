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
JToolBarHelper::title('<a href="index.php?option=' . $this->option . '">' . JText::_('COM_PUBLICATIONS_LICENSE') . '</a>: <small><small>[ ' . $text . ' ]</small></small>', 'addedit.png');
JToolBarHelper::save();
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	/*var form = document.getElementById('adminForm');
	
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	var field = document.getElementById('field-title');
	if (field.value == '') {
		alert( 'Type must have a title' );
	} else {
		alert('vff');*/
		submitform( pressbutton );
		return;
	//}
}
</script>

<form action="index.php" method="post" id="item-form" name="adminForm">
	<div class="col width-50 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_PUBLICATIONS_LICENSE_DETAILS'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-title"><?php echo JText::_('Title'); ?>:<span class="required">*</span></label></td>
						<td><input type="text" name="fields[title]" id="field-title" size="55" maxlength="100" value="<?php echo $this->escape($this->row->title); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-name"><?php echo JText::_('Name'); ?>:<span class="required">*</span></label></td>
						<td>
							<input type="text" name="fields[name]" id="field-name" size="55" maxlength="100" value="<?php echo $this->escape($this->row->name); ?>" />
							<span class="hint"><?php echo JText::_('If no name is provided, one will be generated from the title.'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-url"><?php echo JText::_('URL'); ?>:</label></td>
						<td>
							<input type="text" name="fields[url]" id="field-url" size="55" maxlength="100" value="<?php echo $this->escape($this->row->url); ?>" />
							<span class="hint"><?php echo JText::_('URL to the full license.'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('About'); ?>:<span class="required">*</span></label></td>
						<td><?php 
							$editor =& JFactory::getEditor();
							echo $editor->display('fields[info]', stripslashes($this->row->info), '', '', '50', '4', false);
						?>
							<span class="hint"><?php echo JText::_('Short description of license'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Content'); ?>:</label></td>
						<td><?php 
							$editor =& JFactory::getEditor();
							echo $editor->display('fields[text]', stripslashes($this->row->text), '', '', '50', '10', false);
						?></td>
					</tr>
					<tr>
						<td class="key"><label for="field-icon"><?php echo JText::_('Icon'); ?>:</label></td>
						<td>
							<input type="text" name="fields[icon]" id="field-icon" size="55" value="<?php echo $this->escape($this->row->icon); ?>" />
							<span class="hint"><?php echo JText::_('Path to icon image'); ?></span>
						</td>
					</tr>
				</tbody>
			</table>

			<input type="hidden" name="fields[ordering]" value="<?php echo $this->row->ordering; ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
	  <fieldset class="adminform">
			<legend><span><?php echo JText::_('License Configuration'); ?></span></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><?php echo JText::_('ID'); ?></td>
					<td><?php echo $this->row->id; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('Default'); ?></td>
					<td><?php echo $this->row->main == 1 ? JText::_('COM_PUBLICATIONS_LICENSE_YES') : JText::_('COM_PUBLICATIONS_LICENSE_NO') ; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('Active'); ?></td>
					<td>
						<span class="hint"><?php echo JText::_('COM_PUBLICATIONS_LICENSE_ACTIVE_EXPLAIN'); ?></span>
						<label class="block"><input class="option" name="active" type="radio" value="1" <?php echo $this->row->active == 1 ? 'checked="checked"' : ''; ?> /> <?php echo JText::_('COM_PUBLICATIONS_LICENSE_YES'); ?>
						</label>
						<label class="block"><input class="option" name="active" type="radio" value="0" <?php echo $this->row->active == 0 ? 'checked="checked"' : ''; ?> /> <?php echo JText::_('COM_PUBLICATIONS_LICENSE_NO'); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('Customizable'); ?></td>
					<td>
						<span class="hint"><?php echo JText::_('Do we allow users to customize license text?'); ?></span>
						<label class="block"><input class="option" name="customizable" type="radio" value="1" <?php echo $this->row->customizable == 1 ? 'checked="checked"' : ''; ?> /> <?php echo JText::_('COM_PUBLICATIONS_LICENSE_YES'); ?>
						</label>
						<label class="block"><input class="option" name="customizable" type="radio" value="0" <?php echo $this->row->customizable == 0 ? 'checked="checked"' : ''; ?> /> <?php echo JText::_('COM_PUBLICATIONS_LICENSE_NO'); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('Agreement required'); ?></td>
					<td>
						<span class="hint"><?php echo JText::_('Do we require publication authors to agree to license terms?'); ?></span>
						<label class="block"><input class="option" name="agreement" type="radio" value="1" <?php echo $this->row->agreement == 1 ? 'checked="checked"' : ''; ?> /> <?php echo JText::_('COM_PUBLICATIONS_LICENSE_YES'); ?>
						</label>
						<label class="block"><input class="option" name="agreement" type="radio" value="0" <?php echo $this->row->agreement == 0 ? 'checked="checked"' : ''; ?> /> <?php echo JText::_('COM_PUBLICATIONS_LICENSE_NO'); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('Apps only'); ?></td>
					<td>
						<span class="hint"><?php echo JText::_('Is this license applicable to apps publications only?'); ?></span>
						<label class="block"><input class="option" name="apps_only" type="radio" value="1" <?php echo $this->row->apps_only == 1 ? 'checked="checked"' : ''; ?> /> <?php echo JText::_('COM_PUBLICATIONS_LICENSE_YES'); ?>
						</label>
						<label class="block"><input class="option" name="apps_only" type="radio" value="0" <?php echo $this->row->apps_only == 0 ? 'checked="checked"' : ''; ?> /> <?php echo JText::_('COM_PUBLICATIONS_LICENSE_NO'); ?>
						</label>
					</td>
				</tr>
<?php if ($this->row->id) { ?>
				<tr>
					<td class="key"><?php echo JText::_('Ordering'); ?></td>
					<td><?php echo $this->row->ordering; ?></td>
				</tr>
<?php } ?>
			</tbody>
		</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>
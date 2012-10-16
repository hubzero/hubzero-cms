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

$canDo = CoursesHelper::getActions('course');

$text = ($this->task == 'edit' ? JText::_('Edit Page') : JText::_('New Page'));

JToolBarHelper::title(JText::_('COM_COURSES').': <small><small>[ ' . $text . ' ]</small></small>', 'courses.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
?>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-100">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Course Page'); ?></span></legend>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('cn'); ?>" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="page[id]" value="<?php echo $this->page->id; ?>" />
			<input type="hidden" name="page[gid]" value="<?php echo $this->course->get('gidNumber'); ?>" />

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-title"><?php echo JText::_('Title'); ?>:</label></td>
						<td>
							<input type="text" name="page[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->page->title)); ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-url"><?php echo JText::_('URL'); ?>:</label></td>
						<td>
							<input type="text" name="page[url]" id="field-url" value="<?php echo $this->escape(stripslashes($this->page->url)); ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-content"><?php echo JText::_('Content'); ?>:</label></td>
						<td>
							<textarea name="page[content]" id="field-content" rows="10" columns="10"><?php echo $this->escape(stripslashes($this->page->content)); ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="content"><?php echo JText::_('Active'); ?>:</label></td>
						<td>
							<input type="radio" name="page[active]" id="field-active_yes" value="1" <?php if ($this->page->active) { echo 'checked="checked"'; } ?> /> <label for="field-active_yes"><?php echo JText::_('Yes'); ?></label>
							<input type="radio" name="page[active]" id="field-active_no" value="0" <?php if (!$this->page->active) { echo 'checked="checked"'; } ?> /> <label for="field-active_no"><?php echo JText::_('No'); ?></label>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-privacy"><?php echo JText::_('Privacy'); ?>:</label></td>
						<td>
							<select name="page[privacy]" id="field-privacy">
								<option value="default" <?php if ($this->page->privacy == 'default') { echo 'selected="selected"'; } ?>><?php echo JText::_('Inherit Overview Tabs Privacy'); ?></option>
								<option value="members" <?php if ($this->page->privacy == 'members') { echo 'selected="selected"'; } ?>><?php echo JText::_('Members Only'); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>

	<?php echo JHTML::_('form.token'); ?>
</form>
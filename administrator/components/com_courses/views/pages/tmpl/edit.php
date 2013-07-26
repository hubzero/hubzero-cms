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
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
?>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-70">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Page details'); ?></span></legend>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="course" value="<?php echo $this->course->get('id'); ?>" />
			<input type="hidden" name="offering" value="<?php echo $this->offering->get('id'); ?>" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="fields[course_id]" value="<?php echo $this->course->get('id'); ?>" />
			<input type="hidden" name="fields[offering_id]" value="<?php echo $this->row->get('offering_id'); ?>" />

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-title"><?php echo JText::_('Title'); ?>:</label></td>
						<td>
							<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-url"><?php echo JText::_('URL'); ?>:</label></td>
						<td>
							<input type="text" name="fields[url]" id="field-url" value="<?php echo $this->escape(stripslashes($this->row->url)); ?>" />
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label for="field-content"><?php echo JText::_('Content'); ?>:</label><br />
							<textarea name="fields[content]" id="field-content" rows="35" columns="40"><?php echo $this->escape(stripslashes($this->row->content)); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-30 fltrt">
		<table class="meta" summary="<?php echo JText::_('COM_COURSES_META_SUMMARY'); ?>">
			<tbody>
				<tr>
					<th><?php echo JText::_('Type'); ?></th>
				<?php if ($this->row->get('course_id')) { ?>
					<?php if ($this->row->get('offering_id')) { ?>
						<td><?php echo JText::_('Offering page'); ?></td>
					<?php } else { ?>
						<td><?php echo JText::_('Course overview page'); ?></td>
					<?php } ?>
				<?php } else { ?>
					<td><?php echo JText::_('User Guide'); ?></td>
				<?php } ?>
				</tr>
			<?php if ($this->row->get('course_id')) { ?>
				<tr>
					<th><?php echo JText::_('Course ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('course_id')); ?></td>
				</tr>
			<?php } ?>
			<?php if ($this->row->get('offering_id')) { ?>
				<tr>
					<th><?php echo JText::_('Offering ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('offering_id')); ?></td>
				</tr>
			<?php } ?>
				<tr>
					<th><?php echo JText::_('ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('id')); ?></td>
				</tr>
			</tbody>
		</table>
		
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Publishing'); ?></span></legend>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="content"><?php echo JText::_('Active'); ?>:</label></td>
						<td>
							<input type="radio" name="fields[active]" id="field-active_yes" value="1" <?php if ($this->row->active) { echo 'checked="checked"'; } ?> /> <label for="field-active_yes"><?php echo JText::_('Yes'); ?></label>
							<input type="radio" name="fields[active]" id="field-active_no" value="0" <?php if (!$this->row->active) { echo 'checked="checked"'; } ?> /> <label for="field-active_no"><?php echo JText::_('No'); ?></label>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<?php if (!$this->row->get('id')) { ?>
			<p><?php echo JText::_('A new page must be saved first before uploading files.'); ?></p>
			<?php } else { ?>
			<iframe width="100%" height="300" name="filelist" id="filelist" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=pages&amp;task=files&amp;tmpl=component&amp;listdir=<?php echo $this->row->get('offering_id'); ?>&amp;course=<?php echo $this->course->get('id'); ?>"></iframe>
			<?php } ?>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>
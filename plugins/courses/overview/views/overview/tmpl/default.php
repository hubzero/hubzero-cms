<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$field = strtolower(JRequest::getWord('field', ''));
$task  = strtolower(JRequest::getWord('task', ''));

if ($this->course->access('edit', 'course') && $field == 'description')
{
	?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" class="form-inplace" method="post">
		<label for="field_description">
			<?php
				echo \JFactory::getEditor()->display('course[description]', $this->escape(stripslashes($this->course->description('raw'))), '', '', 35, 50, false, 'field_description');
			?>
		</label>

		<p class="submit">
			<input type="submit" class="btn btn-success" value="<?php echo JText::_('COM_COURSES_SAVE'); ?>" />
			<a class="btn btn-secondary" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=course&gid=' . $this->course->get('alias')); ?>">
				<?php echo JText::_('COM_COURSES_CANCEL'); ?>
			</a>
		</p>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="course" />
		<input type="hidden" name="task" value="save" />

		<?php echo JHTML::_('form.token'); ?>

		<input type="hidden" name="gid" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
		<input type="hidden" name="course[id]" value="<?php echo $this->escape($this->course->get('id')); ?>" />
		<input type="hidden" name="course[alias]" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
	</form>
	<?php
}
else
{
	if ($this->course->access('edit', 'course'))
	{
		?>
		<div class="manager-options">
			<a class="icon-edit btn btn-secondary" href="<?php echo JRoute::_($this->course->link() . '&task=edit&field=description'); ?>">
				<?php echo JText::_('COM_COURSES_EDIT'); ?>
			</a>
			<span><strong><?php echo JText::_('COM_COURSES_LONG_DESCRIPTION'); ?></strong></span>
		</div>
		<?php
	}

	if (!$this->course->get('description'))
	{
		echo '<p><em>' . JText::_('COM_COURSES_LONG_DESCRIPTION_NONE') . '</em></p>';
	}
	else
	{
		echo $this->course->description('parsed');
	}
}
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

JToolBarHelper::title(JText::_('COM_COURSES') . ': <small><small>[ ' . JText::_('Course Pages') . ' ]</small></small>', 'courses.png');
if ($canDo->get('core.create')) 
{
	JToolBarHelper::addNew();
}
//JToolBarHelper::cancel();
?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	submitform(pressbutton);
}
</script>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;gid=<?php echo $this->course->cn; ?>" name="adminForm" id="adminForm" method="post">
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	
	<table class="adminlist">
		<thead>
		 	<tr>
				<th colspan="2" scope="col"><?php echo $this->course->get('description') . ' - Pages'; ?></th>
			</tr>
		</thead>
		<tbody>
<?php if (count($this->pages) > 0) : ?>
	<?php foreach ($this->pages as $page) : ?>
			<tr>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;gid=<?php echo $this->course->cn; ?>&amp;task=edit&amp;page=<?php echo $page['id']; ?>">
						<?php echo $this->escape(stripslashes($page['title'])); ?>
					</a>
<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($page['title'])); ?>
					</span>
<?php } ?>
				</td>
				<td>
				<?php if ($page['active']) { ?>
					<span class="state publish">
						<span class="text"><?php echo JText::_('Published'); ?></span>
					</span>
				<?php } else { ?>
					<span class="state unpublish">
						<span class="text"><?php echo JText::_('Unpublished'); ?></span>
					</span>
				<?php } ?>
				</td>
			</tr>
	<?php endforeach; ?>
<?php else : ?>
			<tr>
				<td colspan="2"><?php echo JText::_('Currently there are no pages for this course.'); ?></td>
			</tr>
<?php endif; ?>
		</tbody>
	</table>

	<?php echo JHTML::_('form.token'); ?>
</form>
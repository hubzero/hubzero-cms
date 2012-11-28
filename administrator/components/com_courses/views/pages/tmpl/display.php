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

$canDo = CoursesHelper::getActions('page');

JToolBarHelper::title(JText::_('COM_COURSES') . ': <small><small>[ ' . JText::_('Course Pages') . ' ]</small></small>', 'courses.png');
if ($canDo->get('core.create')) 
{
	JToolBarHelper::addNew();
}
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.delete')) 
{
	JToolBarHelper::deleteList('delete', 'delete');
}
?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	submitform(pressbutton);
}
</script>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('COM_COURSES_SEARCH'); ?>:</label> 
		<input type="text" name="search" id="filter_search" value="<?php echo $this->filters['search']; ?>" />

		<input type="submit" value="<?php echo JText::_('COM_COURSES_GO'); ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
		 	<tr>
				<th colspan="4">
					(<a href="index.php?option=<?php echo $this->option ?>&amp;controller=courses&amp;task=edit&amp;id[]=<?php echo $this->course->get('id'); ?>">
						<?php echo $this->escape(stripslashes($this->course->get('alias'))); ?>
					</a>) 
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=courses&amp;task=edit&amp;id[]=<?php echo $this->course->get('id'); ?>">
						<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
					</a>: 
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=offerings&amp;task=edit&amp;id[]=<?php echo $this->offering->get('id'); ?>">
						<?php echo $this->escape(stripslashes($this->offering->get('title'))); ?>
					</a>: 
					<?php echo JText::_('Pages'); ?>
				</th>
			</tr>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col"><?php echo JText::_('ID'); ?></th>
				<th scope="col"><?php echo JText::_('Title'); ?></th>
				<th scope="col"><?php echo JText::_('State'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php if (count($this->rows) > 0) : ?>
	<?php foreach ($this->rows as $page) : ?>
			<tr>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $this->escape($page['id']); ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $this->escape($page['id']); ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $this->escape($page['id']); ?>">
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
				<td colspan="2"><?php echo JText::_('Currently there are no pages for this offering.'); ?></td>
			</tr>
<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="offering" value="<?php echo $this->offering->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>
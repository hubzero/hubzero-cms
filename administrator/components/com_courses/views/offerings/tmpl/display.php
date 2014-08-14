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

$canDo = CoursesHelper::getActions();

JToolBarHelper::title(JText::_('COM_COURSES') . ': ' . JText::_('COM_COURSES_OFFERINGS'), 'courses.png');
if ($canDo->get('core.create'))
{
	JToolBarHelper::custom('copy', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
	JToolBarHelper::spacer();
	JToolBarHelper::addNew();
}
if ($canDo->get('core.edit'))
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.delete'))
{
	JToolBarHelper::deleteList('COM_COURSES_DELETE_CONFIRM', 'delete');
}
JToolBarHelper::spacer();
JToolBarHelper::help('offerings');

JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('adminForm');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_COURSES_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo JText::_('COM_COURSES_GO'); ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="10">
					(<?php echo $this->escape(stripslashes($this->course->get('alias'))); ?>)
					<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
				</th>
			</tr>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COURSES_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COURSES_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COURSES_COL_STARTS', 'publish_up', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COURSES_COL_ENDS', 'publish_down', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COURSES_COL_PUBLISHED', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_SECTIONS'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_ENROLLMENT'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_UNITS'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_PAGES'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$i = 0;
$k = 0;
foreach ($this->rows as $row)
{
	$units    = $row->units(array('count' => true));
	$students = 0;

	$s = $row->sections();
	if ($s->total() >  0)
	{
		$sids = array();
		foreach ($s as $section)
		{
			$sids[] = $section->get('id');
		}

		$students = $row->members(array(
						'count' => true,
						'student' => 1,
						'section_id' => $sids
					));
	}

	$pages    = $row->pages(array('count' => true, 'active' => array(0, 1)));
	$sections = $row->sections(array('count' => true));
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $this->escape($row->get('id')); ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->get('id'); ?>">
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</span>
				<?php } ?>
				</td>
				<td>
					<?php echo ($row->get('publish_up') && $row->get('publish_up') != '0000-00-00 00:00:00') ? JHTML::_('date', $row->get('publish_up'), JText::_('DATE_FORMAT_HZ1')) : JText::_('COM_COURSES_NO_DATE'); ?>
				</td>
				<td>
					<?php echo ($row->get('publish_down') && $row->get('publish_down') != '0000-00-00 00:00:00') ? JHTML::_('date', $row->get('publish_down'), JText::_('DATE_FORMAT_HZ1')) : JText::_('COM_COURSES_NEVER'); ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit.state')) { ?>
					<?php if ($row->get('state') == 1) { ?>
					<a class="jgrid" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=unpublish&amp;course=<?php echo $this->course->get('id'); ?>&amp;id=<?php echo $row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_COURSES_SET_TASK', JText::_('COM_COURSES_UNPUBLISHED')); ?>">
						<span class="state publish">
							<span class="text"><?php echo JText::_('COM_COURSES_PUBLISHED'); ?></span>
						</span>
					</a>
					<?php } else if ($row->get('state') == 2) { ?>
					<a class="jgrid" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=publish&amp;course=<?php echo $this->course->get('id'); ?>&amp;id=<?php echo $row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_COURSES_SET_TASK', JText::_('COM_COURSES_PUBLISHED')); ?>">
						<span class="state trash">
							<span class="text"><?php echo JText::_('COM_COURSES_TRASHED'); ?></span>
						</span>
					</a>
					<?php } else if ($row->get('state') == 3) { ?>
					<a class="jgrid" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=publish&amp;course=<?php echo $this->course->get('id'); ?>&amp;id=<?php echo $row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_COURSES_SET_TASK', JText::_('COM_COURSES_PUBLISHED')); ?>">
						<span class="state pending">
							<span class="text"><?php echo JText::_('COM_COURSES_DRAFT'); ?></span>
						</span>
					</a>
					<?php } else if ($row->get('state') == 0) { ?>
					<a class="jgrid" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=publish&amp;course=<?php echo $this->course->get('id'); ?>&amp;id=<?php echo $row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_COURSES_SET_TASK', JText::_('COM_COURSES_PUBLISHED')); ?>">
						<span class="state unpublish">
							<span class="text"><?php echo JText::_('COM_COURSES_UNPUBLISHED'); ?></span>
						</span>
					</a>
					<?php } ?>
				<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage') && $sections > 0) { ?>
						<a class="glyph category" href="index.php?option=<?php echo $this->option ?>&amp;controller=sections&amp;offering=<?php echo $row->get('id'); ?>">
							<?php echo $sections; ?>
						</a>
					<?php } else { ?>
						<span class="glyph category">
							<?php echo $sections; ?>
						</span>
						<?php if ($canDo->get('core.manage')) { ?>
						&nbsp;
						<a class="state add" href="index.php?option=<?php echo $this->option; ?>&amp;controller=sections&amp;offering=<?php echo $row->get('id'); ?>&amp;task=add">
							<span><?php echo JText::_('[ + ]'); ?></span>
						</a>
						<?php } ?>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage')) { ?>
						<a class="glyph member" href="index.php?option=<?php echo $this->option ?>&amp;controller=students&amp;offering=<?php echo $row->get('id'); ?>&amp;section=0">
							<?php echo $students; ?>
						</a>
					<?php } else { ?>
						<span class="glyph member">
							<?php echo $students; ?>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage') && $units > 0) { ?>
						<a class="glyph list" href="index.php?option=<?php echo $this->option; ?>&amp;controller=units&amp;offering=<?php echo $row->get('id'); ?>">
							<?php echo $units; ?>
						</a>
					<?php } else { ?>
						<?php echo $units; ?>
						<?php if ($canDo->get('core.manage')) { ?>
						&nbsp;
						<a class="state add" href="index.php?option=<?php echo $this->option; ?>&amp;controller=units&amp;offering=<?php echo $row->get('id'); ?>&amp;task=add">
							<span><?php echo JText::_('COM_COURSES_ADD'); ?></span>
						</a>
						<?php } ?>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage') && $pages > 0) { ?>
						<a class="glyph list" href="index.php?option=<?php echo $this->option; ?>&amp;controller=pages&amp;offering=<?php echo $row->get('id'); ?>">
							<?php echo $pages; ?>
						</a>
					<?php } else { ?>
						<?php echo $pages; ?>
						<?php if ($canDo->get('core.manage')) { ?>
						&nbsp;
						<a class="state add" href="index.php?option=<?php echo $this->option; ?>&amp;controller=pages&amp;course=<?php echo $this->course->get('id'); ?>&amp;offering=<?php echo $row->get('id'); ?>&amp;task=add">
							<span><?php echo JText::_('COM_COURSES_ADD'); ?></span>
						</a>
						<?php } ?>
					<?php } ?>
				</td>
			</tr>
<?php
	$k = 1 - $k;
	$i++;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="course" value="<?php echo $this->course->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
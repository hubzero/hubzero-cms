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

JToolBarHelper::title(JText::_('COM_COURSES'), 'courses.png');
if ($canDo->get('core.admin'))
{
	JToolBarHelper::preferences('com_courses', '550');
	JToolBarHelper::spacer();
}
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
	JToolBarHelper::deleteList();
}
JToolBarHelper::spacer();
JToolBarHelper::help('courses');

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
		<label for="filter_search"><?php echo JText::_('COM_COURSES_SEARCH'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_COURSES_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo JText::_('COM_COURSES_GO'); ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
		 	<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COURSES_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COURSES_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COURSES_COL_ALIAS', 'alias', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COURSES_PUBLISHED', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_CERTIFICATE'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_MANAGERS'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_OFFERINGS'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_PAGES'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$i = 0;
$k = 0;

foreach ($this->rows as $row)
{
	$offerings = $row->offerings(array('count' => true));
	$pages     = $row->pages(array('count' => true, 'active' => array(0, 1)));

	$params = new JRegistry($row->get('params'));
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('alias'); ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $this->escape($row->get('id')); ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->get('id'); ?>">
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</span>
				<?php } ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->get('id'); ?>">
						<?php echo $this->escape($row->get('alias')); ?>
					</a>
				<?php } else { ?>
					<?php echo $this->escape($row->get('alias')); ?>
				<?php } ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit.state')) { ?>
					<?php if ($row->get('state') == 1) { ?>
						<a class="jgrid" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=unpublish&amp;id[]=<?php echo $row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_COURSES_SET_TASK', JText::_('COM_COURSES_UNPUBLISHED')); ?>">
							<span class="state publish">
								<span class="text"><?php echo JText::_('COM_COURSES_PUBLISHED'); ?></span>
							</span>
						</a>
					<?php } else if ($row->get('state') == 2) { ?>
						<a class="jgrid" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=publish&amp;id[]=<?php echo $row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_COURSES_SET_TASK', JText::_('COM_COURSES_PUBLISHED')); ?>">
							<span class="state trash">
								<span class="text"><?php echo JText::_('COM_COURSES_TRASHED'); ?></span>
							</span>
						</a>
					<?php } else if ($row->get('state') == 3) { ?>
						<a class="jgrid" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=publish&amp;id[]=<?php echo $row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_COURSES_SET_TASK', JText::_('COM_COURSES_PUBLISHED')); ?>">
							<span class="state pending">
								<span class="text"><?php echo JText::_('COM_COURSES_DRAFT'); ?></span>
							</span>
						</a>
					<?php } else if ($row->get('state') == 0) { ?>
						<a class="jgrid" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=publish&amp;id[]=<?php echo $row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_COURSES_SET_TASK', JText::_('COM_COURSES_PUBLISHED')); ?>">
							<span class="state unpublish">
								<span class="text"><?php echo JText::_('COM_COURSES_UNPUBLISHED'); ?></span>
							</span>
						</a>
					<?php } ?>
				<?php } ?>
				</td>
				<td>
					<?php if ($row->certificate()->exists() && $row->certificate()->hasFile()) { ?>
						<a class="jgrid" href="index.php?option=<?php echo $this->option; ?>&amp;controller=certificates&amp;course=<?php echo $row->get('id'); ?>" title="<?php echo JText::_('COM_COURSES_CERTIFICATE_SET'); ?>">
							<span class="state yes">
								<span class="text"><?php echo JText::_('COM_COURSES_CERTIFICATE_SET'); ?></span>
							</span>
						</a>
					<?php } else { ?>
						<a class="jgrid" href="index.php?option=<?php echo $this->option; ?>&amp;controller=certificates&amp;course=<?php echo $row->get('id'); ?>" title="<?php echo JText::_('COM_COURSES_CERTIFICATE_NOT_SET'); ?>">
							<span class="state no">
								<span class="text"><?php echo JText::_('COM_COURSES_CERTIFICATE_NOT_SET'); ?></span>
							</span>
						</a>
					<?php } ?>
				</td>
				<td>
					<span class="glyph member">
						<?php echo $row->managers(array('count' => true)); ?>
					</span>
				</td>
				<td>
					<?php if ($canDo->get('core.manage') && $offerings > 0) { ?>
						<a class="glyph list" href="index.php?option=<?php echo $this->option; ?>&amp;controller=offerings&amp;course=<?php echo $row->get('id'); ?>">
							<?php echo $offerings; ?>
						</a>
					<?php } else { ?>
						<?php echo $offerings; ?>
						<?php if ($canDo->get('core.manage')) { ?>
							&nbsp;
							<a class="state add" href="index.php?option=<?php echo $this->option; ?>&amp;controller=offerings&amp;course=<?php echo $row->get('id'); ?>&amp;task=add">
								<span>[ + ]</span>
							</a>
						<?php } ?>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage') && $pages > 0) { ?>
						<a class="glyph list" href="index.php?option=<?php echo $this->option; ?>&amp;controller=pages&amp;course=<?php echo $row->get('id'); ?>&amp;offering=0">
							<?php echo $pages; ?>
						</a>
					<?php } else { ?>
						<?php echo $pages; ?>
						<?php if ($canDo->get('core.manage')) { ?>
							&nbsp;
							<a class="state add" href="index.php?option=<?php echo $this->option; ?>&amp;controller=pages&amp;course=<?php echo $row->get('id'); ?>&amp;offering=0&amp;task=add">
								<span>[ + ]</span>
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

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
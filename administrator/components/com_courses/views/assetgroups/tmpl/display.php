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

// Preprocess the list of items to find ordering divisions.
$this->ordering = array();
foreach ($this->rows as $row)
{
	$this->ordering[$row->get('parent')][] = $row->get('id');
}

$canDo = CoursesHelper::getActions('unit');

JToolBarHelper::title(JText::_('COM_COURSES').': <small><small>[ ' . JText::_('Asset groups') . ' ]</small></small>', 'courses.png');
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
	JToolBarHelper::deleteList('Are you sure you want to remove these items?', 'remove');
}
JToolBarHelper::spacer();
JToolBarHelper::help('assetgroups.html', true);

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
		<input type="text" name="search" id="filter_search" value="<?php echo $this->filters['search']; ?>" />

		<input type="submit" value="<?php echo JText::_('COM_COURSES_GO'); ?>" />
	</fieldset>
	<div class="clr"></div>
	
	<table class="adminlist" summary="<?php echo JText::_('COM_COURSES_TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th colspan="7">
					(<a href="index.php?option=<?php echo $this->option ?>&amp;controller=offerings&amp;course=<?php echo $this->course->get('id'); ?>">
						<?php echo $this->escape(stripslashes($this->course->get('alias'))); ?>
					</a>) 
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=offerings&amp;course=<?php echo $this->course->get('id'); ?>">
						<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
					</a>: 
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=units&amp;offering=<?php echo $this->unit->get('offering_id'); ?>">
						<?php echo $this->escape(stripslashes($this->offering->get('title'))); ?>
					</a>: 
					<?php echo $this->escape(stripslashes($this->unit->get('title'))); ?>
				</th>
			</tr>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col"><?php echo JText::_('ID'); ?></th>
				<th scope="col"><?php echo JText::_('Title'); ?></th>
				<th scope="col"><?php echo JText::_('Alias'); ?></th>
				<th scope="col"><?php echo JText::_('State'); ?></th>
				<th scope="col"><?php echo JText::_('Ordering'); ?></th>
				<th scope="col"><?php echo JText::_('Assets'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$i = 0;
$k = 0;
$n = count($this->rows);
$ordering = true;
foreach ($this->rows as $row)
{
	$orderkey	= array_search($row->get('id'), $this->ordering[$row->get('parent')]);

	$tip = '[coming soon]';
	//$assetgroups = $row->assetgroups()->total();
	$assets = $row->assets()->total();
	
	switch ($row->get('state'))
	{
		case 1:
			$class = 'publish';
			$task = 'unpublish';
			$alt = JText::_('COM_COURSES_PUBLISHED');
		break;
		case 2:
			$class = 'expire';
			$task = 'publish';
			$alt = JText::_('COM_COURSES_TRASHED');
		break;
		case 0:
			$class = 'unpublish';
			$task = 'publish';
			$alt = JText::_('COM_COURSES_UNPUBLISHED');
		break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $this->escape($row->get('id')); ?>
				</td>
				<td>
					<?php echo $row->treename; ?>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;unit=<?php echo $this->unit->get('id'); ?>&amp;id[]=<?php echo $row->get('id'); ?>">
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
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;unit=<?php echo $this->unit->get('id'); ?>&amp;id[]=<?php echo $row->get('id'); ?>">
						<?php echo $this->escape(stripslashes($row->get('alias'))); ?>
					</a>
<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->get('alias'))); ?>
					</span>
<?php } ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $class; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task;?>&amp;unit=<?php echo $this->unit->get('id'); ?>&amp;id[]=<?php echo $row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_COURSES_SET_TASK',$task);?>">
						<span><?php echo $alt; ?></span>
					</a>
<?php } else { ?>
					<span class="state <?php echo $class; ?>">
						<span><?php echo $alt; ?></span>
					</span>
<?php } ?>
				</td>
				<td class="order" style="whitespace:nowrap">
					<?php echo $row->treename; ?>
					<?php echo $row->get('ordering'); ?>
					<span><?php echo $this->pageNav->orderUpIcon( $i, isset($this->ordering[$row->get('parent')][$orderkey - 1]), 'orderup', 'Move Up', $ordering ); ?></span>
					<span><?php echo $this->pageNav->orderDownIcon( $i, $n, isset($this->ordering[$row->get('parent')][$orderkey + 1]), 'orderdown', 'Move Down', $ordering ); ?></span>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->get('id'); ?>">
						<?php echo $assets; ?>
					</a>
<?php } else { ?>
					<span>
						<?php echo $assets; ?>
					</span>
<?php } ?>
				</td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="offering" value="<?php echo $this->offering->get('id'); ?>" />
	<input type="hidden" name="unit" value="<?php echo $this->unit->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>
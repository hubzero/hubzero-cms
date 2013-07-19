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

$canDo = CoursesHelper::getActions('unit');

JToolBarHelper::title(JText::_('COM_COURSES').': <small><small>[ ' . JText::_('Units') . ' ]</small></small>', 'courses.png');
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
JToolBarHelper::spacer();
JToolBarHelper::help('units.html', true);

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
				<th colspan="11">
					(<a href="index.php?option=<?php echo $this->option ?>&amp;controller=offerings&amp;course=<?php echo $this->course->get('id'); ?>">
						<?php echo $this->escape(stripslashes($this->course->get('alias'))); ?>
					</a>) 
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=offerings&amp;course=<?php echo $this->course->get('id'); ?>">
						<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
					</a>: 
					<?php echo $this->escape(stripslashes($this->offering->get('title'))); ?>
				</th>
			</tr>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col"><?php echo JText::_('ID'); ?></th>
				<th scope="col"><?php echo JText::_('Title'); ?></th>
				<th scope="col"><?php echo JText::_('Alias'); ?></th>
				<th scope="col"><?php echo JText::_('State'); ?></th>
				<th scope="col" colspan="2"><?php echo JText::_('Ordering'); ?></th>
				<th scope="col"><?php echo JText::_('Asset groups'); ?></th>
				<th scope="col"><?php echo JText::_('Assets'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="11"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$i = 0;
$k = 0;
$n = $this->rows->total();
foreach ($this->rows as $row)
{
	$tip = '[coming soon]';
	$assetgroups = $row->assetgroups()->total();
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
<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->get('id'); ?>">
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
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->get('id'); ?>">
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
					<a class="state <?php echo $class; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task;?>&amp;offering=<?php echo $this->offering->get('id'); ?>&amp;id[]=<?php echo $row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_COURSES_SET_TASK',$task);?>">
						<span><?php echo $alt; ?></span>
					</a>
<?php } else { ?>
					<span class="state <?php echo $class; ?>">
						<span><?php echo $alt; ?></span>
					</span>
<?php } ?>
				</td>
				<td>
					<?php 
					$prv = $this->rows->fetch('prev');
					$prev = 0;
					if (is_object($prv))
					{
						$prev = $prv->get('ordering');
					}
					echo $this->pageNav->orderUpIcon( $i, ($row->get('ordering') != $prev) ); ?>
				</td>
				<td>
					<?php 
					$nxt = $this->rows->fetch('next');
					$next = 0;
					if (is_object($nxt))
					{
						$next = $nxt->get('ordering');
					}
					echo $this->pageNav->orderDownIcon( $i, $n, ($row->get('ordering') != $next) ); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage') && $assetgroups > 0) { ?>
						<a class="glyph list" href="index.php?option=<?php echo $this->option; ?>&amp;controller=assetgroups&amp;unit=<?php echo $row->get('id'); ?>">
							<?php echo $assetgroups; ?>
						</a>
					<?php } else { ?>
						<?php echo $assetgroups; ?>
						<?php if ($canDo->get('core.manage')) { ?>
						&nbsp;
						<a class="state add" href="index.php?option=<?php echo $this->option; ?>&amp;controller=assetgroups&amp;unit=<?php echo $row->get('id'); ?>&amp;task=add">
							<span><?php echo JText::_('[ + ]'); ?></span>
						</a>
						<?php } ?>
					<?php } ?>
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
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>
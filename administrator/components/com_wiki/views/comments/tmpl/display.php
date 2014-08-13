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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = WikiHelper::getActions('comment');

JToolBarHelper::title(JText::_('COM_WIKI') . ': ' . JText::_('COM_WIKI_PAGE') . ': ' . JText::_('COM_WIKI_COMMENTS'), 'wiki.png');

if ($canDo->get('core.delete'))
{
	JToolBarHelper::deleteList('', 'delete');
}
if ($canDo->get('core.edit'))
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.create'))
{
	JToolBarHelper::addNew();
}
JToolBarHelper::spacer();
JToolBarHelper::help('comments');

JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_WIKI_FILTER_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo JText::_('COM_WIKI_GO'); ?>" />
		<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="7">
					(<?php echo $this->escape(stripslashes($this->entry->get('pagename'))); ?>) &nbsp; <?php echo $this->escape(stripslashes($this->entry->get('title'))); ?>
				</th>
			</tr>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WIKI_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WIKI_COL_COMMENT', 'content', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WIKI_COL_CREATOR', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WIKI_COL_ANONYMOUS', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WIKI_COL_STATE', 'status', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WIKI_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;

$rows = $this->rows;
for ($i=0, $n=count($rows); $i < $n; $i++)
{
	$row =& $rows[$i];

	if (!$row->anonymous)
	{
		$calt2  = JText::_('JOFF');
		$cls2   = 'off';
		$state2 = 1;
	}
	else
	{
		$calt2  = JText::_('JON');
		$cls2   = 'on';
		$state2 = 0;
	}

	switch ($row->status)
	{
		case 2:
			$calt1  = JText::_('JTRASHED');
			$cls1   = 'trash';
			$state1 = 'publish';
		break;

		case 1:
			$calt1  = JText::_('COM_WIKI_STATE_ABUSIVE');
			$cls1   = 'unpublish';
			$state1 = 'publish';
		break;

		case 0:
		default:
			$calt1  = JText::_('JPUBLISHED');
			$cls1   = 'publish';
			$state1 = 'unpublish';
		break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<?php echo $row->treename; ?>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>">
						<?php echo \Hubzero\Utility\String::truncate($this->escape(stripslashes($row->ctext)), 90); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo \Hubzero\Utility\String::truncate($this->escape(stripslashes($row->ctext)), 90); ?>
					</span>
				<?php } ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->name)); ?>
				</td>
				<td>
					<a class="state <?php echo $cls2; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=anonymous&amp;state=<?php echo $state2; ?>&amp;id=<?php echo $row->id; ?>&amp;pageid=<?php echo $this->filters['pageid']; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><?php echo $calt2; ?></span>
					</a>
				</td>
				<td>
					<a class="state <?php echo $cls1; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $state1; ?>&amp;id=<?php echo $row->id; ?>&amp;pageid=<?php echo $this->filters['pageid']; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><?php echo $calt1; ?></span>
					</a>
				</td>
				<td>
					<time datetime="<?php echo $row->created; ?>">
						<?php echo $row->created; ?>
					</time>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="pageid" value="<?php echo $this->filters['pageid']; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>

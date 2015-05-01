<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = \Components\Wiki\Helpers\Permissions::getActions('comment');

Toolbar::title(Lang::txt('COM_WIKI') . ': ' . Lang::txt('COM_WIKI_PAGE') . ': ' . Lang::txt('COM_WIKI_COMMENTS'), 'wiki.png');

if ($canDo->get('core.delete'))
{
	Toolbar::deleteList('', 'delete');
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
Toolbar::spacer();
Toolbar::help('comments');

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

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_WIKI_FILTER_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo Lang::txt('COM_WIKI_GO'); ?>" />
		<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
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
				<th scope="col" class="priority-5"><?php echo $this->grid('sort', 'COM_WIKI_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_WIKI_COL_COMMENT', 'content', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo $this->grid('sort', 'COM_WIKI_COL_CREATOR', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo $this->grid('sort', 'COM_WIKI_COL_ANONYMOUS', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo $this->grid('sort', 'COM_WIKI_COL_STATE', 'status', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_WIKI_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php
				// Initiate paging
				echo $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				?></td>
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
		$calt2  = Lang::txt('JOFF');
		$cls2   = 'off';
		$state2 = 1;
	}
	else
	{
		$calt2  = Lang::txt('JON');
		$cls2   = 'on';
		$state2 = 0;
	}

	switch ($row->status)
	{
		case 2:
			$calt1  = Lang::txt('JTRASHED');
			$cls1   = 'trash';
			$state1 = 'publish';
		break;

		case 1:
			$calt1  = Lang::txt('COM_WIKI_STATE_ABUSIVE');
			$cls1   = 'unpublish';
			$state1 = 'publish';
		break;

		case 0:
		default:
			$calt1  = Lang::txt('JPUBLISHED');
			$cls1   = 'publish';
			$state1 = 'unpublish';
		break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td class="priority-5">
					<?php echo $row->id; ?>
				</td>
				<td>
					<?php echo $row->treename; ?>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
							<?php echo \Hubzero\Utility\String::truncate($this->escape(stripslashes($row->ctext)), 90); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo \Hubzero\Utility\String::truncate($this->escape(stripslashes($row->ctext)), 90); ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-3">
					<?php echo $this->escape(stripslashes($row->name)); ?>
				</td>
				<td class="priority-5">
					<a class="state <?php echo $cls2; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=anonymous&state=' . $state2 . '&id=' . $row->id . '&pageid=' . $this->filters['pageid'] . '&' . JUtility::getToken() . '=1'); ?>">
						<span><?php echo $calt2; ?></span>
					</a>
				</td>
				<td class="priority-2">
					<a class="state <?php echo $cls1; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $state1 . '&id=' . $row->id . '&pageid=' . $this->filters['pageid'] . '&' . JUtility::getToken() . '=1'); ?>">
						<span><?php echo $calt1; ?></span>
					</a>
				</td>
				<td class="priority-4">
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

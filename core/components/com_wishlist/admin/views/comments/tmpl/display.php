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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Wishlist\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_WISHLIST') . ': ' . Lang::txt('COM_WISHLIST_COMMENTS'), 'wishlist.png');
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::spacer();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('comments');
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
		<label for="filter_search"><?php echo Lang::txt('COM_WISHLIST_SEARCH'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_WISHLIST_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo Lang::txt('COM_WISHLIST_GO'); ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
		<?php if ($this->filters['wish'] > 0) { ?>
			<tr>
				<th colspan="7">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=wishes&wishlist=' . $this->wishlist->id); ?>">
						(<?php echo $this->escape(stripslashes($this->wishlist->category)); ?>) &nbsp;
						<?php echo $this->escape(stripslashes($this->wishlist->title)); ?> &nbsp;&rsaquo;&nbsp;
					</a>
					<?php echo $this->escape(stripslashes($this->wish->subject)); ?>
				</th>
			</tr>
		<?php } ?>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col" class="priority-5"><?php echo $this->grid('sort', 'COM_WISHLIST_COMMENT_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_WISHLIST_COMMENT', 'comment', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_WISHLIST_ADDED_BY', 'added_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo $this->grid('sort', 'COM_WISHLIST_ADDED', 'added', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_WISHLIST_STATE', 'status', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo $this->grid('sort', 'COM_WISHLIST_ANONYMOUS', 'anonymous', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row =& $this->rows[$i];
	switch ($row->state)
	{
		case 1:
			$class = 'publish';
			$task = 'unpublish';
			$alt = Lang::txt('COM_WISHLIST_PUBLISHED');
		break;
		case 2:
			$class = 'trash';
			$task = 'publish';
			$alt = Lang::txt('COM_WISHLIST_TRASHED');
		break;
		case 0:
			$class = 'unpublish';
			$task = 'publish';
			$alt = Lang::txt('COM_WISHLIST_UNPUBLISHED');
		break;
	}

	if ($row->anonymous)
	{
		$aclass = 'publish';
		$atask = 'publicize';
		$aalt = Lang::txt('COM_WISHLIST_ANONYMOUS');
	}
	else
	{
		$aclass = 'unpublish';
		$atask = 'anonymize';
		$aalt = Lang::txt('COM_WISHLIST_NOT_ANONYMOUS');
	}

	$comment = substr(strip_tags(stripslashes($row->content)), 0, 50);
	if (strlen($row->content) >= 50)
	{
		$comment .= '...';
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td class="priority-5">
					<?php echo $row->id; ?>
				</td>
				<td>
					<?php echo $row->prfx; ?>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id . '&wish=' . $row->wish); ?>">
							<span><?php echo $this->escape($comment); ?></span>
						</a>
					<?php } else { ?>
						<span>
							<span><?php echo $this->escape($comment); ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php echo $this->escape(stripslashes($row->name)); ?>
				</td>
				<td class="priority-3">
					<time datetime="<?php echo $row->created; ?>"><?php echo $row->created; ?></time>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $class; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->id . '&wish=' . $this->filters['wish'] . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_WISHLIST_SET_TASK', $task);?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $class; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-2">
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="<?php echo $aclass; ?> state" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $atask . '&id=' . $row->id . '&wish=' . $row->wish . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo $aalt; ?>">
							<span><?php echo $aalt; ?></span>
						</a>
					<?php } else { ?>
						<span class="<?php echo $aclass; ?> state">
							<span><?php echo $aalt; ?></span>
						</span>
					<?php } ?>
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
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
	<input type="hidden" name="wish" value="<?php echo $this->filters['wish']; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>

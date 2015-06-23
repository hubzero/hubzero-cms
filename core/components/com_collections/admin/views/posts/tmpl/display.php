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

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Collections\Helpers\Permissions::getActions('post');

Toolbar::title(Lang::txt('COM_COLLECTIONS') . ': ' . Lang::txt('COM_COLLECTIONS_POSTS'), 'collection.png');
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
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_COLLECTIONS_FILTER_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo Lang::txt('COM_COLLECTIONS_GO'); ?>" />
		<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>

		<input type="hidden" name="collection_id" value="<?php echo $this->filters['collection_id']; ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<?php if ($this->filters['collection_id']) { ?>
				<tr>
					<th colspan="6">
						<?php $collection = new \Components\Collections\Models\Collection($this->filters['collection_id']); ?>
						(<?php echo $this->escape(stripslashes($collection->get('alias'))); ?>)
						<?php echo $this->escape(stripslashes($collection->get('title'))); ?>
					</th>
				</tr>
			<?php } ?>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->rows->total(); ?>);" /></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_COLLECTIONS_COL_DESCRIPTION', 'description', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo $this->grid('sort', 'COM_COLLECTIONS_COL_POSTED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo $this->grid('sort', 'COM_COLLECTIONS_COL_POSTEDBY', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo $this->grid('sort', 'COM_COLLECTIONS_COL_ITEM_ID', 'item_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			<?php if (!$this->filters['collection_id']) { ?>
				<th scope="col" class="priority-2"><?php echo $this->grid('sort', 'COM_COLLECTIONS_COL_COLLECTION_ID', 'collection_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			<?php } ?>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_COLLECTIONS_COL_ORIGINAL', 'original', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo ($this->filters['collection_id'] ? '6' : '7'); ?>">
					<?php
					echo $this->pagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$i = 0;
foreach ($this->rows as $row)
{
	switch ($row->get('original'))
	{
		case 1:
			$class = 'yes';
			$task = 'unoriginal';
			$alt = Lang::txt('COM_COLLECTIONS_IS_ORIGINAL');
		break;

		case 0:
			$class = 'no';
			$task = 'original';
			$alt = Lang::txt('COM_COLLECTIONS_IS_NOT_ORIGINAL');
		break;
	}

	if (!($content = $row->description('clean', 75)))
	{
		$content = Lang::txt('COM_COLLECTIONS_NONE');
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
						<span><?php echo $content; ?></span>
					</a>
				<?php } else { ?>
					<span>
						<span><?php echo $content; ?></span>
					</span>
				<?php } ?>
				</td>
				<td class="priority-5">
					<time datetime="<?php echo $row->get('created'); ?>"><?php echo $row->get('created'); ?></time>
				</td>
				<td class="priority-3">
					<span class="glyph member">
						<?php echo $this->escape($row->creator('name')); ?>
					</span>
				</td>
				<td class="priority-2">
					<?php echo $this->escape($row->get('item_id')); ?>
				</td>
			<?php if (!$this->filters['collection_id']) { ?>
				<td class="priority-2">
					<?php echo $this->escape($row->get('collection_id')); ?>
				</td>
			<?php } ?>
				<td class="priority-4">
				<?php /*if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $class; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id')); ?>" title="<?php echo Lang::txt('COM_COLLECTIONS_SET_TASK', $task);?>">
						<span><?php echo $alt; ?></span>
					</a>
				<?php } else {*/ ?>
					<span class="state <?php echo $class; ?>">
						<span><?php echo $alt; ?></span>
					</span>
				<?php //} ?>
				</td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>
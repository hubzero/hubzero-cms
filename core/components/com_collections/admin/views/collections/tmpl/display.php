<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Collections\Helpers\Permissions::getActions('collection');

Toolbar::title(Lang::txt('COM_COLLECTIONS'), 'collections');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
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
Toolbar::help('collections');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_COLLECTIONS_FILTER_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_COLLECTIONS_GO'); ?>" />
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span6">
				<label for="filter-state"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_STATE'); ?>:</label>
				<select name="state" id="filter-state" class="filter filter-submit">
					<option value="-1"<?php if ($this->filters['state'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COLLECTIONS_ALL_STATES'); ?></option>
					<option value="0"<?php if ($this->filters['state'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->filters['state'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					<option value="2"<?php if ($this->filters['state'] == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
				</select>

				<label for="filter-access"><?php echo Lang::txt('JFIELD_ACCESS_LABEL'); ?>:</label>
				<select name="access" id="filter-access" class="filter filter-submit">
					<option value="-1"><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
					<?php //echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->filters['access']); ?>
					<option value="0"<?php if ($this->filters['access'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COLLECTIONS_ACCESS_PUBLIC'); ?></option>
					<option value="1"<?php if ($this->filters['access'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COLLECTIONS_ACCESS_REGISTERED'); ?></option>
					<option value="4"<?php if ($this->filters['access'] == 4) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COLLECTIONS_ACCESS_PRIVATE'); ?></option>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_COLLECTIONS_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_COLLECTIONS_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_COLLECTIONS_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_COLLECTIONS_COL_ACCESS', 'access', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_COLLECTIONS_COL_OWNER', 'object_type', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_COLLECTIONS_COL_POSTS', 'posts', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php
					echo $this->rows->pagination;
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
			switch ($row->get('state'))
			{
				case 1:
					$class = 'publish';
					$task = 'unpublish';
					$alt = Lang::txt('JPUBLISHED');
					break;
				case 2:
					$class = 'trash';
					$task = 'publish';
					$alt = Lang::txt('JTRASHED');
					break;
				case 0:
					$class = 'unpublish';
					$task = 'publish';
					$alt = Lang::txt('JUNPUBLISHED');
					break;
			}

			switch ($row->get('access', 0))
			{
				case 0:
					$color_access = 'public';
					$task_access = 'accessregistered';
					$row->set('groupname', Lang::txt('COM_COLLECTIONS_ACCESS_PUBLIC'));
				break;
				case 1:
					$color_access = 'registered';
					//$task_access = 'accessspecial';
					$task_access = 'accessprivate';
					$row->set('groupname', Lang::txt('COM_COLLECTIONS_ACCESS_REGISTERED'));
				break;
				/*case 2:
					$color_access = 'special';
					$task_access = 'accessprivate';
					$row->set('groupname', Lang::txt('COM_COLLECTIONS_ACCESS_SPECIAL'));
				break;*/
				case 4:
					$color_access = 'private';
					$task_access = 'accesspublic';
					$row->set('groupname', Lang::txt('COM_COLLECTIONS_ACCESS_PRIVATE'));
				break;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
					<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo $row->get('id'); ?></label>
				</td>
				<td class="priority-4">
					<?php echo $row->get('id'); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a class="glyph category" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
						</a>
					<?php } else { ?>
						<span class="glyph category">
							<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-2">
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $class; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COLLECTIONS_SET_TASK', $task);?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $class; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task_access . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" class="access <?php echo $color_access; ?>" title="<?php echo Lang::txt('COM_COLLECTIONS_CHANGE_ACCESS'); ?>">
							<span><?php echo $row->get('groupname'); ?></span>
						</a>
					<?php } else { ?>
						<span class="access <?php echo $color_access; ?>">
							<span><?php echo $row->get('groupname'); ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-3">
					<span class="scope">
						<span><?php echo $this->escape($row->get('object_type')) . ' (' . $this->escape($row->get('object_id')) . ')'; ?></span>
					</span>
				</td>
				<td>
					<?php
					$posts = $row->posts()->total();
					if ($posts > 0) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=posts&collection_id=' . $row->get('id')); ?>">
							<span><?php echo Lang::txt('COM_COLLECTIONS_NUM_POSTS', $posts); ?></span>
						</a>
					<?php } else { ?>
						<span>
							<span><?php echo Lang::txt('COM_COLLECTIONS_NUM_POSTS', $posts); ?></span>
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

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Story\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_STORY_SECTIONS'), 'story.png');
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}

Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_STORIES'), Route::url('index.php?option=' . $this->option . '&controller=stories'));
Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_SECTIONS'), Route::url('index.php?option=' . $this->option . '&controller=sections'), true);
Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_TOPICS'), Route::url('index.php?option=' . $this->option . '&controller=topics'), false);
Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_DISCUSSIONS'), Route::url('index.php?option=' . $this->option . '&controller=comments'));
Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_SUBMISSIONS'), Route::url('index.php?option=' . $this->option . '&controller=submissions'));

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=sections'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" />
		<input type="submit" value="<?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?>" />
		<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STORY_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col">
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STORY_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STORY_COL_ALIAS', 'alias', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_STORY_COL_ORDERING', 'ordering', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STORY_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
$i = 0;
foreach ($this->rows as $row)
{
	?>
			<tr class="<?php echo 'row' . ($i % 2); ?>">
				<td><?php echo $this->escape($row->get('id')); ?></td>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $this->escape($row->get('id')); ?>" class="checkbox-toggle" />
					<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo Lang::txt('JSELECT'); ?></label>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=sections&task=edit&id[]=' . $row->get('id')); ?>"><?php echo $this->escape($row->get('title')); ?></a>
<?php } else { ?>
					<?php echo $this->escape($row->get('title')); ?>
<?php } ?>
				</td>
				<td><code><?php echo $this->escape($row->get('alias')); ?></code></td>
				<td class="priority-3"><?php echo (int) $row->get('ordering'); ?></td>
				<td>
					<a class="state <?php echo ($row->get('state') ? 'publish' : 'unpublish'); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=sections&task=' . ($row->get('state') ? 'unpublish' : 'publish') . '&id[]=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo Lang::txt($row->get('state') ? 'JPUBLISHED' : 'JUNPUBLISHED'); ?></span>
					</a>
				</td>
			</tr>
	<?php
	$i++;
}
?>
		</tbody>
	</table>

	<?php echo $this->rows->pagination; ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="sections" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>

<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('resource');

Toolbar::title(Lang::txt('COM_RESOURCES_IMPORT_TITLE_IMPORTS'), 'import');

if ($canDo->get('core.create'))
{
	Toolbar::custom('run', 'script', 'script', 'COM_RESOURCES_RUN');
	Toolbar::custom('runtest', 'runtest', 'script', 'COM_RESOURCES_TEST_RUN');
	Toolbar::spacer();
	Toolbar::addNew();
	Toolbar::editList();
	Toolbar::deleteList();
}

Toolbar::spacer();
Toolbar::help('import');

$this->css('import');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_RESOURCES_IMPORT_DISPLAY_FIELD_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_RESOURCES_IMPORT_DISPLAY_FIELD_NUMRECORDS', 'count', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_RESOURCES_IMPORT_DISPLAY_FIELD_CREATED', 'created_at', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_RESOURCES_IMPORT_DISPLAY_FIELD_LASTRUN', 'ran_at', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_FIELD_RUNCOUNT'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php
				// Initiate paging
				echo $this->imports->pagination;
				?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php if ($this->imports->count() > 0) : ?>
				<?php foreach ($this->imports as $i => $import) : ?>
					<tr>
						<td>
							<?php if ($canDo->get('core.create')): ?>
								<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $import->get('id'); ?>" class="checkbox-toggle" />
							<?php endif; ?>
						</td>
						<td>
							<?php if ($canDo->get('core.create')): ?>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $import->get('id')); ?>">
									<?php echo $this->escape($import->get('name')); ?>
								</a>
							<?php else: ?>
								<?php echo $this->escape($import->get('name')); ?>
							<?php endif; ?>
							<br />
							<span class="hint">
								<?php echo nl2br($this->escape($import->get('notes'))); ?>
							</span>
						</td>
						<td>
							<?php
							echo number_format((int)$import->get('count', 0));
							?>
						</td>
						<td class="priority-4">
							<strong><?php echo Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_ON'); ?></strong>
							<?php
							$created_on = Date::of($import->get('created_at'))->toLocal('m/d/Y @ g:i a');
							echo $created_on . '<br />';
							?>
							<strong><?php echo Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_BY'); ?></strong>
							<?php
							$created_by = User::getInstance($import->get('created_by'));
							echo $created_by->get('name', Lang::txt('COM_RESOURCE_UNKNOWN'));
							?>
						</td>
						<td class="priority-3">
							<?php
							$runs = $import->runs()
								->whereEquals('dry_run', 0)
								->order('id', 'desc')
								->rows();

							$lastRun = $runs
								->first();
							?>
							<?php if ($lastRun) : ?>
								<strong><?php echo Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_ON'); ?></strong>
								<?php
									$created_on = Date::of($lastRun->get('ran_at'))->toLocal('m/d/Y @ g:i a');
									echo $created_on . '<br />';
								?>
								<strong><?php echo Lang::txt('COM_RESOURCES_IMPORT_DISPLAY_BY'); ?></strong>
								<?php
									if ($created_by = User::getInstance($lastRun->get('ran_by')))
									{
										echo $created_by->get('name');
									}
								?>
							<?php else: ?>
								n/a
							<?php endif; ?>
						</td>
						<td class="priority-2">
							<?php
								echo $runs->count();
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="6"><?php echo Lang::txt('COM_RESOURCES_IMPORT_NONE'); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
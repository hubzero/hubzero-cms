<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_PROJECTS') . ': ' . Lang::txt('COM_PROJECTS_ACTIVITY'), 'projects');
if (User::authorise('core.delete', $this->option . '.component'))
{
	Toolbar::deleteList('COM_PROJECTS_ACTIVITY_DELETE', 'delete');
	Toolbar::spacer();
}
Toolbar::help('activity');

Html::behavior('tooltip');
Html::behavior('modal');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span4">
				<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_PROJECTS_FILTER_SEARCH_DESC'); ?>" />

				<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span8">
				<label for="filter_action"><?php echo Lang::txt('COM_PROJECTS_FILTER_ACTION'); ?>:</label>
				<select name="action" class="inputbox filter filter-submit" id="filter_action">
					<option value=""><?php echo Lang::txt('COM_PROJECTS_FILTER_ACTION'); ?></option>
					<option value="created"<?php if ($this->filters['action'] == 'created') { echo ' selected="selected"'; } ?>>created</option>
					<option value="updated"<?php if ($this->filters['action'] == 'updated') { echo ' selected="selected"'; } ?>>updated</option>
					<option value="deleted"<?php if ($this->filters['action'] == 'deleted') { echo ' selected="selected"'; } ?>>deleted</option>
					<option value="joined"<?php if ($this->filters['action'] == 'joined') { echo ' selected="selected"'; } ?>>joined</option>
					<option value="uploaded"<?php if ($this->filters['action'] == 'uploaded') { echo ' selected="selected"'; } ?>>uploaded</option>
					<option value="accepted"<?php if ($this->filters['action'] == 'accepted') { echo ' selected="selected"'; } ?>>accepted</option>
					<option value="cancelled"<?php if ($this->filters['action'] == 'cancelled') { echo ' selected="selected"'; } ?>>cancelled</option>
					<option value="submitted"<?php if ($this->filters['action'] == 'submitted') { echo ' selected="selected"'; } ?>>submitted</option>
					<option value="emailed"<?php if ($this->filters['action'] == 'emailed') { echo ' selected="selected"'; } ?>>emailed</option>
				</select>

				<label for="filter_filter"><?php echo Lang::txt('COM_PROJECTS_FILTER_FILTER'); ?>:</label>
				<select name="filter" class="inputbox filter filter-submit" id="filter_filter">
					<option value=""><?php echo Lang::txt('COM_PROJECTS_FILTER_FILTER'); ?></option>
					<option value="starred"<?php if ($this->filters['filter'] == 'starred') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_PROJECTS_FILTER_STARRED'); ?></option>
				</select>

				<label for="filter_state"><?php echo Lang::txt('COM_PROJECTS_FIELD_STATE'); ?>:</label>
				<select name="state" id="filter_state" class="inputbox filter filter-submit">
					<option value="-1"<?php if ($this->filters['state'] == '-1') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_PROJECTS_ALL_STATES'); ?></option>
					<option value="0"<?php if ($this->filters['state'] === 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->filters['state'] === 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					<option value="2"<?php if ($this->filters['state'] === 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
				</select>
			</div>
		</div>
	</fieldset>
	<table class="adminlist">
		<thead>
		<?php if ($this->filters['project']) { ?>
			<tr>
				<th colspan="8"><a href="<?php echo Route::url('index.php?option=' . $this->option); ?>"><?php echo Lang::txt('COM_PROJECTS'); ?></a> > (<?php echo $this->escape(stripslashes($this->project->get('alias'))); ?>) <?php echo $this->escape(stripslashes($this->project->get('title'))); ?></th>
			</tr>
		<?php } ?>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-6"><?php echo Html::grid('sort', 'COM_PROJECTS_ACTIVITY_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_PROJECTS_ACTIVITY_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_PROJECTS_ACTIVITY_CREATED_BY', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_PROJECTS_ACTIVITY_ACTION', 'action', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_PROJECTS_ACTIVITY_DESCRIPTION', 'description', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_PROJECTS_ACTIVITY_PROJECT'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_PROJECTS_ACTIVITY_STATE'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_PROJECTS_ACTIVITY_STARRED'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php
				echo $this->rows->pagination;
				?></td>
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
				case '2':
					$task = 'publish';
					$alt = Lang::txt('JTRASHED');
					$cls = 'trash';
				break;
				case '1':
					$task = 'unpublish';
					$alt = Lang::txt('JPUBLISHED');
					$cls = 'publish';
				break;
				case '0':
				default:
					$task = 'publish';
					$alt = Lang::txt('JUNPUBLISHED');
					$cls = 'unpublish';
				break;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" class="checkbox-toggle" />
				</td>
				<td class="priority-6">
					<?php
					echo $this->escape($row->get('id'));
					?>
				</td>
				<td class="priority-4">
					<?php
					echo $this->escape($row->get('created'));
					?>
				</td>
				<td class="priority-3">
					<?php
					$creator = User::getInstance($row->log->get('created_by'));
					echo $this->escape(stripslashes($creator->get('name', Lang::txt('COM_PROJECTS_UNKNOWN'))));
					?>
				</td>
				<td class="priority-3">
					<?php echo $this->escape($row->log->get('action')); ?>
				</td>
				<td>
					<?php if (strpos($row->log->get('scope'), '.comment')): ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape(Hubzero\Utility\Str::truncate(strip_tags($row->log->get('description')), 100)); ?>
						</a>
					<?php else: ?>
						<?php echo $this->escape(Hubzero\Utility\Str::truncate(strip_tags($row->log->get('description')), 100)); ?>
					<?php endif; ?>
				</td>
				<td class="priority-2">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=projects&task=edit&id=' . $row->get('scope_id')); ?>">
						<?php
						$model = new Components\Projects\Models\Project($row->get('scope_id'));
						echo $this->escape($model->get('alias')); ?>
					</a>
				</td>
				<td class="priority-2">
					<?php if (User::authorise('core.edit.state', $this->option . '.component')): ?>
						<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_FORUM_SET_TO', $task); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php else: ?>
						<span class="state <?php echo $cls; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php endif; ?>
				</td>
				<td class="priority-2">
					<?php if ($row->get('starred')): ?>
						<?php if (User::authorise('core.edit.state', $this->option . '.component')): ?>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=unstar&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>">
						<?php endif; ?>
						<span class="state default"><span class="text"><?php echo Lang::txt('JYES'); ?></span></span>
						<?php if (User::authorise('core.edit.state', $this->option . '.component')): ?>
							</a>
						<?php endif; ?>
					<?php else: ?>
						<?php if (User::authorise('core.edit.state', $this->option . '.component')): ?>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=star&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>">
						<?php endif; ?>
						<span class="state notdefault"><span class="text"><?php echo Lang::txt('JNO'); ?></span></span>
						<?php if (User::authorise('core.edit.state', $this->option . '.component')): ?>
							</a>
						<?php endif; ?>
					<?php endif; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
			$i++;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
	<?php echo Html::input('token'); ?>
</form>
<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Answers\Helpers\Permissions::getActions('question');

Toolbar::title(Lang::txt('COM_ANSWERS_TITLE') . ': ' . Lang::txt('COM_ANSWERS_QUESTIONS'), 'answers');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList('COM_ANSWERS_CONFIRM_DELETE');
}
Toolbar::spacer();
Toolbar::help('questions');

?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_ANSWERS_FILTER_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_ANSWERS_GO'); ?>" />
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span6 rtl">
				<label for="filter-state"><?php echo Lang::txt('COM_ANSWERS_FILTER_BY'); ?></label>
				<select name="state" id="filter-state" class="filter filter-submit">
					<option value="0"<?php if ($this->filters['state'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_ANSWERS_FILTER_BY_OPEN'); ?></option>
					<option value="1"<?php if ($this->filters['state'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_ANSWERS_FILTER_BY_CLOSED'); ?></option>
					<option value="-1"<?php if ($this->filters['state'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_ANSWERS_FILTER_BY_ALL'); ?></option>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_SUBJECT', 'subject', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_CREATOR', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-1"><?php echo Lang::txt('COM_ANSWERS_COL_ANSWERS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php
				// Initiate paging
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
				case 2:
					$task = 'open';
					$alt = Lang::txt('COM_ANSWERS_STATE_TRASHED');
					$cls = 'trashed';
				break;
				case 1:
					$task = 'open';
					$alt = Lang::txt('COM_ANSWERS_STATE_CLOSED');
					$cls = 'unpublished';
				break;
				case 0:
					$task = 'close';
					$alt = Lang::txt('COM_ANSWERS_STATE_OPEN');
					$cls = 'published';
				break;
			}

			$comments = $row->responses->count();
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
				</td>
				<td class="priority-4">
					<?php echo $row->get('id'); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<span><?php echo $this->escape(strip_tags($row->get('subject'))); ?></span>
						</a>
					<?php } else { ?>
						<span>
							<span><?php echo $this->escape(strip_tags($row->get('subject'))); ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-2">
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_ANSWERS_SET_STATE', $task); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $cls; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-4">
					<time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time>
				</td>
				<td class="priority-3">
					<a class="glyph user" href="<?php echo Route::url('index.php?option=com_members&controller=members&task=edit&id=' . $row->get('created_by')); ?>">
						<?php echo $this->escape($row->creator->get('name')) . ' (' . $row->get('created_by') . ')'; ?>
					</a>
					<?php if ($row->get('anonymous')) { ?>
						<br /><span>(<?php echo Lang::txt('COM_ANSWERS_FIELD_ANONYMOUS'); ?>)</span>
					<?php } ?>
				</td>
				<td class="priority-1">
					<?php if ($comments > 0) { ?>
						<a class="glyph comment" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=answers&qid=' . $row->get('id')); ?>">
							<span><?php echo Lang::txt('COM_ANSWERS_NUM_RESPONSES', $comments); ?></span>
						</a>
					<?php } else { ?>
						<a class="glyph comment" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=answers&qid=' . $row->get('id')); ?>">
							<span>0</span>
						</a>
					<?php } ?>
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
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
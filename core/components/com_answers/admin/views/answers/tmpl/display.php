<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Answers\Helpers\Permissions::getActions('answer');

Toolbar::title(Lang::txt('COM_ANSWERS_TITLE') . ': ' . Lang::txt('COM_ANSWERS_RESPONSES'), 'answers');
if ($canDo->get('core.create') && $this->filters['question_id'])
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
	Toolbar::spacer();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList('COM_ANSWERS_CONFIRM_DELETE');
	Toolbar::spacer();
}
Toolbar::help('responses');

?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter-state"><?php echo Lang::txt('COM_ANSWERS_FILTER_BY'); ?></label>
		<select name="state" id="filter-state" class="filter filter-submit">
			<option value="-1"<?php if ($this->filters['state'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_ANSWERS_FILTER_BY_ALL_RESPONSES'); ?></option>
			<option value="1"<?php if ($this->filters['state'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_ANSWERS_FILTER_BY_ACCEPTED'); ?></option>
			<option value="0"<?php if ($this->filters['state'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_ANSWERS_FILTER_BY_UNACCEPTED'); ?></option>
		</select>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="7">
					<?php if ($this->question->get('id')) { ?>
						#<?php echo $this->escape(stripslashes($this->question->get('id'))); ?> -
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=questions&task=edit&id=' . $this->question->get('id')); ?>">
							<?php echo $this->escape(strip_tags($this->question->get('subject'))); ?>
						</a>
					<?php } else { ?>
						<?php echo Lang::txt('COM_ANSWERS_RESPONSES_TO_ALL'); ?>
					<?php } ?>
				</th>
			</tr>
			<tr>
				<th scope="col">
					<input type="checkbox" name="toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_ANSWER', 'answer', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_ACCEPTED', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_CREATOR', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_VOTES', 'helpful', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php
				// initiate paging
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
			switch (intval($row->get('state')))
			{
				case 1:
					$task = 'reject';
					$alt = Lang::txt('COM_ANSWERS_STATE_ACCEPTED');
					$cls = 'published';
				break;

				case 0:
				default:
					$task = 'accept';
					$alt = Lang::txt('COM_ANSWERS_STATE_UNACCEPTED');
					$cls = 'unpublished';
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
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id') . '&qid=' . $this->question->get('id')); ?>">
							<span><?php echo $this->truncate(strip_tags($row->get('answer')), 75); ?></span>
						</a>
					<?php } else { ?>
						<span>
							<span><?php echo $this->truncate(strip_tags($row->get('answer')), 75); ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&qid=' . $this->question->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_ANSWERS_SET_STATE', $task); ?>">
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
						<span><?php echo $this->escape($row->creator->get('name')) . ' (' . $row->get('created_by') . ')'; ?></span>
					</a>
					<?php if ($row->get('anonymous')) { ?>
						<br /><span>(<?php echo Lang::txt('JANONYMOUS'); ?>)</span>
					<?php } ?>
				</td>
				<td class="priority-4">
					<span class="vote like">+<?php echo $row->get('helpful', 0); ?></span>
					<span class="vote dislike">-<?php echo $row->get('nothelpful', 0); ?></span>
				</td>
			</tr>
			<?php
			$i++;
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="qid" value="<?php echo $this->question->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Answers\Helpers\Permissions::getActions('answer');

Toolbar::title(Lang::txt('COM_ANSWERS_TITLE') . ': ' . Lang::txt('COM_ANSWERS_RESPONSES'), 'answers.png');
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
	Toolbar::deleteList();
	Toolbar::spacer();
}
Toolbar::help('responses');

?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter-state"><?php echo Lang::txt('COM_ANSWERS_FILTER_BY'); ?></label>
		<select name="state" id="filter-state" onchange="document.adminForm.submit();">
			<option value="-1"<?php if ($this->filters['state'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_ANSWERS_FILTER_BY_ALL_RESPONSES'); ?></option>
			<option value="1"<?php if ($this->filters['state'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_ANSWERS_FILTER_BY_ACCEPTED'); ?></option>
			<option value="0"<?php if ($this->filters['state'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_ANSWERS_FILTER_BY_UNACCEPTED'); ?></option>
		</select>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="6">
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
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->rows->count(); ?>);" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_ANSWER', 'answer', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_ACCEPTED', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_CREATOR', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_ANSWERS_COL_VOTES', 'helpful', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php
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
					$task = 'accept';
					$alt = Lang::txt('COM_ANSWERS_STATE_UNACCEPTED');
					$cls = 'unpublished';
				break;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id') . '&qid=' . $this->question->get('id')); ?>">
						<span><?php echo $this->truncate(strip_tags($row->get('answer')), 75); ?></span>
					</a>
				</td>
				<td>
					<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&qid=' . $this->question->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_ANSWERS_SET_STATE', $task); ?>">
						<span><?php echo $alt; ?></span>
					</a>
				</td>
				<td class="priority-4">
					<time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time>
				</td>
				<td class="priority-3">
					<a class="glyph user" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=members&task=edit&id=' . $row->get('created_by')); ?>">
						<span><?php echo $this->escape(stripslashes($row->creator()->get('name'))).' ('.$row->get('created_by').')'; ?></span>
					</a>
					<?php if ($row->get('anonymous')) { ?>
						<br /><span>(<?php echo Lang::txt('COM_ANSWERS_ANONYMOUS'); ?>)</span>
					<?php } ?>
				</td>
				<td class="priority-4">
					<span class="vote like" style="color:green;">+<?php echo $row->get('helpful', 0); ?></span>
					<span class="vote dislike" style="color:red;">-<?php echo $row->get('nothelpful', 0); ?></span>
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
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>
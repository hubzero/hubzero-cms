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

$canDo = \Components\Answers\Helpers\Permissions::getActions('question');

Toolbar::title(Lang::txt('COM_ANSWERS_TITLE') . ': ' . Lang::txt('COM_ANSWERS_QUESTIONS'), 'answers.png');
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
	Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('questions');

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

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="col width-50 fltlft">
			<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
			<input type="text" name="q" id="filter_search" value="<?php echo $this->escape($this->filters['q']); ?>" placeholder="<?php echo Lang::txt('COM_ANSWERS_FILTER_SEARCH_PLACEHOLDER'); ?>" />

			<input type="submit" value="<?php echo Lang::txt('COM_ANSWERS_GO'); ?>" />
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="col width-50 fltrt">
			<label for="filterby"><?php echo Lang::txt('COM_ANSWERS_FILTER_BY'); ?></label>
			<select name="filterby" id="filterby" onchange="document.adminForm.submit();">
				<option value="open"<?php if ($this->filters['filterby'] == 'open') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_ANSWERS_FILTER_BY_OPEN'); ?></option>
				<option value="closed"<?php if ($this->filters['filterby'] == 'closed') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_ANSWERS_FILTER_BY_CLOSED'); ?></option>
				<option value="all"<?php if ($this->filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_ANSWERS_FILTER_BY_ALL'); ?></option>
			</select>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->results );?>);" /></th>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_ANSWERS_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_ANSWERS_COL_SUBJECT', 'subject', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo $this->grid('sort', 'COM_ANSWERS_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_ANSWERS_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo $this->grid('sort', 'COM_ANSWERS_COL_CREATOR', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-1"><?php echo $this->grid('sort', 'COM_ANSWERS_COL_ANSWERS', 'rcount', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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
for ($i=0, $n=count($this->results); $i < $n; $i++)
{
	$row =& $this->results[$i];

	switch ($row->get('state'))
	{
		case '1':
			$task = 'open';
			$alt = Lang::txt('COM_ANSWERS_STATE_CLOSED');
			$cls = 'unpublished';
		break;
		case '0':
			$task = 'close';
			$alt = Lang::txt('COM_ANSWERS_STATE_OPEN');
			$cls = 'published';
		break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td class="priority-4">
					<?php echo $row->get('id'); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<span><?php echo $this->escape($row->subject('clean')); ?></span>
						</a>
					<?php } else { ?>
						<span>
							<span><?php echo $this->escape($row->subject('clean')); ?></span>
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
					<a class="glyph user" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=members&task=edit&id=' . $row->creator('id')); ?>">
						<?php echo $this->escape(stripslashes($row->creator('name'))); ?>
					</a>
					<?php if ($row->get('anonymous')) { ?>
						<br /><span>(<?php echo Lang::txt('COM_ANSWERS_FIELD_ANONYMOUS'); ?>)</span>
					<?php } ?>
				</td>
				<td class="priority-1">
					<?php if ($row->get('rcount', 0) > 0) { //if ($row->comments('count', array('filterby' => 'all', 'replies' => false)) > 0) { ?>
						<a class="glyph comment" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=answers&qid=' . $row->get('id')); ?>">
							<span><?php echo Lang::txt('COM_ANSWERS_NUM_RESPONSES', $row->get('rcount', 0)); //$row->comments('count')); ?></span>
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
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>
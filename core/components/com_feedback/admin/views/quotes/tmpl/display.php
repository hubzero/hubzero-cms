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

$canDo = \Components\Feedback\Helpers\Permissions::getActions('quote');

Toolbar::title(Lang::txt('COM_FEEDBACK'), 'feedback.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
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
Toolbar::help('quotes');

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

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_FEEDBACK_FILTER_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo Lang::txt('COM_FEEDBACK_GO'); ?>" />
		<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Html::grid('sort', 'COM_FEEDBACK_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_FEEDBACK_COL_SUBMITTED', 'date', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_FEEDBACK_COL_AUTHOR', 'fullname', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_FEEDBACK_COL_ORGANIZATION', 'org', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_FEEDBACK_COL_QUOTE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_FEEDBACK_COL_QUOTES'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_FEEDBACK_COL_OK_PUBLISH'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->rows->pagination; ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		foreach ($this->rows as $i => $row)
		{
			if (!trim($row->get('quote')))
			{
				$row->set('quote', $row->get('short_quote'));
			}
			if (!trim($row->quote))
			{
				$row->set('quote', $row->get('miniquote'));
			}
			if (!trim($row->quote))
			{
				$row->set('quote', Lang::txt('COM_FEEDBACK_BLANK'));
			}
			?>
			<tr>
				<td>
					<?php echo $row->get('id'); ?>
				</td>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onClick="isChecked(this.checked);" />
				</td>
				<td class="priority-2">
					<?php if ($row->get('date') && $row->get('date') != '0000-00-00 00:00:00') { ?>
						<time datetime="<?php echo $row->get('date'); ?>"><?php echo $row->created('date'); ?></time>
					<?php } ?>
				</td>
				<td class="priority-3">
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape(stripslashes($row->get('fullname'))); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($row->get('fullname'))); ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-5">
					<?php echo $this->escape(stripslashes($row->get('org'))); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape(\Hubzero\Utility\String::truncate(strip_tags($row->get('quote')), 100)); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(\Hubzero\Utility\String::truncate(strip_tags($row->get('quote')), 100)); ?>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php echo ($row->get('notable_quote') == 1) ? '<span class="state yes"><span>' . Lang::txt('JYES') . '</span></span>' : ''; ?>

				</td>
				<td class="priority-4">
					<?php echo ($row->get('publish_ok') == 1) ? '<span class="state yes"><span>' . Lang::txt('JYES') . '</span></span>' : ''; ?>
				</td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
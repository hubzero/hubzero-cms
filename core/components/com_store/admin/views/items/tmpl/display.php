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

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Store\Helpers\Permissions::getActions('item');

Toolbar::title(Lang::txt('COM_STORE_MANAGER'), 'store');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
Toolbar::spacer();
Toolbar::help('items');

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
		<div class="grid">
			<div class="col span4">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_STORE_FILTER_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_STORE_GO'); ?>" />
				<button type="button" onclick="$('#filter_search').val('');$('#filter-published').val('-1');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span8 align-right">
				<label for="filter-published"><?php echo Lang::txt('COM_STORE_FILTER_PUBLISHED'); ?>:</label>
				<select name="published" id="filter-published" onchange="this.form.submit();">
					<option value="-1"<?php if ($this->filters['published'] == '-1') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_ALL_STATES'); ?></option>
					<option value="0"<?php if ($this->filters['published'] === 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->filters['published'] === 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					<option value="2"<?php if ($this->filters['published'] === 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
				</select>

				<label for="filter-available"><?php echo Lang::txt('COM_STORE_FILTER_AVAILIBILITY'); ?>:</label>
				<select name="available" id="filter-available" onchange="this.form.submit();">
					<option value="-1"<?php if ($this->filters['available'] == '-1') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_ALL_AVAILABILITY'); ?></option>
					<option value="0"<?php if ($this->filters['available'] === 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_AVAILABLE'); ?></option>
					<option value="1"<?php if ($this->filters['available'] === 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_UNAVAILABLE'); ?></option>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_STORE_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORE_CATEGORY'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STORE_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STORE_DESCRIPTION', 'description', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STORE_PRICE', 'price', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORE_TIMES_ORDERED'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STORE_INSTOCK', 'available', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STORE_PUBLISHED', 'published', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php
				// Initiate paging
				echo $this->rows->pagination
				?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach ($this->rows as $row)
		{
			$status = '';
			switch ($row->available)
			{
				case '1':
					$a_class = 'publish';
					$a_task = 'unavailable';
					$a_alt = Lang::txt('COM_STORE_TIP_MARK_UNAVAIL');
					break;
				case '0':
					$a_class = 'unpublish';
					$a_task = 'available';
					$a_alt = Lang::txt('COM_STORE_TIP_MARK_AVAIL');
					break;
			}
			switch ($row->published)
			{
				case '1':
					$p_class = 'publish';
					$p_task = 'unpublish';
					$p_alt = Lang::txt('COM_STORE_TIP_REMOVE_ITEM');
					break;
				case '0':
					$p_class = 'unpublish';
					$p_task = 'publish';
					$p_alt = Lang::txt('COM_STORE_TIP_ADD_ITEM');
					break;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>" title="<?php echo Lang::txt('COM_STORE_VIEW_ITEM_DETAILS'); ?>">
						<?php echo $row->id; ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->category)); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>" title="<?php echo Lang::txt('COM_STORE_VIEW_ITEM_DETAILS'); ?>">
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php echo \Hubzero\Utility\String::truncate(stripslashes($row->description), 300); ?></td>
				<td>
					<?php echo $this->escape(stripslashes($row->price)); ?>
				</td>
				<td>
					<?php echo $row->orders()->count(); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $a_class; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $a_task . '&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo $a_alt; ?>">
							<span><?php echo $a_alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $a_class; ?>">
							<span><?php echo $a_alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $p_class; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $p_task . '&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo $p_alt; ?>">
							<span></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $p_class; ?>">
							<span><?php echo $p_alt; ?></span>
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
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>

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

$canDo = \Components\Store\Helpers\PErmissions::getActions('item');

$text = (!$this->store_enabled) ? ' (store is disabled)' : '';

Toolbar::title(Lang::txt('COM_STORE_MANAGER') . $text, 'store');
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
		<label for="filter-filterby"><?php echo Lang::txt('COM_STORE_FILTERBY'); ?>:</label>
		<select name="filterby" id="filter-filterby" onchange="document.adminForm.submit();">
			<option value="available"<?php if ($this->filters['filterby'] == 'available') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_INSTORE_ITEMS'); ?></option>
			<option value="published"<?php if ($this->filters['filterby'] == 'published') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_PUBLISHED'); ?></option>
			<option value="all"<?php if ($this->filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_ALL_ITEMS'); ?></option>
		</select>

		<label for="filter-sortby"><?php echo Lang::txt('COM_STORE_SORTBY'); ?>:</label>
		<select name="sortby" id="filter-sortby" onchange="document.adminForm.submit();">
			<option value="pricelow"<?php if ($this->filters['sortby'] == 'pricelow') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_LOWEST_PRICE'); ?></option>
			<option value="pricehigh"<?php if ($this->filters['sortby'] == 'pricehigh') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_HIGHEST_PRICE'); ?></option>
			<option value="date"<?php if ($this->filters['sortby'] == 'date') { echo ' selected="selected"'; } ?>><?php echo ucfirst(Lang::txt('COM_STORE_DATE_ADDED')); ?></option>
			<option value="category"<?php if ($this->filters['sortby'] == 'category') { echo ' selected="selected"'; } ?>><?php echo ucfirst(Lang::txt('COM_STORE_CATEGORY')); ?></option>
		</select>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo strtoupper(Lang::txt('COM_STORE_ID')); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORE_CATEGORY'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORE_TITLE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORE_DESCRIPTION'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORE_PRICE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORE_TIMES_ORDERED'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORE_INSTOCK'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORE_PUBLISHED'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php
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
		for ($i=0, $n=count($this->rows); $i < $n; $i++)
		{
			$row = &$this->rows[$i];

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
					<?php echo ($row->allorders) ? $row->allorders : '0'; ?>
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
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />

	<?php echo Html::input('token'); ?>
</form>

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

Toolbar::title(Lang::txt('COM_EVENTS_MANAGER').': '.Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CATEGORIES'), 'event.png');
Toolbar::publishList();
Toolbar::unpublishList();
Toolbar::spacer();
Toolbar::addNew();
Toolbar::editList();
Toolbar::deleteList();

Html::behavior('tooltip');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">#</th>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_CATEGORY_NAME'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_CATEGORY_NUM_RECORDS'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_EVENTS_E_PUBLISHING'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ACCESS'); ?></th>
				<th scope="col" colspan="2"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_CATEGORY_REORDER'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10"><?php
				// Initiate paging
				$pageNav = $this->pagination(
					$this->total,
					$this->limitstart,
					$this->limit
				);
				echo $pageNav->render();
				?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
	$k = 0;
	for ($i=0, $n=count($this->rows); $i < $n; $i++)
	{
		$row = &$this->rows[$i];
		$class = $row->published ? 'published' : 'unpublished';
		$alt = $row->published ? 'Published' : 'Unpublished';
		$task = $row->published ? 'unpublish' : 'publish';

		if ($row->groupname == 'Public') {
			$color_access = 'style="color: green;"';
		} else if ($row->groupname == 'Special') {
			$color_access = 'style="color: red;"';
		} else {
			$color_access = 'style="color: black;"';
		}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
<?php if ($row->checked_out && $row->checked_out != User::get('id')) { ?>
					&nbsp;
<?php } else { ?>
					<input type="checkbox" id="cb<?php echo $i;?>" name="id[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
<?php } ?>
				</td>
				<td>
<?php if ($row->checked_out && $row->checked_out != User::get('id')) { ?>
					<span class="checkedout hasTip" title="Checked out::<?php echo $row->editor; ?>">
						<?php echo $this->escape(stripslashes($row->name)); ?> <?php echo $this->escape(stripslashes($row->title)); ?>
					</span>
<?php } else { ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
						<?php echo $this->escape(stripslashes($row->name)); ?> &mdash; <?php echo $this->escape(stripslashes($row->title)); ?>
					</a>
<?php } ?>
				</td>
				<td>
					<?php echo $row->num; ?>
				</td>
				<td>
					<a class="state <?php echo $class;?>" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $task; ?>')">
						<span><?php echo $alt; ?></span>
					</a>
				</td>
				<td>
					<span <?php echo $color_access;?>>
						<?php echo $this->escape(stripslashes($row->groupname)); ?>
					</span>
				</td>
				<td>
				<?php if ($i > 0 || ($i+$this->pageNav->limitstart > 0)) { ?>
					<a href="#reorder" class="order up jgrid" onclick="return listItemTask('cb<?php echo $i;?>','orderup')" title="Move Up">
						<span class="state uparrow"><span><?php echo Lang::txt('COM_EVENTS_MOVE_UP'); ?></span></span>
					</a>
				<?php } else { ?>
					&nbsp;
				<?php } ?>
				</td>
				<td>
				<?php if ($i < $n-1 || $i+$this->pageNav->limitstart < $this->pageNav->total-1) { ?>
					<a href="#reorder" class="order down jgrid" onclick="return listItemTask('cb<?php echo $i;?>','orderdown')" title="Move Down">
						<span class="state downarrow"><span><?php echo Lang::txt('COM_EVENTS_MOVE_DOWN'); ?></span></span>
					</a>
				<?php } else { ?>
					&nbsp;
				<?php } ?>
				</td>
			</tr>
	<?php
		$k = 1 - $k;
	} // for loop
	?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="section" value="<?php echo $this->section; ?>" />
	<input type="hidden" name="task" value="" autocomplete="" />
	<input type="hidden" name="chosen" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>

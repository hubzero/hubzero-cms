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

$canDo = \Components\Wishlist\Helpers\Permissions::getActions('wish');

Toolbar::title(Lang::txt('COM_WISHLIST') . ': ' . Lang::txt('COM_WISHLIST_WISHES'), 'wishlist');
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
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
Toolbar::help('wishes');

$this->css();
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
			<div class="col span6">
				<label for="filter_search"><?php echo Lang::txt('COM_WISHLIST_SEARCH'); ?>:</label>
				<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_WISHLIST_SEARCH_PLACEHOLDER'); ?>" />
				<input type="submit" value="<?php echo Lang::txt('COM_WISHLIST_GO'); ?>" />
				<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span6">
				<label for="filter-status"><?php echo Lang::txt('COM_WISHLIST_FILTER_STATUS'); ?>:</label>
				<select name="status" id="filter-status" onchange="this.form.submit()">
					<option value="all"<?php echo ($this->filters['status'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATE_ALL'); ?></option>
					<option value="granted"<?php echo ($this->filters['status'] == 'granted') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATE_GRANTED'); ?></option>
					<option value="open"<?php echo ($this->filters['status'] == 'open') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATE_OPEN'); ?></option>
					<option value="accepted"<?php echo ($this->filters['status'] == 'accepted') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATE_ACCEPTED'); ?></option>
					<option value="pending"<?php echo ($this->filters['status'] == 'pending') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATE_PENDING'); ?></option>
					<option value="rejected"<?php echo ($this->filters['status'] == 'rejected') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATE_REJECTED'); ?></option>
					<option value="withdrawn"<?php echo ($this->filters['status'] == 'withdrawn') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATE_WITHDRAWN'); ?></option>
					<option value="deleted"<?php echo ($this->filters['status'] == 'deleted') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATE_DELETED'); ?></option>
					<option value="useraccepted"<?php echo ($this->filters['status'] == 'useraccepted') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATE_USER_ACCEPTED'); ?></option>
					<option value="private"<?php echo ($this->filters['status'] == 'private') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATE_PRIVATE'); ?></option>
					<option value="public"<?php echo ($this->filters['status'] == 'public') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATE_PUBLIC'); ?></option>
					<option value="assigned"<?php echo ($this->filters['status'] == 'assigned') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATE_ASSIGNED'); ?></option>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<?php if ($this->wishlist->id) { ?>
				<tr>
					<th colspan="<?php echo (!$this->wishlist->id ? 9 : 8); ?>">
						(<?php echo $this->escape(stripslashes($this->wishlist->category)); ?>) &nbsp; <?php echo $this->escape(stripslashes($this->wishlist->title)); ?>
					</th>
				</tr>
			<?php } ?>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_WISHLIST_WISH_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_WISHLIST_TITLE', 'subject', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<?php if (!$this->wishlist->id) { ?>
					<th scope="col"><?php echo Html::grid('sort', 'COM_WISHLIST_WISHLIST_ID', 'wishlist', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<?php } ?>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_WISHLIST_PROPOSED_BY', 'proposed_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_WISHLIST_PROPOSED', 'proposed', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_WISHLIST_STATUS', 'status', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_WISHLIST_ACCESS', 'private', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_WISHLIST_COMMENTS', 'numreplies', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo (!$this->wishlist->id ? 9 : 8); ?>"><?php
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
			switch ($row->status)
			{
				case 1:
					$class = 'granted';
					$task = 'pending';
					$alt = Lang::txt('COM_WISHLIST_STATUS_GRANTED');
				break;
				case 2:
					$class = 'trashed';
					$task = 'grant';
					$alt = Lang::txt('COM_WISHLIST_STATUS_DELETED');
				break;
				case 3:
					$class = 'rejected';
					$task = 'pending';
					$alt = Lang::txt('COM_WISHLIST_STATUS_REJECTED');
				break;
				case 4:
					$class = 'withdrawn';
					$task = 'pending';
					$alt = Lang::txt('COM_WISHLIST_STATUS_WITHDRAWN');
				break;
				case 6:
					$class = 'accepted';
					$task = 'grant';
					$alt = Lang::txt('COM_WISHLIST_STATUS_WITHDRAWN');
				break;
				case 7:
					$class = 'flagged';
					$task = 'pending';
					$alt = Lang::txt('COM_WISHLIST_STATUS_WITHDRAWN');
				break;
				case 0:
				default;
					$class = 'pending';
					$task = 'grant';
					$alt = Lang::txt('COM_WISHLIST_STATUS_PENDING');
				break;
			}

			if ($row->private)
			{
				$color_access = 'access private';
				$task_access = 'accesspublic';
				$groupname = 'Private';
			}
			else
			{
				$color_access = 'access public';
				$task_access = 'accessregistered';
				$groupname = 'Public';
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td class="priority-5">
					<?php echo $row->id; ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
							<span><?php echo $this->escape(stripslashes($row->subject)); ?></span>
						</a>
					<?php } else { ?>
						<span>
							<span><?php echo $this->escape(stripslashes($row->subject)); ?></span>
						</span>
					<?php } ?>
				</td>
				<?php if (!$this->wishlist->id) { ?>
					<td>
						<?php echo $row->wishlist; ?>
					</td>
				<?php } ?>
				<td class="priority-4">
					<?php echo $this->escape($row->proposer->get('name', Lang::txt('(unknown)'))); ?>
				</td>
				<td class="priority-5">
					<time datetime="<?php echo $row->proposed; ?>"><?php echo $row->proposed; ?></time>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $class; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_WISHLIST_SET_TASK', $task); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $class; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-3">
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task_access . '&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>" class="<?php echo $color_access; ?>" title="<?php echo Lang::txt('COM_WISHLIST_CHANGE_ACCESS'); ?>">
							<?php echo $groupname; ?>
						</a>
					<?php } else { ?>
						<span class="<?php echo $color_access; ?>">
							<?php echo $groupname; ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-2">
					<a class="glyph comment" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=comments&wish=' . $row->id); ?>">
						<span><?php echo $this->escape($row->comments()->total()); ?></span>
					</a>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
			$i++;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
	<input type="hidden" name="wishlist" value="<?php echo $this->filters['wishlist']; ?>" />
	<input type="hidden" name="cid" value="<?php echo $this->filters['wishlist']; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>

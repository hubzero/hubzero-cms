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

$canDo = \Components\Wishlist\Helpers\Permissions::getActions('wishlist');

Toolbar::title(Lang::txt('COM_WISHLIST'), 'wishlist');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
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
Toolbar::help('lists');
?>

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
				<label for="filter-category"><?php echo Lang::txt('COM_WISHLIST_CATEGORY'); ?>:</label>
				<select name="category" id="filter-category" onchange="this.form.submit()">
					<option value=""<?php echo ($this->filters['category'] == '') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_SELECT_CATEGORY'); ?></option>
					<option value="general"<?php echo ($this->filters['category'] == 'general') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_CATEGORY_GENERAL'); ?></option>
					<option value="group"<?php echo ($this->filters['category'] == 'group') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_CATEGORY_GROUP'); ?></option>
					<option value="resource"<?php echo ($this->filters['category'] == 'resource') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_CATEGORY_RESOURCE'); ?></option>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_WISHLIST_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_WISHLIST_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_WISHLIST_ACCESS', 'public', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4" colspan="2"><?php echo Html::grid('sort', 'COM_WISHLIST_CATEGORY', 'category', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<!-- <th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_WISHLIST_WISHES', 'wishes', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th> -->
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_WISHLIST_WISHES'); ?></th>
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
			switch ($row->state)
			{
				case 1:
					$class = 'publish';
					$task  = 'unpublish';
					$alt   = Lang::txt('COM_WISHLIST_PUBLISHED');
				break;
				case 2:
					$class = 'expire';
					$task  = 'publish';
					$alt   = Lang::txt('COM_WISHLIST_TRASHED');
				break;
				case 0:
					$class = 'unpublish';
					$task  = 'publish';
					$alt   = Lang::txt('COM_WISHLIST_UNPUBLISHED');
				break;
			}

			if (!$row->public)
			{
				$color_access = 'access private';
				$task_access  = 'accessregistered';
				$groupname    = 'Private';
			}
			else
			{
				$color_access = 'access public';
				$task_access  = 'accesspublic';
				$groupname    = 'Public';
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
							<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
						</a>
					<?php } else { ?>
						<span>
							<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $class; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_WISHLIST_SET_TASK',$task);?>">
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
				<td class="priority-4">
					<span class="glyph category">
						<span><?php echo $this->escape(stripslashes($row->category)); ?></span>
					</span>
				</td>
				<td class="priority-4">
					<span>
						<span><?php echo $this->escape(stripslashes($row->referenceid)); ?></span>
					</span>
				</td>
				<td class="priority-2">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=wishes&wishlist=' . $row->id); ?>">
						<span><?php echo $row->wishes()->total() . ' ' . Lang::txt('COM_WISHLIST_LIST_WISHES'); ?></span>
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
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>

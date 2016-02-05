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

$canDo = \Components\Resources\Helpers\Permissions::getActions('resource');

Toolbar::title(Lang::txt('COM_RESOURCES'), 'resources.png');
if ($canDo->get('core.admin'))
{
	Toolbar::custom('check', 'scan', '', 'COM_RESOURCES_AUDIT', false);
	Toolbar::spacer();
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew('addchild', 'COM_RESOURCES_ADD_CHILD');
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

Html::behavior('tooltip');

$this->css();
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
		<div class="col width-50 fltlft">
			<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>: </label>
			<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_RESOURCES_SEARCH_PLACEHOLDER'); ?>" />

			<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo Lang::txt('COM_RESOURCES_GO'); ?>" />
			<button type="button" onclick="$('#filter_search').val('');$('#filter-status').val('all');$('#type').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="col width-50 fltrt">
			<label for="filter-status"><?php echo Lang::txt('COM_RESOURCES_FILTER_STATUS'); ?>:</label>
			<select name="status" id="filter-status" onchange="this.form.submit();">
				<option value="all"<?php echo ($this->filters['status'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_RESOURCES_FILTER_STATUS_ALL'); ?></option>
				<option value="2"<?php echo ($this->filters['status'] == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL'); ?></option>
				<option value="5"<?php echo ($this->filters['status'] == 5) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_RESOURCES_DRAFT_INTERNAL'); ?></option>
				<option value="3"<?php echo ($this->filters['status'] == 3) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_RESOURCES_PENDING'); ?></option>
				<option value="0"<?php echo ($this->filters['status'] == 0 && $this->filters['status'] != 'all') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
				<option value="1"<?php echo ($this->filters['status'] == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
				<option value="4"<?php echo ($this->filters['status'] == 4) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JTRASHED'); ?></option>
			</select>

			<label for="type"><?php echo Lang::txt('COM_RESOURCES_FILTER_TYPE'); ?>:</label>
			<?php echo \Components\Resources\Helpers\Html::selectType($this->types, 'type', $this->filters['type'], Lang::txt('COM_RESOURCES_FILTER_TYPE_ALL'), '', ' onchange="this.form.submit();"', ''); ?>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo ($this->rows) ? count($this->rows) : 0; ?>);" /></th>
				<th scope="col" class="priority-5"><?php echo $this->grid('sort', 'COM_RESOURCES_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_RESOURCES_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-3"><?php echo $this->grid('sort', 'COM_RESOURCES_COL_STATUS', 'published', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-3"><?php echo $this->grid('sort', 'COM_RESOURCES_COL_ACCESS', 'access', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_RESOURCES_COL_MODIFIED', 'modified', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-5"><?php echo Lang::txt('COM_RESOURCES_COL_LICENSE'); ?></th>
				<th scope="col" class="priority-2"><?php echo $this->grid('sort', 'COM_RESOURCES_COL_TYPE', 'type', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_RESOURCES_COL_CHILDREN', 'children', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_RESOURCES_COL_TAGS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10"><?php
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
$filterstring  = '';

$database = App::get('db');

for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row =& $this->rows[$i];

	$rparams = new \Hubzero\Config\Registry($row->params);
	$license = $rparams->get('license');

	// Build some publishing info
	$info  = Lang::txt('COM_RESOURCES_CREATED') . ': ' . Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . '<br />';
	$info .= Lang::txt('COM_RESOURCES_CREATED_BY') . ': ' . $this->escape($row->created_by) . '<br />';

	// Get the published status
	$now = Date::toSql();
	switch ($row->published)
	{
		case 0:
			$alt   = Lang::txt('JUNPUBLISHED');
			$class = 'unpublished';
			$task  = 'publish';
			break;
		case 1:
			if ($now <= $row->publish_up)
			{
				$alt   = Lang::txt('COM_RESOURCES_PENDING');
				$class = 'pending';
				$task  = 'unpublish';
			} else if ($now <= $row->publish_down || $row->publish_down == "0000-00-00 00:00:00")
			{
				$alt   = Lang::txt('JPUBLISHED');
				$class = 'published';
				$task  = 'unpublish';
			}
			else if ($now > $row->publish_down)
			{
				$alt   = Lang::txt('COM_RESOURCES_EXPIRED');
				$class = 'expired';
				$task  = 'unpublish';
			}

			$info .= Lang::txt('JPUBLISHED') . ': ' . Date::of($row->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . '<br />';
			break;
		case 2:
			$alt   = Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL');
			$class = 'draftexternal';
			$task  = 'publish';
			break;
		case 3:
			$alt   = Lang::txt('COM_RESOURCES_NEW');
			$class = 'submitted';
			$task  = 'publish';
			break;
		case 4:
			$alt   = Lang::txt('JTRASHED');
			$class = 'trashed';
			$task  = 'publish';
			break;
		case 5:
			$alt   = Lang::txt('COM_RESOURCES_DRAFT_INTERNAL');
			$class = 'draftinternal';
			$task  = 'publish';
			break;
		default:
			$alt   = '-';
			$class = '';
			$task  = '';
			break;
	}

	switch ($row->access)
	{
		case 0:
			$color_access = 'public';
			$task_access  = 'accessregistered';
			$row->groupname = 'COM_RESOURCES_ACCESS_PUBLIC';
			break;
		case 1:
			$color_access = 'registered';
			$task_access  = 'accessspecial';
			$row->groupname = 'COM_RESOURCES_ACCESS_REGISTERED';
			break;
		case 2:
			$color_access = 'special';
			$task_access  = 'accessprotected';
			$row->groupname = 'COM_RESOURCES_ACCESS_SPECIAL';
			break;
		case 3:
			$color_access = 'protected';
			$task_access  = 'accessprivate';
			$row->groupname = 'COM_RESOURCES_ACCESS_PROTECTED';
			break;
		case 4:
			$color_access = 'private';
			$task_access  = 'accesspublic';
			$row->groupname = 'COM_RESOURCES_ACCESS_PRIVATE';
			break;
	}

	// Get the tags on this item
	$rt = new \Components\Resources\Helpers\Tags($row->id);
	$tags = $rt->tags('count');

	// See if it's checked out or not
	if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00')
	{
		$date = Date::of($row->checked_out_time)->toLocal(Lang::txt('DATE_FORMAT_LC1'));
		$time = Date::of($row->checked_out_time)->toLocal('H:i');

		$checked  = '<span class="editlinktip hasTip" title="' . Lang::txt('JLIB_HTML_CHECKED_OUT') . '::' . $this->escape($row->editor) . '<br />' . $date . '<br />' . $time . '">';
		$checked .= Html::asset('image', 'admin/checked_out.png', null, null, true) . '</span>';

		$info .= ($row->checked_out_time != '0000-00-00 00:00:00')
				 ? Lang::txt('COM_RESOURCES_CHECKED_OUT') . ': ' . Date::of($row->checked_out_time)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . '<br />'
				 : '';
		if ($row->editor)
		{
			$info .= Lang::txt('COM_RESOURCES_CHECKED_OUT_BY') . ': ' . $this->escape($row->editor);
		}
	}
	else
	{
		$checked = $this->grid('id', $i, $row->id, false, 'id');
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $checked; ?>
				</td>
				<td class="priority-5">
					<?php echo $row->id; ?>
				</td>
				<td>
					<a class="editlinktip hasTip" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id . $filterstring); ?>" title="<?php echo Lang::txt('COM_RESOURCES_PUBLISH_INFO');?>::<?php echo $info; ?>">
						<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
					</a>
				</td>
				<td class="priority-3">
					<a class="state <?php echo $class; ?> hasTip" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->id . $filterstring . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo $alt; ?> :: <?php echo Lang::txt('COM_RESOURCES_SET_TASK_TO', $task); ?>">
						<span><?php echo $alt; ?></span>
					</a>
				</td>
				<td class="priority-3">
					<a class="access <?php echo $color_access; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task_access . '&id=' . $row->id . $filterstring . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_RESOURCES_CHANGE_ACCESS'); ?>">
						<span><?php echo $this->escape(Lang::txt($row->groupname)); ?></span>
					</a>
				</td>
				<td class="priority-4" style="white-space: nowrap">
					<?php if ($row->modified == '0000-00-00 00:00:00') { echo Lang::txt('COM_RESOURCES_NOT_MODIFIED'); } else { ?>
						<time datetime="<?php echo ($row->modified != '0000-00-00 00:00:00' ? $row->modified : $row->created); ?>">
							<?php echo Date::of($row->modified != '0000-00-00 00:00:00' ? $row->modified : $row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?>
						</time>
					<?php } ?>
				</td>
				<td class="priority-5">
					<?php echo $this->escape(stripslashes($license)); ?>
				</td>
				<td class="priority-2" style="white-space: nowrap">
					<?php echo $this->escape(stripslashes($row->typetitle)); ?>
				</td>
				<td style="white-space: nowrap">
					<?php if ($row->children > 0) { ?>
						<a class="glyph menulist" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=children&pid=' . $row->id); ?>">
							<span><?php echo $row->children; ?></span>
						</a>
					<?php } else { ?>
						<a class="state add" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=addchild&pid=' . $row->id); ?>">
							<span><?php echo Lang::txt('COM_RESOURCES_ADD'); ?></span>
						</a>
					<?php } ?>
				</td>
				<td style="white-space: nowrap">
					<?php if ($tags > 0) { ?>
						<a class="glyph tag" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=tags&id=' . $row->id); ?>">
							<span><?php echo $tags; ?></span>
						</a>
					<?php } else { ?>
						<a class="state add" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=tags&id=' . $row->id); ?>">
							<span><?php echo Lang::txt('COM_RESOURCES_ADD'); ?></span>
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

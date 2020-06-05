<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('resource');

Toolbar::title(Lang::txt('COM_RESOURCES'), 'resources');
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

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>: </label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_RESOURCES_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo Lang::txt('COM_RESOURCES_GO'); ?>" />
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span6">
				<label for="filter-status"><?php echo Lang::txt('COM_RESOURCES_FILTER_STATUS'); ?>:</label>
				<select name="status" id="filter-status" class="filter filter-submit">
					<option value="all"<?php echo ($this->filters['status'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_RESOURCES_FILTER_STATUS_ALL'); ?></option>
					<option value="2"<?php echo ($this->filters['status'] == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL'); ?></option>
					<option value="5"<?php echo ($this->filters['status'] == 5) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_RESOURCES_DRAFT_INTERNAL'); ?></option>
					<option value="3"<?php echo ($this->filters['status'] == 3) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_RESOURCES_PENDING'); ?></option>
					<option value="0"<?php echo ($this->filters['status'] == 0 && $this->filters['status'] != 'all') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php echo ($this->filters['status'] == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					<option value="4"<?php echo ($this->filters['status'] == 4) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JTRASHED'); ?></option>
				</select>

				<label for="filter-license"><?php echo Lang::txt('COM_RESOURCES_FILTER_LICENSE'); ?>:</label>
				<select name="license" id="filter-license" class="filter filter-submit">
					<option value="all"<?php echo ($this->filters['license'] == 'all') ? 'selected="selected"' : '';?>><?php echo Lang::txt('COM_RESOURCES_FILTER_LICENSE_ALL'); ?></option>
					<?php foreach ($this->licenses as $license) { ?>
						<option value="<?php echo $license->get('name'); ?>"<?php echo ($this->filters['license'] == $license->get('name')) ? 'selected="selected"' : ''; ?>><?php echo $license->get('title'); ?></option>
					<?php } ?>
				</select>

				<label for="filter-type"><?php echo Lang::txt('COM_RESOURCES_FILTER_TYPE'); ?>:</label>
				<select name="type" id="filter-type" class="filter filter-submit">
					<option value=""><?php echo Lang::txt('COM_RESOURCES_FILTER_TYPE_ALL'); ?></option>
					<?php foreach ($this->types as $type) { ?>
						<option value="<?php echo $type->id; ?>"<?php echo ($this->filters['type'] == $type->id) ? ' selected="selected"' : ''; ?>><?php echo $this->escape($type->type); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_STATUS', 'published', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_ACCESS', 'access', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_MODIFIED', 'modified', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_LICENSE', 'license', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_TYPE', 'type', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_CHILDREN', 'children', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_RESOURCES_COL_TAGS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10"><?php
				// Initiate paging
				echo $this->rows->pagination;
				?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		$filterstring  = '';

		foreach ($this->rows as $row)
		{
			$license = $row->get('license', $row->params->get('license'));

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
					}
					else if ($now <= $row->publish_down || !$row->publish_down || $row->publish_down == '0000-00-00 00:00:00')
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
			$tags = count($row->tags());

			// See if it's checked out or not
			if ($row->checked_out || ($row->checked_out_time && $row->checked_out_time != '0000-00-00 00:00:00')) // && $row->checked_out != User::get('id'))
			{
				$date = Date::of($row->checked_out_time)->toLocal(Lang::txt('DATE_FORMAT_LC1'));
				$time = Date::of($row->checked_out_time)->toLocal('H:i');

				$editor = User::getInstance($row->checked_out);

				$checked  = '<span class="editlinktip hasTip" title="' . Lang::txt('JLIB_HTML_CHECKED_OUT') . '::' . $this->escape($editor->get('name')) . '<br />' . $date . '<br />' . $time . '">';
				$checked .= '<span class="checkedout"></span>' . '</span>';

				$info .= ($row->checked_out_time && $row->checked_out_time != '0000-00-00 00:00:00')
						 ? Lang::txt('COM_RESOURCES_CHECKED_OUT') . ': ' . Date::of($row->checked_out_time)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . '<br />'
						 : '';

				if ($editor)
				{
					$info .= Lang::txt('COM_RESOURCES_CHECKED_OUT_BY') . ': ' . $this->escape($editor->get('name'));
				}
			}
			else
			{
				$checked = Html::grid('id', $i, $row->id, false, 'id');
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
				<td class="priority-4">
					<?php if (!$row->modified || $row->modified == '0000-00-00 00:00:00') { ?>
						<?php echo Lang::txt('COM_RESOURCES_NOT_MODIFIED'); ?>
					<?php } else { ?>
						<time datetime="<?php echo ($row->modified && $row->modified != '0000-00-00 00:00:00') ? $row->modified : $row->created; ?>">
							<?php echo Date::of($row->modified && $row->modified != '0000-00-00 00:00:00' ? $row->modified : $row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?>
						</time>
					<?php } ?>
				</td>
				<td class="priority-5">
					<?php echo $this->escape(stripslashes($license)); ?>
				</td>
				<td class="priority-2">
					<?php echo $this->escape(stripslashes($row->type->type)); ?>
				</td>
				<td>
					<?php if ($row->children()->total() > 0) { ?>
						<a class="glyph menulist" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=children&pid=' . $row->id); ?>">
							<span><?php echo $row->children()->total(); ?></span>
						</a>
					<?php } else { ?>
						<a class="state add" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=addchild&pid=' . $row->id); ?>">
							<span><?php echo Lang::txt('COM_RESOURCES_ADD'); ?></span>
						</a>
					<?php } ?>
				</td>
				<td>
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
			$i++;
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>

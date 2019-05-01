<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('resource');

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_CHILDREN'), 'resources');
if ($this->filters['parent_id'] > 0)
{
	if ($canDo->get('core.create'))
	{
		Toolbar::addNew('addchild', 'COM_RESOURCES_ADD_CHILD');
	}
	if ($canDo->get('core.delete'))
	{
		Toolbar::deleteList('COM_RESOURCES_REMOVE_CHILD_CONFIRM', 'removechild', 'COM_RESOURCES_REMOVE_CHILD');
	}
	Toolbar::spacer();
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::spacer();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}

$this->css();

Html::behavior('tooltip');

if ($this->filters['parent_id'] > 0)
{
	$colspan = 9;
	if ($this->parent->get('type') == 5)
	{
		$colspan = 10;
	}
}
else
{
	$colspan = 7;
}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<?php if ($this->filters['parent_id'] > 0) { ?>
				<tr>
					<th colspan="9">
						<?php echo '<a href="' . Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $this->filters['parent_id']) . '">' . $this->escape(stripslashes($this->parent->title)) . '</a>'; ?>
					</th>
				</tr>
			<?php } ?>
			<tr>
				<th><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th><?php echo Lang::txt('COM_RESOURCES_COL_ID'); ?></th>
				<th><?php echo Lang::txt('COM_RESOURCES_COL_TITLE'); ?></th>
				<th><?php echo Lang::txt('COM_RESOURCES_COL_STATUS'); ?></th>
				<th><?php echo Lang::txt('COM_RESOURCES_COL_ACCESS'); ?></th>
				<th><?php echo Lang::txt('COM_RESOURCES_COL_TYPE'); ?></th>
				<?php if ($this->filters['parent_id'] > 0) { ?>
					<th colspan="3"><?php echo Lang::txt('COM_RESOURCES_COL_ORDER'); ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo $colspan; ?>"><?php
				// Initiate paging
				$pageNav = $this->rows->pagination;
				echo $pageNav->render();
				?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		$orderings = $this->rows->fieldsByKey('associative_ordering');
		foreach ($this->rows as $row)
		{
			// Build some publishing info
			$info  = Lang::txt('COM_RESOURCES_CREATED') . ': ' . $row->created . '<br />';
			$info .= Lang::txt('COM_RESOURCES_CREATED_BY') . ': ' . $this->escape($row->created_by) . '<br />';

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
					} else if ($now <= $row->publish_down || !$row->publish_down || $row->publish_down == '0000-00-00 00:00:00')
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

			if ($row->get('logical_type'))
			{
				$typec  = $this->escape($row->logicaltype->get('type'));
				$typec .= ' (' . $this->escape(stripslashes($row->type->get('type'))) . ')';
			}
			else
			{
				$typec = $this->escape(stripslashes($row->type->get('type')));
			}

			// See if it's checked out or not
			if ($row->checked_out || ($row->checked_out_time && $row->checked_out_time != '0000-00-00 00:00:00')) // && $row->checked_out != User::get('id'))
			{
				$date = Date::of($row->checked_out_time)->toLocal(Lang::txt('DATE_FORMAT_LC1'));
				$time = Date::of($row->checked_out_time)->toLocal('H:i');

				$checked  = '<span class="editlinktip hasTip" title="' . Lang::txt('JLIB_HTML_CHECKED_OUT') . '::' . $this->escape($row->editor) . '<br />' . $date . '<br />' . $time . '">';
				$checked .= '<span class="checkedout"></span>' . '</span>';

				$info .= ($row->checked_out_time && $row->checked_out_time != '0000-00-00 00:00:00')
						 ? Lang::txt('COM_RESOURCES_CHECKED_OUT') . ': ' . Date::of($row->checked_out_time)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . '<br />'
						 : '';
				if ($row->editor)
				{
					$info .= Lang::txt('COM_RESOURCES_CHECKED_OUT_BY') . ': ' . $this->escape($row->editor);
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
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<?php if ((($row->checked_out || ($row->checked_out_time && $row->checked_out_time != '0000-00-00 00:00:00')) && $row->checked_out != User::get('id')) || !$canDo->get('core.edit')) { ?>
						<span class="editlinktip hasTip" title="<?php echo Lang::txt('COM_RESOURCES_PUBLISH_INFO');?>::<?php echo $info; ?>">
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</span>
						<?php echo ($row->standalone != 1 && $row->path != '') ? '<br /><small>' . $row->path . '</small>': ''; ?>
					<?php } else { ?>
						<a class="editlinktip hasTip" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id . '&pid=' . $this->filters['parent_id']); ?>" title="<?php echo Lang::txt('COM_RESOURCES_PUBLISH_INFO');?>::<?php echo $info; ?>">
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</a>
						<?php echo ($row->standalone != 1 && $row->path != '') ? '<br /><small>' . $row->path . '</small>': ''; ?>
					<?php } ?>
				</td>
				<td>
					<?php if ($row->checked_out || ($row->checked_out_time && $row->checked_out_time != '0000-00-00 00:00:00') || !$canDo->get('core.edit.state')) { ?>
						<span class="state <?php echo $class;?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } else { ?>
						<a class="state <?php echo $class;?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->id . '&pid=' . $this->filters['parent_id'] . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_RESOURCES_SET_TASK_TO', $task); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } ?>
				</td>
				<td>
					<?php if ($row->checked_out || ($row->checked_out_time && $row->checked_out_time != '0000-00-00 00:00:00') || !$canDo->get('core.edit.state')) { ?>
						<span class="access <?php echo $color_access; ?>">
							<span><?php echo Lang::txt($row->groupname); ?></span>
						</span>
					<?php } else { ?>
						<a class="access <?php echo $color_access; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task_access . '&id=' . $row->id . '&pid=' . $this->filters['parent_id'] . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_RESOURCES_CHANGE_ACCESS'); ?>">
							<span><?php echo Lang::txt($row->groupname); ?></span>
						</a>
					<?php } ?>
				</td>
				<td>
					<?php echo $typec; ?>
				</td>
				<?php if ($this->filters['parent_id'] > 0) { ?>
					<td>
						<?php echo $pageNav->orderUpIcon($i, ($row->associative_ordering != @$orderings[$i-1])); ?>
					</td>
					<td>
						<?php echo $pageNav->orderDownIcon($i, $pageNav->total, ($row->associative_ordering != @$orderings[$i+1])); ?>
					</td>
					<td>
						<?php echo $row->associative_ordering; ?>
					</td>
				<?php } ?>
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
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
	<input type="hidden" name="viewtask" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="pid" value="<?php echo $this->escape($this->filters['parent_id']); ?>" />

	<?php echo Html::input('token'); ?>
</form>

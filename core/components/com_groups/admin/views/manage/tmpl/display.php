<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Groups\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_GROUPS'), 'groups');

if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_groups', '550');
	Toolbar::spacer();
}
if ($canDo->get('core.manage') && $this->config->get('super_gitlab', 0))
{
	Toolbar::custom('update', 'refresh', '', 'COM_GROUPS_UPDATE_CODE');
	Toolbar::spacer();
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::archiveList();
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
	Toolbar::deleteList('COM_GROUPS_DELETE_CONFIRM', 'delete');
}
Toolbar::spacer();
Toolbar::help('groups');

$this->css();

Html::behavior('tooltip');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span4">
				<label for="filter_search"><?php echo Lang::txt('COM_GROUPS_SEARCH'); ?>:</label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_GROUPS_SEARCH'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_GROUPS_GO'); ?>" />
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span8 align-right">
				<label for="filter-type"><?php echo Lang::txt('COM_GROUPS_TYPE'); ?>:</label>
				<select name="type" id="filter-type"  class="filter filter-submit">
					<option value="all"<?php echo ($this->filters['type'][0] == 'all') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_TYPE'); ?></option>
					<option value="hub"<?php echo ($this->filters['type'][0] == 'hub') ? ' selected="selected"' : ''; ?>>Hub</option>
					<option value="super"<?php echo ($this->filters['type'][0] == 'super') ? ' selected="selected"' : ''; ?>>Super</option>
					<?php if ($canDo->get('core.admin')) { ?>
						<option value="system"<?php echo ($this->filters['type'][0] == 'system') ? ' selected="selected"' : ''; ?>>System</option>
					<?php } ?>
					<option value="project"<?php echo ($this->filters['type'][0] == 'project') ? ' selected="selected"' : ''; ?>>Project</option>
					<option value="course"<?php echo ($this->filters['type'][0] == 'course') ? ' selected="selected"' : ''; ?>>Course</option>
				</select>

				<label for="filter-discoverability"><?php echo Lang::txt('COM_GROUPS_DISCOVERABILITY'); ?>:</label>
				<select name="discoverability" id="filter-discoverability"  class="filter filter-submit">
					<option value=""<?php echo ($this->filters['discoverability'] == null) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_DISCOVERABILITY'); ?></option>
					<option value="0"<?php echo ($this->filters['discoverability'] == 0 && $this->filters['discoverability'] != null) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_DISCOVERABILITY_VISIBLE'); ?></option>
					<option value="1"<?php echo ($this->filters['discoverability'] == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_DISCOVERABILITY_HIDDEN'); ?></option>
				</select>

				<label for="filter-policy"><?php echo Lang::txt('COM_GROUPS_JOIN_POLICY'); ?>:</label>
				<select name="policy" id="filter-policy" class="filter filter-submit">
					<option value=""<?php echo ($this->filters['policy'] == '') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_JOIN_POLICY'); ?></option>
					<option value="open"<?php echo ($this->filters['policy'] == 'open') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_JOIN_POLICY_PUBLIC'); ?></option>
					<option value="restricted"<?php echo ($this->filters['policy'] == 'restricted') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_JOIN_POLICY_RESTRICTED'); ?></option>
					<option value="invite"<?php echo ($this->filters['policy'] == 'invite') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_JOIN_POLICY_INVITE'); ?></option>
					<option value="closed"<?php echo ($this->filters['policy'] == 'closed') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_JOIN_POLICY_CLOSED'); ?></option>
				</select>

				<label for="filter-state"><?php echo Lang::txt('COM_GROUPS_STATE'); ?>:</label>
				<select name="state" id="filter-state" class="filter filter-submit">
					<option value="-1"<?php echo ($this->filters['state'] == -1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_STATE'); ?></option>
					<option value="0"<?php echo ($this->filters['state'] == 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_UNPUBLISHED'); ?></option>
					<option value="1"<?php echo ($this->filters['state'] == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_PUBLISHED'); ?></option>
					<option value="2"<?php echo ($this->filters['state'] == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_ARCHIVED'); ?></option>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
		 	<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-6"><?php echo Html::grid('sort', 'COM_GROUPS_ID', 'gidNumber', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_GROUPS_NAME', 'description', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_GROUPS_CN', 'cn', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_GROUPS_TYPE', 'type', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_GROUPS_PUBLISHED', 'published', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_GROUPS_APPROVED', 'approved', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_GROUPS_MEMBERS'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_GROUPS_PAGES'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9"><?php
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
		$database = App::get('db');
		$p = new \Components\Groups\Tables\Page($database);
		$k = 0;
		for ($i=0, $n=count($this->rows); $i < $n; $i++)
		{
			$row =& $this->rows[$i];

			$group = new \Hubzero\User\Group();
			$group->read($row->gidNumber);

			switch ($row->type)
			{
				case '0':
					$type = '<span class="group-type system">' . Lang::txt('COM_GROUPS_TYPE_SYSTEM') . '</span>';
					break;
				case '1':
					$type = '<span class="group-type hub">' . Lang::txt('COM_GROUPS_TYPE_HUB') . '</span>';
					break;
				case '2':
					$type = '<span class="group-type project">' . Lang::txt('COM_GROUPS_TYPE_PROJECT') . '</span>';
					break;
				case '3':
					$type = '<span class="group-type super">' . Lang::txt('COM_GROUPS_TYPE_SUPER') . '</span>';
					break;
				case '4':
					$type = '<span class="group-type course">' . Lang::txt('COM_GROUPS_TYPE_COURSE') . '</span>';
					break;
			}

			$pages = $p->count(array('gidNumber' => $row->gidNumber));

			//get group invite emails
			$inviteemails = \Hubzero\User\Group\InviteEmail::all()
				->whereEquals('gidNumber', $group->get('gidNumber'))
				->total();

			//get group membership
			$members    = $group->get('members');
			$managers   = $group->get('managers');
			$applicants = $group->get('applicants');
			$invitees   = $group->get('invitees');

			//remove any managers from members list
			$true_members = array_diff($members, $managers);

			//build membership tooltip
			$tip  = '<table><tbody>';
			$tip .= '<tr><th>' . Lang::txt('COM_GROUPS_MEMBERS') . '</th><td>' . count($true_members) . '</td></tr>';
			$tip .= '<tr><th>' . Lang::txt('COM_GROUPS_MANAGERS') . '</th><td>' . count($managers) . '</td></tr>';
			$tip .= '<tr><th>' . Lang::txt('COM_GROUPS_APPLICANTS') . '</th><td>' . count($applicants) . '</td></tr>';
			$tip .= '<tr><th>' . Lang::txt('COM_GROUPS_INVITEES') . '</th><td>' . (count($invitees) + $inviteemails) . '</td></tr>';
			$tip .= '</tbody></table>';
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $this->escape($row->cn); ?>" class="checkbox-toggle" />
				</td>
				<td class="priority-6">
					<?php echo $this->escape($row->gidNumber); ?>
				</td>
				<td class="priority-4">
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->cn); ?>">
							<?php echo $row->description ? $this->escape(stripslashes($row->description)) : '<span class="empty-field smallsub">' . Lang::txt('COM_GROUPS_NONE') . '</span>'; ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $row->description ? $this->escape(stripslashes($row->description)) : '<span class="empty-field smallsub">' . Lang::txt('COM_GROUPS_NONE') . '</span>'; ?>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->cn); ?>">
							<?php echo $this->escape($row->cn); ?>
						</a>
					<?php } else { ?>
						<?php echo $this->escape($row->cn); ?>
					<?php } ?>
				</td>
				<td class="priority-5">
					<?php echo $type; ?>
				</td>
				<td class="priority-3">
					<?php if ($canDo->get('core.edit.state')) { ?>
						<?php if ($row->published == 2) { ?>
							<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=publish&id=' . $row->cn . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_GROUPS_PUBLISH'); ?>">
								<span class="state archive">
									<span class="text"><?php echo Lang::txt('COM_GROUPS_ARCHIVED'); ?></span>
								</span>
							</a>
						<?php } elseif ($row->published == 1) { ?>
							<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=unpublish&id=' . $row->cn . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_GROUPS_UNPUBLISH'); ?>">
								<span class="state publish">
									<span class="text"><?php echo Lang::txt('COM_GROUPS_PUBLISHED'); ?></span>
								</span>
							</a>
						<?php } else { ?>
							<a class="jgrid" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=publish&id=' . $row->cn . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_GROUPS_PUBLISH'); ?>">
								<span class="state unpublish">
									<span class="text"><?php echo Lang::txt('COM_GROUPS_UNPUBLISHED'); ?></span>
								</span>
							</a>
						<?php } ?>
					<?php } ?>
				</td>
				<td class="priority-3">
					<?php if ($canDo->get('core.edit.state')) { ?>
						<?php if (!$group->get('approved')) { ?>
							<a class="jgrid state no" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=approve&id=' . $row->cn . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_GROUPS_APPROVE'); ?>">
								<span class="not-approved">
									<span class="text"><?php echo Lang::txt('COM_GROUPS_APPROVE'); ?></span>
								</span>
							</a>
						<?php } else { ?>
							<a class="jgrid state yes" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=unapprove&id=' . $row->cn . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_GROUPS_UNAPPROVE'); ?>">
								<span class="approved">
									<span class="text"><?php echo Lang::txt('COM_GROUPS_APPROVED'); ?></span>
								</span>
							</a>
						<?php } ?>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage')) { ?>
						<a class="glyph member hasTip" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=membership&gid=' . $row->cn); ?>" title="<?php echo Lang::txt('COM_GROUPS_MANAGE_MEMBERSHIP') . '::' . $tip; ?>">
							<?php echo count($members); ?>
						</a>
					<?php } else { ?>
						<span class="glyph member" title="<?php echo Lang::txt('COM_GROUPS_MANAGE_MEMBERSHIP') . '::' . $tip; ?>">
							<?php echo count($members); ?>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=pages&gid=' . $row->cn); ?>"><?php echo Lang::txt('COM_GROUPS_PAGES_COUNT', $pages); ?></a>
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
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>

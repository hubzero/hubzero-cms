<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	->css('selector')
	->js();

// List sorting
$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$whatsleft  = $this->total - $this->filters['start'] - $this->filters['limit'];
$prev_start = $this->filters['start'] - $this->filters['limit'];
$prev_start = $prev_start < 0 ? 0 : $prev_start;
$next_start = $this->filters['start'] + $this->filters['limit'];

$group = $this->model->groupOwner();

$members = array();
$managers = array();
if ($group)
{
	$members = $group->get('members');
	$managers = $group->get('managers');
}
$roles = [
	'COLLABORATORS' => 0,
	'MANAGERS' => 1,
	'REVIEWERS' => 5
];
?>

<?php if ($this->model->groupOwner()) { ?>
	<?php
	$team = $this->model->table('Owner')->getIds($this->model->get('id'), 'all', 1);

	// See who's in members but not in the team
	$notteam = array_diff($members, $team);
	?>
	<fieldset class="group-options">
		<legend><?php echo Lang::txt('PLG_PROJECTS_TEAM_GROUP_MEMBERS'); ?>:</legend>

		<p class="notice"><?php echo Lang::txt('PLG_PROJECTS_TEAM_GROUP_PROJECT_EDITING'); ?></p>

		<div class="input-wrap <?php if ($this->model->get('sync_group') != 0) { echo 'active'; } ?>">
			<label for="membership_sync">
				<input class="option" data-action="syncall" name="sync_group" id="membership_sync" type="radio" value="1" <?php if ($this->model->get('sync_group') != 0) { echo ' checked="checked"'; } ?> />
				<span class="label-text"><?php echo Lang::txt('PLG_PROJECTS_TEAM_GROUP_LABEL_SYNC'); ?></span>
			</label>

			<select id="sync-role-selector" name="syncRole" hidden>
				<?php foreach ($roles as $description => $value): ?>
					<option value="" selected disabled hidden>
						<?php echo Lang::txt('PLG_PROJECTS_TEAM_SYNCING_DEFAULT'); ?>
					</option>
					<option value="<?php echo $value; ?>">
						<?php echo Lang::txt("PLG_PROJECTS_TEAM_SYNCING_$description"); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<div class="group-action group-action-syncall">
				<?php if (count($notteam)) { ?>
					<p><input type="submit" class="btn option" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_GROUP_SYNC'); ?>" /></p>
				<?php } else { ?>
					<p class="icon-success"><?php echo Lang::txt('PLG_PROJECTS_TEAM_GROUP_MEMBERS_TEAM'); ?></p>
				<?php } ?>
			</div>
		</div>

		<div class="input-wrap <?php if ($this->model->get('sync_group') == 0) { echo 'active'; } ?>">
			<label for="membership_custom">
				<input class="option" data-action="selective" name="sync_group" id="membership_custom" type="radio" value="0" <?php if ($this->model->get('sync_group') == 0) { echo ' checked="checked"'; } ?> />
				<span class="label-text"><?php echo Lang::txt('PLG_PROJECTS_TEAM_GROUP_LABEL_SELECT'); ?></span>
			</label>

			<div class="group-action group-action-selective">
				<?php if (count($notteam)) { ?>
					<p><a class="btn icon-group" id="choosemember" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=' . $this->task . '&alias=' . $this->model->get('alias') . '&active=team&action=choose'); ?>"><?php echo Lang::txt('PLG_PROJECTS_TEAM_GROUP_SELECT'); ?></a></p>
				<?php } else { ?>
					<p class="icon-info"><?php echo Lang::txt('PLG_PROJECTS_TEAM_GROUP_MEMBERS_TEAM'); ?></p>
				<?php } ?>
			</div>
		</div>
	</fieldset>
<?php } ?>

<fieldset>
	<?php if (!$this->setup): ?>
		<legend><?php echo Lang::txt('PLG_PROJECTS_TEAM_ADD_NEW_MEMBERS') . ' ' . Lang::txt('PLG_PROJECTS_TEAM_AS') . ':'; ?></legend>
	<?php else: ?>
		<legend><?php echo Lang::txt('PLG_PROJECTS_TEAM_PER_USER'); ?>:</legend>
	<?php endif; ?>

	<div class="combine_options">
		<label for="role_owner">
			<?php if ($this->setup) { ?>
				<?php echo Lang::txt('PLG_PROJECTS_TEAM_AS'); ?>:
			<?php } ?>
			<input class="option" name="role" id="role_owner" type="radio" value="1"  />
			<?php echo Lang::txt('PLG_PROJECTS_TEAM_LABEL_OWNERS'); ?>
		</label>
		<span class="and_or"><?php echo Lang::txt('PLG_PROJECTS_TEAM_OR'); ?></span>
		<label for="role_collaborator">
			<input class="option" name="role" id="role_collaborator" type="radio" value="0" checked="checked" />
			<?php echo Lang::txt('PLG_PROJECTS_TEAM_LABEL_COLLABORATORS'); ?>
		</label>
		<span class="and_or"><?php echo Lang::txt('PLG_PROJECTS_TEAM_OR'); ?></span>
		<label for="role_reviewer">
			<input class="option" name="role" id="role_reviewer" type="radio" value="5" />
			<?php echo Lang::txt('PLG_PROJECTS_TEAM_LABEL_REVIEWERS'); ?>
		</label>
	</div>

	<p class="hint"><?php echo Lang::txt('PLG_PROJECTS_TEAM_ADD_TEAM_HINT'); ?></p>

	<div class="add-team">
		<label id="add-users">
			<span class="instr i_user"><?php echo Lang::txt('PLG_PROJECTS_TEAM_ADD_IND_USER'); ?>:</span>
			<?php
				$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'newmember', 'newmember')));
				if (count($mc) > 0) {
					echo $mc[0];
				} else { ?>
					<input type="text" name="newmember" id="newmember" value="" size="35" />
				<?php } ?>
		</label>
		<span class="or_separator"><?php echo strtoupper(Lang::txt('PLG_PROJECTS_TEAM_OR')); ?></span>
		<label id="add-groups">
			<span class="instr i_group"><?php echo Lang::txt('PLG_PROJECTS_TEAM_ADD_GROUP_OF_USERS'); ?>:</span>
			<?php
				$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('groups', 'newgroup', 'newgroup')));
				if (count($mc) > 0) {
					echo $mc[0];
				} else { ?>
					<input type="text" name="newgroup" id="newgroup" value="" size="35" maxlength="200" />
				<?php } ?>
		</label>
		<input type="submit" id="team-save" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_ADD'); ?>" class="btn yesbtn" />
	</div>
</fieldset>

<div id="team-spacer">
	<div class="list-editing">
		<p>
			<span><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TEAM_TOTAL_MEMBERS')); ?>: <span class="prominent"><?php echo $this->total; ?></span></span>
			<span id="team-manage" class="manage-options hidden">
				<span class="faded"><?php echo Lang::txt('PLG_PROJECTS_TEAM_EDIT_ROLE'); ?></span>
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=team&action=delete'); ?>" class="manage" id="t-delete" ><?php echo Lang::txt('PLG_PROJECTS_TEAM_DELETE'); ?></a>
			</span>
		</p>
	</div>
</div>

<table id="teamlist" class="listing">
	<thead>
		<tr>
			<th class="checkbox"></th>
			<th class="th_image"></th>
			<th class="th_user i_user <?php if ($this->filters['sortby'] == 'name') { echo 'activesort'; } ?>">
				<a class="re_sort" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=' . $this->task . '&alias=' . $this->model->get('alias') . '&active=team&sortby=name&sortdir=' . $sortbyDir); ?>">
					<?php echo Lang::txt('PLG_PROJECTS_TEAM_NAME'); ?>
				</a>
			</th>
			<th<?php if ($this->filters['sortby'] == 'role') { echo ' class="activesort"'; } ?>>
				<a class="re_sort" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=' . $this->task . '&alias=' . $this->model->get('alias') . '&active=team&sortby=role&sortdir=' . $sortbyDir) . '#teamlist'; ?>">
					<?php echo Lang::txt('PLG_PROJECTS_TEAM_ROLE'); ?>
				</a>
			</th>
			<th<?php if ($this->filters['sortby'] == 'status') { echo ' class="activesort"'; } ?>>
				<a class="re_sort" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=' . $this->task . '&alias=' . $this->model->get('alias') . '&active=team&sortby=status&sortdir=' . $sortbyDir . '#teamlist'); ?>">
					<?php echo Lang::txt('PLG_PROJECTS_TEAM_JOINED'); ?>
				</a>
			</th>
			<th>
			</th>
			<th class="i_group <?php if ($this->filters['sortby'] == 'group') { echo 'activesort'; } ?>">
				<?php if ($this->count_groups > 0) { ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=' . $this->task . '&alias=' . $this->model->get('alias') . '&active=team&sortby=group&sortdir=' . $sortbyDir . '#teamlist'); ?>" class="re_sort" ><?php } ?>
					<?php echo Lang::txt('PLG_PROJECTS_TEAM_ADDED_AS_PART_OF_GROUP'); ?>
				<?php if ($this->count_groups > 0) { ?></a><?php } ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($this->team as $owner)
		{
			// Get profile thumb image
			$profile = User::getInstance($owner->userid);

			$username = $owner->username ? $owner->username : $owner->invited_email;
			$creator  = $this->model->get('owned_by_user') == $owner->userid ? 1 : 0;

			switch ($owner->role)
			{
				case \Components\Projects\Models\Orm\Owner::ROLE_MANAGER:
					$role = Lang::txt('PLG_PROJECTS_TEAM_LABEL_OWNER');
					break;
				case \Components\Projects\Models\Orm\Owner::ROLE_REVIEWER:
					$role = Lang::txt('PLG_PROJECTS_TEAM_LABEL_REVIEWER');
					break;
				case \Components\Projects\Models\Orm\Owner::ROLE_COLLABORATOR:
				default:
					$role = Lang::txt('PLG_PROJECTS_TEAM_LABEL_COLLABORATOR');
					break;
			}

			$disabled = false;
			if ($owner->native && (($group && $this->model->get('sync_group') && in_array($owner->userid, $members)) || ($this->managers_count == 1 && $owner->role == 1)))
			{
				$disabled = true;
			}

			$cls = '';
			if ($owner->userid == $this->uid)
			{
				$cls = 'native';
			}
			elseif ($owner->status == 0)
			{
				$cls = 'u_invited';
			}
			?>
			<tr class="mline <?php $cls; ?>" id="tr_<?php echo $owner->id; ?>">
				<td>
					<input type="checkbox" value="<?php echo $owner->id; ?>" name="owner[]" class="checkmember <?php if ($owner->groupid) { echo 'group:' . $owner->groupid; } ?>" data-group="<?php echo $owner->groupid; ?>" <?php if ($disabled) { echo 'disabled="disabled"'; } ?> />
				</td>
				<td class="imagebox">
					<span class="user-img-wrap">
						<img src="<?php echo $profile->picture(); ?>" alt="<?php echo $this->escape($owner->fullname); ?>" />
					</span>
				</td>
				<td>
					<?php echo $owner->fullname; ?><span class="block mini short prominent"><?php echo $username; ?></span>
				</td>
				<td class="mini nobsp">
					<?php if (!$creator) { ?>
						<span data-owner="<?php echo $owner->id; ?>" data-role="<?php echo $owner->role; ?>" class="frole owner:<?php echo $owner->id; ?> role:<?php echo $owner->role; ?>" id="r<?php echo $owner->id; ?>">
					<?php } ?>
					<?php echo $role; ?>
					<?php if (!$creator) { ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-5">
					<?php if ($owner->status == 1): ?>
						<time datetime="<?php echo Date::of($owner->added)->format('Y-m-d\TH:i:s\Z');?>">
							<?php echo Date::of($owner->added)->toLocal('M d, Y'); ?>
						</time>
					<?php elseif ($owner->status == 3): ?>
						<span class="invited"><?php echo Lang::txt('PLG_PROJECTS_TEAM_REQUESTED'); ?></span>
					<?php else: ?>
						<span class="invited"><?php echo Lang::txt('PLG_PROJECTS_TEAM_INVITED'); ?></span>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($owner->status == 3): ?>
						<a id="<?php echo 'form-' . $owner->id;?>"
							href="<?php echo Route::url('index.php?option=com_projects&alias=' .
								$this->model->get('alias') . '&task=team&action=approvemembership&owner=' . $owner->userid . '&' . Session::getFormToken() . '=1');?>"
							class="btn btn-success">
							<?php echo Lang::txt('PLG_PROJECTS_TEAM_APPROVE_REQUEST'); ?>
						</a>
						<a id="<?php echo 'form-' . $owner->id;?>"
							href="<?php echo Route::url('index.php?option=com_projects&alias=' .
								$this->model->get('alias') . '&task=team&action=denymembership&owner=' . $owner->userid . '&' . Session::getFormToken() . '=1');?>"
							class="btn btn-danger modal">
							<?php echo Lang::txt('PLG_PROJECTS_TEAM_DENY_REQUEST'); ?>
						</a>
					<?php endif; ?>
				</td>
				<td>
					<?php echo $owner->groupdesc ? \Hubzero\Utility\Str::truncate($owner->groupdesc, 30) : ''; ?>
					<span class="block mini short prominent"><?php echo $owner->groupname; ?></span>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>
<div class="nav_pager">
	<p>
		<?php if ($this->filters['start'] == 0) { ?>
			<span>&laquo; <?php echo Lang::txt('PLG_PROJECTS_TEAM_PREVIOUS'); ?></span>
		<?php } else { ?>
			<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=' . $this->task . '&active=team&sortby=' . $this->filters['sortby'] . '&start=' . $prev_start . '&sortdir=' . $this->filters['sortdir']); ?>">&laquo; <?php echo Lang::txt('PLG_PROJECTS_TEAM_PREVIOUS'); ?></a>
		<?php } ?><span>&nbsp; | &nbsp;</span>
		<?php if ($whatsleft <= 0 or $this->filters['limit'] == 0) { ?>
			<span><?php echo Lang::txt('PLG_PROJECTS_TEAM_NEXT'); ?> &raquo;</span>
		<?php } else { ?>
			<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=' . $this->task . '&active=team&sortby=' . $this->filters['sortby'] . '&start=' . $next_start . '&sortdir=' . $this->filters['sortdir']); ?>"><?php echo Lang::txt('PLG_PROJECTS_TEAM_NEXT'); ?> &raquo;</a>
		<?php } ?>
	</p>
</div>

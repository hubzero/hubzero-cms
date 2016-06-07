<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	 ->js();

// List sorting
$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$whatsleft  = $this->total - $this->filters['start'] - $this->filters['limit'];
$prev_start = $this->filters['start'] - $this->filters['limit'];
$prev_start = $prev_start < 0 ? 0 : $prev_start;
$next_start = $this->filters['start'] + $this->filters['limit'];

?>
<?php if (!$this->setup) { ?>
	<h5><?php echo Lang::txt('PLG_PROJECTS_TEAM_ADD_NEW_MEMBERS') . ' ' . Lang::txt('PLG_PROJECTS_TEAM_AS') . ':'; ?></h5>
<?php } ?>

<div class="combine_options">
	 <label>
		<?php if ($this->setup) { ?>
			<?php echo Lang::txt('PLG_PROJECTS_TEAM_AS'); ?>:
		<?php } ?>
		 <input class="option" name="role" id="role_owner" type="radio" value="1"  />
		<?php echo Lang::txt('PLG_PROJECTS_TEAM_LABEL_OWNERS'); ?>
	 </label>
	 <label>
		<span class="and_or"><?php echo Lang::txt('PLG_PROJECTS_TEAM_OR'); ?></span>
		<input class="option" name="role" id="role_collaborator" type="radio" value="0" checked="checked" />
		<?php echo Lang::txt('PLG_PROJECTS_TEAM_LABEL_COLLABORATORS'); ?>
	</label>
	 <label>
		<span class="and_or"><?php echo Lang::txt('PLG_PROJECTS_TEAM_OR'); ?></span>
		<input class="option" name="role" id="role_reviewer" type="radio" value="5" />
		<?php echo Lang::txt('PLG_PROJECTS_TEAM_LABEL_REVIEWERS'); ?>
	</label>
</div>
<p class="hint"><?php echo Lang::txt('PLG_PROJECTS_TEAM_ADD_TEAM_HINT'); ?></p>

<div class="add-team">
	<label id="add-users">
		 <span class="instr i_user"><?php echo Lang::txt('PLG_PROJECTS_TEAM_ADD_IND_USER'); ?>:</span>
		<?php
			$mc = Event::trigger( 'hubzero.onGetMultiEntry', array(array('members', 'newmember', 'newmember')) );
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
			$mc = Event::trigger( 'hubzero.onGetMultiEntry', array(array('groups', 'newgroup', 'newgroup')) );
			if (count($mc) > 0) {
				echo $mc[0];
			} else { ?>
				<input type="text" name="newgroup" id="newgroup" value="" size="35" maxlength="200" />
			<?php } ?>
	</label>
	 <input type="submit" id="team-save" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_ADD'); ?>" class="btn yesbtn" />
</div>
<div id="team-spacer">
	<?php if ($this->model->groupOwner()) { ?>
	<p class="notice"><?php echo Lang::txt('PLG_PROJECTS_TEAM_GROUP_PROJECT_EDITING'); ?></p>
	<?php } ?>
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
				<th class="th_user i_user <?php if ($this->filters['sortby'] == 'name') { echo 'activesort'; } ?>"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=' . $this->task . '&alias=' . $this->model->get('alias') . '&active=team&sortby=name&sortdir=' . $sortbyDir); ?>" class="re_sort"><?php echo Lang::txt('PLG_PROJECTS_TEAM_NAME'); ?></a></th>
				<th<?php if ($this->filters['sortby'] == 'role') { echo ' class="activesort"'; } ?>><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=' . $this->task . '&alias=' . $this->model->get('alias') . '&active=team&sortby=role&sortdir=' . $sortbyDir) . '#teamlist'; ?>" class="re_sort"><?php echo Lang::txt('PLG_PROJECTS_TEAM_ROLE'); ?></a></th>
				<th<?php if ($this->filters['sortby'] == 'status') { echo ' class="activesort"'; } ?>><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=' . $this->task . '&alias=' . $this->model->get('alias') . '&active=team&sortby=status&sortdir=' . $sortbyDir . '#teamlist'); ?>" class="re_sort"><?php echo Lang::txt('PLG_PROJECTS_TEAM_JOINED'); ?></a></th>
				<th class="i_group <?php if ($this->filters['sortby'] == 'group') { echo 'activesort'; } ?>"><?php if ( $this->count_groups > 0 ) { ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=' . $this->task . '&alias=' . $this->model->get('alias') . '&active=team&sortby=group&sortdir=' . $sortbyDir . '#teamlist'); ?>" class="re_sort" ><?php } ?><?php echo Lang::txt('PLG_PROJECTS_TEAM_ADDED_AS_PART_OF_GROUP'); ?><?php if ( $this->count_groups > 0 ) { ?></a><?php } ?></th>
			</tr>
		</thead>
		<tbody>
<?php foreach ($this->team as $owner)
	{
					// Get profile thumb image
					$profile = User::getInstance($owner->userid);
					$actor   = User::getInstance($this->uid);
					$thumb   = $profile->get('id') ? $profile->picture() : $actor->picture(true);

					$username = $owner->username ? $owner->username : $owner->invited_email;
					$creator = $this->model->get('owned_by_user') == $owner->userid ? 1 : 0;

					switch ($owner->role)
					{
						case '1':
							$role = Lang::txt('PLG_PROJECTS_TEAM_LABEL_OWNER');
							break;
						case '2':
						default:
							$role = Lang::txt('PLG_PROJECTS_TEAM_LABEL_COLLABORATOR');
							break;
						case '5':
							$role = Lang::txt('PLG_PROJECTS_TEAM_LABEL_REVIEWER');
							break;
					}
?>
			<tr class="mline <?php if ($owner->userid == $this->uid) { echo 'native'; } else if ($owner->status == 0) { echo 'u_invited'; } ?>" id="tr_<?php echo $owner->id; ?>">
				<td><input type="checkbox" value="<?php echo $owner->id?>" name="owner[]" class="checkmember <?php if ($owner->groupid) { echo 'group:' . $owner->groupid; } ?>"  <?php if ($owner->native && ($this->model->groupOwner() || ($this->managers_count == 1 && $owner->role == 1))) { echo 'disabled="disabled"'; } ?> /></td>
				<td class="imagebox"><img src="<?php echo $thumb; ?>" alt="<?php echo $owner->fullname; ?>" /></td>
				<td><?php echo $owner->fullname; ?><span class="block mini short prominent"><?php echo $username; ?></span></td>
				<td class="mini nobsp"><?php if (!$creator) { ?><span class="frole owner:<?php echo $owner->id; ?> role:<?php echo $owner->role; ?>" id="r<?php echo $owner->id; ?>"><?php } ?><?php echo $role; ?><?php if (!$creator) { ?></span><?php } ?></td>
				<td class="mini"><?php echo $owner->status == 1 ? Date::of($owner->added)->toLocal('M d, Y') : '<span class="invited">' . Lang::txt('PLG_PROJECTS_TEAM_INVITED') . '</span>';  ?></td>
				<td><?php echo $owner->groupdesc ? \Hubzero\Utility\String::truncate($owner->groupdesc, 30) : ''; ?><span class="block mini short prominent"><?php echo $owner->groupname; ?></span></td>
			</tr>
<?php } ?>
			</tbody>
			</table>
	<div class="nav_pager"><p>
		<?php
		if ($this->filters['start'] == 0) {	?>
			<span>&laquo; <?php echo Lang::txt('PLG_PROJECTS_TEAM_PREVIOUS'); ?></span>
		<?php	} else {  ?>
			<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=' . $this->task . '&active=team&sortby=' . $this->filters['sortby'] . '&start=' . $prev_start . '&sortdir=' . $this->filters['sortdir']); ?>">&laquo; <?php echo Lang::txt('PLG_PROJECTS_TEAM_PREVIOUS'); ?></a>
		<?php } ?><span>&nbsp; | &nbsp;</span>
		<?php
		if ( $whatsleft <= 0 or $this->filters['limit'] == 0 ) { ?>
			<span><?php echo Lang::txt('PLG_PROJECTS_TEAM_NEXT'); ?> &raquo;</span>
		<?php	} else { ?>
			<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=' . $this->task . '&active=team&sortby=' . $this->filters['sortby'] . '&start=' . $next_start . '&sortdir=' . $this->filters['sortdir']); ?>"><?php echo Lang::txt('PLG_PROJECTS_TEAM_NEXT'); ?> &raquo;</a>
		<?php } ?></p>
	</div>

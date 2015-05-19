<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$sortAppend = '&sortdir=' . urlencode($sortbyDir);

?>
<div id="plg-header">
	<h3 class="team"><?php echo $this->title; ?></h3>
</div>
<?php if ($this->model->access('manager')) { ?>
<ul id="page_options" class="pluginOptions">
	<li>
		<a class="icon-edit btn"  href="<?php echo Route::url($this->model->link('team')); ?>">
			<?php echo Lang::txt('COM_PROJECTS_EDIT_TEAM'); ?>
		</a>
	</li>
</ul>
<?php } ?>
<form id="plg-form" method="post" action="<?php echo Route::url($this->model->link('team')); ?>">
 <div>
	<input type="hidden" id="id" name="id" value="<?php echo $this->model->get('id'); ?>" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="action" value="team" />
</div>
<div class="container">
	<div class="list-menu">
		<ul class="entries-menu order-options">
			<li>
				<a class="sort-title<?php if ($this->filters['sortby'] == 'date') { echo ' active'; } ?>" href="<?php echo Route::url($this->model->link('team') . $sortAppend . '&sortby=date'); ?>" title="<?php echo Lang::txt('COM_PROJECTS_SORT_BY') . ' ' . strtolower(Lang::txt('COM_PROJECTS_JOINED')); ?>">
					&darr; <?php echo Lang::txt('COM_PROJECTS_JOINED'); ?>
				</a>
			</li>
			<li>
				<a class="sort-title<?php if ($this->filters['sortby'] == 'role') { echo ' active'; } ?>" href="<?php echo Route::url($this->model->link('team') . $sortAppend . '&sortby=role'); ?>" title="<?php echo Lang::txt('COM_PROJECTS_SORT_BY') . ' ' . strtolower(Lang::txt('COM_PROJECTS_ROLE')); ?>">
					&darr; <?php echo Lang::txt('COM_PROJECTS_ROLE'); ?>
				</a>
			</li>
			<li>
				<a class="sort-title<?php if ($this->filters['sortby'] == 'name') { echo ' active'; } ?>" href="<?php echo Route::url($this->model->link('team') . $sortAppend . '&sortby=name'); ?>" title="<?php echo Lang::txt('COM_PROJECTS_SORT_BY') . ' ' . strtolower(Lang::txt('COM_PROJECTS_NAME')); ?>">
					&darr; <?php echo Lang::txt('COM_PROJECTS_NAME'); ?>
				</a>
			</li>
		</ul>
		<p class="msg-total"><?php echo ucfirst(Lang::txt('COM_PROJECTS_SHOWING')); ?> <?php if ($this->total <= count($this->team)) { echo Lang::txt('COM_PROJECTS_ALL'); } ?> <span class="prominent"><?php echo count($this->team); ?></span>  <?php if ($this->total > count($this->team)) { echo Lang::txt('COM_PROJECTS_OUT_OF') . ' ' . $this->total; } ?> <?php echo Lang::txt('COM_PROJECTS_TEAM_MEMBERS'); ?></p>
	</div>
	<table id="teamlist" class="listing">
		<thead>
			<tr>
				<th class="imagebox"></th>
				<th class="th_user i_user"><?php echo Lang::txt('COM_PROJECTS_NAME'); ?></th>
				<th><?php echo Lang::txt('COM_PROJECTS_ROLE'); ?></th>
				<th><?php echo Lang::txt('COM_PROJECTS_JOINED'); ?></th>
				<?php if ($this->count_groups) { ?>
				<th class="i_group"></th>
				<?php } ?>
				<th><?php echo Lang::txt('COM_PROJECTS_TEAM_LAST_VISIT'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php foreach ($this->team as $owner)
	{
					// Get profile thumb image
					$profile = \Hubzero\User\Profile::getInstance($owner->userid);
					$actor   = \Hubzero\User\Profile::getInstance($this->uid);
					$thumb   = $profile ? $profile->getPicture() : $actor->getPicture(true);

					$timecheck = date('Y-m-d H:i:s', time() - (15 * 60));
					$lastvisit = $owner->lastvisit && $owner->lastvisit != '0000-00-00 00:00:00'
								? \Components\Projects\Helpers\Html::timeAgo($owner->lastvisit) . ' ' . Lang::txt('COM_PROJECTS_AGO')
								: Lang::txt('COM_PROJECTS_NEVER');
					$lastvisit = $owner->userid == $this->uid || ($owner->online && $owner->lastvisit > $timecheck)
								? '<span class="online">' . Lang::txt('COM_PROJECTS_TEAM_ONLINE_NOW') . '</span>'
								: $lastvisit;

					// User deleted?
					// Edge case!
					if ($owner->userid && !$owner->username)
					{
						$objO = new \Components\Projects\Tables\Owner($this->database);
						$objO->load($owner->id);
						$objO->status = 2;
						$objO->store();
						continue;
					}

					$creator = $this->model->owner('id') == $owner->userid ? 1 : 0;

					// Determine css class for user
					switch ($owner->role)
					{
						case '1':
							$role = Lang::txt('COM_PROJECTS_LABEL_OWNER');
							break;
						case '2':
						default:
							$role = Lang::txt('COM_PROJECTS_LABEL_COLLABORATOR');
							break;
						case '5':
							$role = Lang::txt('COM_PROJECTS_LABEL_REVIEWER');
							break;
					}
					$username = $owner->username ? $owner->username : $owner->invited_email;
?>
			<tr class="mline <?php if ($owner->userid == $this->uid) { echo 'native'; } ?>" id="tr_<?php echo $owner->id; ?>">
				<td class="imagebox"><a href="/members/<?php echo $owner->userid; ?>" <?php if ($owner->fullname) { ?>title="<?php echo htmlentities($owner->fullname) . ' (' . $owner->userid . ')'; ?>"<?php } ?>><img src="<?php echo $thumb; ?>" alt="<?php echo $owner->fullname ? htmlentities($owner->fullname) : ''; ?>" /></a></td>
				<td><?php echo $owner->fullname; ?><span class="block mini short prominent"><?php echo $username; ?></span></td>
				<td class="mini"><?php echo $role; ?></td>
				<td class="mini"><?php echo $owner->status == 1 ? Date::of($owner->added)->toLocal('M d, Y') : '<span class="invited">' . Lang::txt('COM_PROJECTS_INVITED').'</span>';  ?></td>
				<?php if ($this->count_groups) { ?>
				<td><?php echo $owner->groupdesc ? \Hubzero\Utility\String::truncate($owner->groupdesc, 30) : ''; ?><span class="block mini short prominent"><?php echo $owner->groupname; ?></span></td>
				<?php } ?>
				<td class="mini"><?php echo $lastvisit; ?></td>
			</tr>
<?php } ?>
			</tbody>
			</table>
			<?php
			// Pagination
			$pageNav = $this->pagination(
				$this->total,
				$this->filters['start'],
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
			$pageNav->setAdditionalUrlParam('sortdir', $this->filters['sortdir']);

			$pagenavhtml = $pageNav->render();
			?>
			<fieldset>
				<?php echo $pagenavhtml; ?>
			</fieldset>
	</div>
</form>

<?php if ((!$this->model->access('manager') || $this->managers_count > 1) && !$this->setup && !$this->model->groupOwner()) { ?>
<p class="extras"><span class="rightfloat"><a href="<?php echo Route::url($this->model->link('team') . '&action=quit'); ?>"><?php echo Lang::txt('COM_PROJECTS_LEAVE_PROJECT'); ?></a></span></p>
<?php } ?>

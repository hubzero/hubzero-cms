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
$whatsleft  = $this->total - $this->filters['start'] - $this->filters['limit'];
$prev_start = $this->filters['start'] - $this->filters['limit'];
$prev_start = $prev_start < 0 ? 0 : $prev_start;
$next_start = $this->filters['start'] + $this->filters['limit'];

$goto  = '&alias=' . $this->model->get('alias');

?>
<div id="plg-header">
	<h3 class="team"><?php echo $this->title; ?></h3>
</div>

<form id="plg-form" method="post" action="<?php echo Route::url('index.php?option=' . $this->option . $goto . '&active=team'); ?>">
 <div>
	<input type="hidden" id="id" name="id" value="<?php echo $this->model->get('id'); ?>" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="action" value="team" />
</div>
<div class="list-editing">
 <p><?php echo ucfirst(Lang::txt('COM_PROJECTS_SHOWING')); ?> <?php if ($this->total <= count($this->team)) { echo Lang::txt('COM_PROJECTS_ALL'); }?> <span class="prominent"><?php echo count($this->team); ?></span>  <?php if ($this->total > count($this->team)) { echo Lang::txt('COM_PROJECTS_OUT_OF') . ' ' . $this->total; }?> <?php echo Lang::txt('COM_PROJECTS_TEAM_MEMBERS'); ?>
	<?php if ($this->model->access('manager')) { ?>
	<span class="editlink"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit' . $goto . '&active=team'); ?>"><?php echo Lang::txt('COM_PROJECTS_EDIT_TEAM'); ?></a></span>
	<?php } ?></p>
</div>
	<table id="teamlist" class="listing">
		<thead>
			<tr>
				<th class="th_image"></th>
				<th class="th_user i_user <?php if ($this->filters['sortby'] == 'name') { echo 'activesort'; } ?>"><a href="<?php echo Route::url('index.php?option=' . $this->option . $goto . '&active=team') . '/?action=view&amp;t_sortby=name&amp;t_sortdir=' . $sortbyDir; ?>" class="re_sort ajax_action" title="<?php echo Lang::txt('COM_PROJECTS_SORT_BY') . ' ' . Lang::txt('COM_PROJECTS_NAME'); ?>"><?php echo Lang::txt('COM_PROJECTS_NAME'); ?></a></th>
				<th<?php if ($this->filters['sortby'] == 'role') { echo ' class="activesort"'; } ?>><a href="<?php echo Route::url('index.php?option=' . $this->option . $goto . '&active=team') . '/?action=view&amp;t_sortby=role&amp;t_sortdir=' . $sortbyDir; ?>" class="re_sort ajax_action" title="<?php echo Lang::txt('COM_PROJECTS_SORT_BY') . ' ' . Lang::txt('COM_PROJECTS_ROLE'); ?>"><?php echo Lang::txt('COM_PROJECTS_ROLE'); ?></a></th>
				<th<?php if ($this->filters['sortby'] == 'date') { echo ' class="activesort"'; } ?>><a href="<?php echo Route::url('index.php?option=' . $this->option . $goto . '&active=team') . '/?action=view&amp;t_sortby=date&amp;t_sortdir=' . $sortbyDir; ?>" class="re_sort" title="<?php echo Lang::txt('COM_PROJECTS_SORT_BY') . ' ' . Lang::txt('COM_PROJECTS_JOINED'); ?>"><?php echo Lang::txt('COM_PROJECTS_JOINED'); ?></a></th>
				<th class="i_group <?php if ($this->filters['sortby'] == 'group') { echo 'activesort'; } ?>"><?php if ( $this->count_groups > 0 ) { ?><a href="<?php echo Route::url('index.php?option=' . $this->option . $goto . '&active=team') . '/?action=view&amp;t_sortby=group&amp;t_sortdir=' . $sortbyDir; ?>" class="re_sort" title="<?php echo Lang::txt('COM_PROJECTS_SORT_BY') . ' ' . Lang::txt('COM_PROJECTS_ADDED_AS_PART_OF_GROUP'); ?>"><?php } ?><?php echo Lang::txt('COM_PROJECTS_ADDED_AS_PART_OF_GROUP'); ?><?php if ( $this->count_groups > 0 ) { ?></a><?php } ?></th>
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
					$usr_class = $owner->status == 0 ? ' class="userinvited"' : ' class="useractive"';
					$usr_class = ($creator || ($this->model->groupOwner('id') && $owner->native))
						? ' class="userowner"' : $usr_class;
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
				<td<?php echo $usr_class; ?>><a href="/members/<?php echo $owner->userid; ?>" <?php if ($owner->fullname) { ?>title="<?php echo htmlentities($owner->fullname) . ' (' . $owner->userid . ')'; ?>"<?php } ?>><img width="30" height="30" src="<?php echo $thumb; ?>" alt="<?php echo $owner->fullname ? htmlentities($owner->fullname) : ''; ?>" /></a></td>
				<td><?php echo $owner->fullname; ?><span class="block mini short prominent"><?php echo $username; ?></span></td>
				<td class="mini"><?php echo $role; ?></td>
				<td class="mini"><?php echo $owner->status == 1 ? JHTML::_('date', $owner->added, 'M d, Y') : '<span class="invited">' . Lang::txt('COM_PROJECTS_INVITED').'</span>';  ?></td>
				<td><?php echo $owner->groupdesc ? \Hubzero\Utility\String::truncate($owner->groupdesc, 30) : ''; ?><span class="block mini short prominent"><?php echo $owner->groupname; ?></span></td>
				<td class="mini"><?php echo $lastvisit; ?></td>
			</tr>
<?php } ?>
			</tbody>
			</table>
			<div class="nav_pager">
				<p>
				<?php
				if ($this->filters['start'] == 0) {	?>
					<span>&laquo; <?php echo Lang::txt('COM_PROJECTS_PREVIOUS'); ?></span>
				<?php	} else {  ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . $goto . '&active=team') . '/?action=view&amp;t_sortby=' . $this->filters['sortby'] . '&amp;t_limitstart=' . $prev_start . '&amp;t_sortdir=' . $this->filters['sortdir']; ?>" class="ajax_action">&laquo; <?php echo Lang::txt('COM_PROJECTS_PREVIOUS'); ?></a>
				<?php } ?><span>&nbsp; | &nbsp;</span>
				<?php
				if ( $whatsleft <= 0 or $this->filters['limit'] == 0 ) { ?>
					<span><?php echo Lang::txt('COM_PROJECTS_NEXT'); ?> &raquo;</span>
				<?php	} else { ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . $goto . '&active=team') . '/?action=view&amp;t_sortby=' . $this->filters['sortby'] . '&amp;t_limitstart=' . $next_start . '&amp;t_sortdir=' . $this->filters['sortdir']; ?>" class="ajax_action"><?php echo Lang::txt('COM_PROJECTS_NEXT'); ?> &raquo;</a>
				<?php } ?>
				</p>
			</div>
			<?php if ((!$this->model->access('manager') || $this->managers_count > 1) && !$this->setup && !$this->model->groupOwner()) { ?>
			<p class="extras"><a href="<?php echo Route::url('index.php?option=' . $this->option . $goto . '&active=team&action=quit'); ?>"><?php echo Lang::txt('COM_PROJECTS_LEAVE_PROJECT'); ?></a></p>
			<?php } ?>
</form>

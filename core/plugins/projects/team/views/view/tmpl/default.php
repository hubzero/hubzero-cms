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

$this->css();

$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$sortAppend = '&sortdir=' . urlencode($sortbyDir);

?>
<div id="plg-header">
	<h3 class="team"><?php echo $this->title; ?></h3>
</div>
<?php if ($this->model->access('manager')) { ?>
	<ul id="page_options" class="pluginOptions">
		<li>
			<a class="icon-edit btn"  href="<?php echo Route::url($this->model->link() . '&task=edit&active=team'); ?>">
				<?php echo Lang::txt('PLG_PROJECTS_TEAM_EDIT_TEAM'); ?>
			</a>
		</li>
	</ul>
<?php } ?>
<form id="plg-form" method="post" action="<?php echo Route::url($this->model->link('team')); ?>">
	<input type="hidden" id="id" name="id" value="<?php echo $this->model->get('id'); ?>" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="action" value="team" />

	<div class="container">
		<!-- <p class="msg-total"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TEAM_SHOWING')); ?> <?php if ($this->total <= count($this->team)) { echo Lang::txt('PLG_PROJECTS_TEAM_ALL'); } ?> <span class="prominent"><?php echo count($this->team); ?></span>  <?php if ($this->total > count($this->team)) { echo Lang::txt('PLG_PROJECTS_TEAM_OUT_OF') . ' ' . $this->total; } ?> <?php echo Lang::txt('PLG_PROJECTS_TEAM_MEMBERS'); ?></p> -->

		<table id="teamlist" class="listing">
			<thead>
				<tr>
					<th class="priority-3 imagebox"></th>
					<th>
						<a class="sort-title <?php if ($this->filters['sortby'] == 'name') { echo strtolower($this->filters['sortdir']) . ' active'; } ?>" href="<?php echo Route::url($this->model->link('team') . $sortAppend . '&sortby=name'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TEAM_SORT_BY', strtolower(Lang::txt('PLG_PROJECTS_TEAM_NAME'))); ?>">
							<?php echo Lang::txt('PLG_PROJECTS_TEAM_NAME'); ?>
						</a>
					</th>
					<th>
						<a class="sort-title <?php if ($this->filters['sortby'] == 'role') { echo strtolower($this->filters['sortdir']) . ' active'; } ?>" href="<?php echo Route::url($this->model->link('team') . $sortAppend . '&sortby=role'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TEAM_SORT_BY', strtolower(Lang::txt('PLG_PROJECTS_TEAM_ROLE'))); ?>">
							<?php echo Lang::txt('PLG_PROJECTS_TEAM_ROLE'); ?>
						</a>
					</th>
					<th class="priority-5">
						<a class="sort-title <?php if ($this->filters['sortby'] == 'date') { echo strtolower($this->filters['sortdir']) . ' active'; } ?>" href="<?php echo Route::url($this->model->link('team') . $sortAppend . '&sortby=date'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TEAM_SORT_BY', strtolower(Lang::txt('PLG_PROJECTS_TEAM_JOINED'))); ?>">
							<?php echo Lang::txt('PLG_PROJECTS_TEAM_JOINED'); ?>
						</a>
					</th>
					<?php if ($this->count_groups) { ?>
						<th class="priority-2">
							<?php echo Lang::txt('PLG_PROJECTS_TEAM_GROUP'); ?>
						</th>
					<?php } ?>
					<th class="priority-4">
						<?php echo Lang::txt('PLG_PROJECTS_TEAM_LAST_VISIT'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($this->team as $owner)
			{
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

				$profile = User::getInstance($owner->userid);

				$creator = $this->model->owner('id') == $owner->userid ? 1 : 0;

				// Determine css class for user
				switch ($owner->role)
				{
					case '1':
						$role = Lang::txt('PLG_PROJECTS_TEAM_LABEL_OWNER');
						break;
					case '2':
					case '5':
						$role = Lang::txt('PLG_PROJECTS_TEAM_LABEL_REVIEWER');
						break;
					default:
						$role = Lang::txt('PLG_PROJECTS_TEAM_LABEL_COLLABORATOR');
						break;
				}
				$username = $owner->username ? $owner->username : $owner->invited_email;
				?>
				<tr class="mline <?php if ($owner->userid == $this->uid) { echo 'native'; } ?>" id="tr_<?php echo $owner->id; ?>">
					<td class="priority-3 imagebox">
						<a class="user-img-wrap" href="<?php echo Route::url('index.php?option=com_members&id=' . $owner->userid); ?>" <?php if ($owner->fullname) { ?>title="<?php echo $this->escape($owner->fullname) . ' (' . $owner->userid . ')'; ?>"<?php } ?>>
							<img src="<?php echo $profile->picture(); ?>" alt="<?php echo $owner->fullname ? $this->escape($owner->fullname) : ''; ?>" />
						</a>
					</td>
					<td>
						<?php echo $this->escape($owner->fullname); ?>
						<span class="block mini short prominent"><?php echo $this->escape($username); ?></span>
					</td>
					<td>
						<?php echo $creator && !$this->model->groupOwner() ? '<span class="prominent">' . Lang::txt('PLG_PROJECTS_TEAM_OWNER') . '</span>/' : ''; echo $role; ?>
					</td>
					<td class="priority-5">
						<?php echo $owner->status == 1 ? '<time datetime="' . Date::of($owner->added)->format('Y-m-d\TH:i:s\Z') . '">' . Date::of($owner->added)->toLocal('M d, Y') . '</time>' : '<span class="invited">' . Lang::txt('PLG_PROJECTS_TEAM_INVITED').'</span>';  ?>
					</td>
					<?php if ($this->count_groups) { ?>
						<td class="priority-2">
							<?php echo $owner->groupdesc ? \Hubzero\Utility\String::truncate($owner->groupdesc, 30) : ''; ?><span class="block mini short prominent"><?php echo $owner->groupname; ?></span>
						</td>
					<?php } ?>
					<td class="priority-4">
						<?php
						$timecheck = date('Y-m-d H:i:s', time() - (15 * 60));
						$lastvisit = Lang::txt('PLG_PROJECTS_TEAM_NEVER');
						if ($owner->lastvisit && $owner->lastvisit != '0000-00-00 00:00:00')
						{
							$lastvisit = '<time datetime="' . Date::of($owner->lastvisit)->format('Y-m-d\TH:i:s\Z') . '">' . Components\Projects\Helpers\Html::timeAgo($owner->lastvisit) . ' ' . Lang::txt('PLG_PROJECTS_TEAM_AGO') . '</time>';
						}
						if ($owner->userid == $this->uid || ($owner->online && $owner->lastvisit > $timecheck))
						{
							$lastvisit = '<span class="online">' . Lang::txt('PLG_PROJECTS_TEAM_ONLINE_NOW') . '</span>';
						}
						echo $lastvisit;
						?>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>

		<fieldset>
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

			echo $pagenavhtml;
			?>
		</fieldset>
	</div>
</form>

<?php if ((!$this->model->access('manager') || $this->managers_count > 1) && !$this->setup && !$this->model->groupOwner()) { ?>
	<p class="extras"><span class="rightfloat"><a href="<?php echo Route::url($this->model->link('team') . '&action=quit'); ?>"><?php echo Lang::txt('PLG_PROJECTS_TEAM_LEAVE_PROJECT'); ?></a></span></p>
<?php } ?>

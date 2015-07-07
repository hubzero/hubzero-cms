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

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('icons', 'com_projects')
	 ->css('emocons', 'com_projects');

$this->js();
$li = '';

if (count($this->activities) > 0 )
{
	$projects = array();
	$i = 1;

	?>
<div id="latest_activity" class="infofeed">
	<div class="activity-items" id="activity-feed">
		<?php
		// Loop through activities
		foreach ($this->activities as $activity)
		{
			$a = $activity['activity'];
			if (!isset($projects[$a->projectid]))
			{
				$projects[$a->projectid] = \Components\Projects\Models\Project::getInstance($a->projectid);
			}

			// Show activity
			$this->view('_activity')
		     ->set('model', $projects[$a->projectid])
			 ->set('activity', $activity)
			 ->set('uid', $this->uid)
			 ->set('edit', false)
			 ->set('showProject', true)
		     ->display();

			?>
		<?php
			$li = 'li_' . $a->id;
			$i++;
		} // end foreach
		?>
	</div>
</div>
<?php } else { ?>
	<p class="noresults"><?php echo Lang::txt('PLG_PROJECTS_BLOG_NO_ACTIVITIES'); ?></p>
<?php } ?>

<div class="nav_pager more-updates">
<?php
if ($this->total > $this->filters['limit']) {
	$limit = $this->filters['limit'] + $this->limit; ?>
	<p><a href="<?php echo Route::url('index.php?option=com_members&id=' . $this->uid . '&active=projects') . '?action=updates&amp;limit=' . $limit . '&amp;prev=' . $this->filters['limit'] . '#' . $li;  ?>"><?php echo Lang::txt('PLG_PROJECTS_BLOG_VIEW_OLDER_ENTRIES'); ?></a></p>
<?php } else if ($this->filters['limit'] != $this->limit) { ?>
	<p><?php echo Lang::txt('PLG_PROJECTS_BLOG_VIEW_OLDER_ENTRIES_NO_MORE'); ?></p>
<?php } ?>
</div><!-- / #more-updates -->

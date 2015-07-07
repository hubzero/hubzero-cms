<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

if (count($this->activities) > 0 ) {
?>
<div class="activity-items" id="activity-feed">
<?php
// Loop through activities
	foreach ($this->activities as $activity)
	{
		// Show activity
		$this->view('_activity')
	     ->set('option', $this->option)
	     ->set('model', $this->model)
		 ->set('activity', $activity)
		 ->set('uid', $this->uid)
	     ->display();
	}  ?>
</div>
<?php } else { ?>
<p class="noresults"><?php echo Lang::txt('PLG_PROJECTS_BLOG_NO_ACTIVITIES'); ?></p>
<?php } ?>

<div id="more-updates" class="nav_pager">
<?php
if ($this->total > $this->filters['limit'])
{
	$limit = $this->filters['limit'] + $this->limit; ?>
	<p><a href="<?php echo Route::url($this->model->link() . '&limit=' . $limit . '&amp;prev=' . $this->filters['limit']);  ?>"><?php echo Lang::txt('PLG_PROJECTS_BLOG_VIEW_OLDER_ENTRIES'); ?></a></p>
<?php } else if ($this->filters['limit'] != $this->limit) { ?>
	<p><?php echo Lang::txt('PLG_PROJECTS_BLOG_VIEW_OLDER_ENTRIES_NO_MORE'); ?></p>
<?php } else if ($this->total > 5 && $this->total < $this->filters['limit']) { ?>
	<p><?php echo Lang::txt('PLG_PROJECTS_BLOG_VIEW_OLDER_ENTRIES_NO_MORE'); ?></p>
<?php
} ?>
</div><!-- / #more-updates -->

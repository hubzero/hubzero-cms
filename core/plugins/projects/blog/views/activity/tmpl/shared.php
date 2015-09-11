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

<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

if (count($this->activities) > 0 ) { ?>
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
		}
		?>
	</div>
<?php } else { ?>
	<p class="noresults"><?php echo Lang::txt('PLG_PROJECTS_BLOG_NO_ACTIVITIES'); ?></p>
<?php } ?>

<div id="more-updates" class="nav_pager">
	<?php
	if ($this->total > $this->filters['limit'])
	{
		$limit = $this->filters['limit'] + $this->limit;
		?>
		<p><a href="<?php echo Route::url($this->model->link() . '&limit=' . $limit . '&prev=' . $this->filters['limit']);  ?>"><?php echo Lang::txt('PLG_PROJECTS_BLOG_VIEW_OLDER_ENTRIES'); ?></a></p>
	<?php } else if ($this->filters['limit'] != $this->limit) { ?>
		<p><?php echo Lang::txt('PLG_PROJECTS_BLOG_VIEW_OLDER_ENTRIES_NO_MORE'); ?></p>
	<?php } else if ($this->total > 5 && $this->total < $this->filters['limit']) { ?>
		<p><?php echo Lang::txt('PLG_PROJECTS_BLOG_VIEW_OLDER_ENTRIES_NO_MORE'); ?></p>
		<?php
	}
	?>
</div><!-- / #more-updates -->

<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	->css('icons', 'com_projects')
	->css('emocons', 'com_projects');

$this->js();
$li = '';

if (count($this->activities) > 0)
{
	$projects = array();
	$i = 1;
	?>
	<div id="latest_activity" class="infofeed">
		<div class="activity-items" id="activity-feed">
			<?php
			// get all sessions
			$online = array();

			// Loop through activities
			foreach ($this->activities as $activity)
			{
				//$a = $activity['activity'];

				if (!isset($projects[$activity->get('scope_id')]))
				{
					$projects[$activity->get('scope_id')] = \Components\Projects\Models\Project::getInstance($activity->get('scope_id'));
				}

				// Show activity
				$this->view('_activity')
					->set('model', $projects[$activity->get('scope_id')])
					->set('activity', $activity)
					->set('uid', $this->uid)
					->set('edit', false)
					->set('showProject', true)
					->set('online', $online)
					->set('option', 'com_projects')
					->display();

				$li = 'tail_' . $activity->log->get('id');
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
	if ($this->filters['start'] + $this->filters['limit'] < $this->total)
	{
		$start = $this->filters['start'] + $this->filters['limit'] - 1;

		$option = Request::getCmd('option', 'com_members');
		?>
		<p><a href="<?php echo Route::url('index.php?option=' . $option . '&' . ($option == 'com_groups' ? 'cn=' . Request::getCmd('cn') : 'id=' . $this->uid) . '&active=projects&action=updates&limit=' .  $this->filters['limit'] . '&start=' . $start . '#' . $li);  ?>"><?php echo Lang::txt('PLG_PROJECTS_BLOG_VIEW_OLDER_ENTRIES'); ?></a></p>
	<?php } else if ($this->filters['limit'] != $this->limit) { ?>
		<p><?php echo Lang::txt('PLG_PROJECTS_BLOG_VIEW_OLDER_ENTRIES_NO_MORE'); ?></p>
		<?php
	}
	?>
</div><!-- / #more-updates -->

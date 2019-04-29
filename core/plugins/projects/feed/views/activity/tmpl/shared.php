<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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

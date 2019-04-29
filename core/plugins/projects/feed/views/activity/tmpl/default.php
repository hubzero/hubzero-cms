<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (count($this->activities) > 0) { ?>
	<div class="activity-items" id="activity-feed">
		<?php
		// get all sessions
		$online = array();
		$sessions = Hubzero\Session\Helper::getAllSessions(array(
			'guest'    => 0,
			'distinct' => 1
		));
		if ($sessions)
		{
			// see if any session matches our userid
			foreach ($sessions as $session)
			{
				$online[] = $session->userid;
			}
		}

		// Loop through activities
		foreach ($this->activities as $activity)
		{
			// Show activity
			$this->view('_activity')
				->set('option', $this->option)
				->set('model', $this->model)
				->set('activity', $activity)
				->set('online', $online)
				->display();
		}
		?>
	</div>
<?php } else { ?>
	<p class="noresults"><?php echo Lang::txt('PLG_PROJECTS_BLOG_NO_ACTIVITIES'); ?></p>
<?php } ?>

<div id="more-updates" class="nav_pager">
	<?php
	$limit = $this->filters['limit'] + $this->limit;
	$start = $this->filters['limit'] + $this->filters['start'];

	if ($this->total > $start)
	{
		?>
		<p><a href="<?php echo Route::url($this->model->link('feed') . '&limit=' . $limit);  ?>"><?php echo Lang::txt('PLG_PROJECTS_BLOG_VIEW_OLDER_ENTRIES'); ?></a></p>
		<?php
	}
	else
	{
		?>
		<p><?php echo Lang::txt('PLG_PROJECTS_BLOG_VIEW_OLDER_ENTRIES_NO_MORE'); ?></p>
		<?php
	}
	?>
</div><!-- / #more-updates -->

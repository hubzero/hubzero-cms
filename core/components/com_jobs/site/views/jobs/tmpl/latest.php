<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>

<section class="main section">
	<h3><?php echo Lang::txt('COM_JOBS_LATEST_POSTINGS'); ?></h3>
	<form method="get" action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>">
		<?php if (count($this->jobs) > 0) { ?>
			<?php // Display List of items
				$view = new \Hubzero\Component\View(array(
					'base_path' => Component::path('com_jobs') . DS . 'site',
					'name'      => 'jobs',
					'layout'    => '_list'
				));
				$view->set('option', $this->option)
					->set('filters', $this->filters)
					->set('config', $this->config)
					->set('task', $this->task)
					->set('emp', $this->emp)
					->set('mini', 1)
					->set('jobs', $this->jobs)
					->set('admin', $this->admin)
				->display();
			?>
		<?php } else { ?>
		<p><?php echo Lang::txt('COM_JOBS_NO_JOBS_FOUND'); ?></p>
		<?php } ?>
	</form>
</section>

<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<form class="submit-options" method="post" action=""> <?php // echo Route::url($this->publication->link() . '&active=fork&action=fork'); ?>
	<h3><?php echo Lang::txt('COM_PUBLICATIONS_ADD_NEW_PUB'); ?></h3>
	<div class="grid">
		<div class="col span5 submit-new">
			<h4><?php echo Lang::txt('COM_PUBLICATIONS_NEW_PUB_NO_PROJECT'); ?></h4>
			<p><?php echo Lang::txt('COM_PUBLICATIONS_NEW_PUB_NO_PROJECT_EXPLANATION'); ?></p>
			<p><a class="btn btn-success" href="<?php echo Route::url('publications/submit?action=publication&base=qubesresource'); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_NEW_PUB_CREATE'); ?></a></p>
		</div>
		<div class="col span7 omega submit-to-project">
			<h4><?php echo Lang::txt('COM_PUBLICATIONS_NEW_PUB_EXISTING_PROJECT'); ?></h4>
			<?php if (count($this->projects) > 0) { ?>
				<ul>
					<?php foreach ($this->projects as $project) { ?>
						<li>
							<span class="project-image-wrap">
								<img src="<?php echo Route::url($project->link('thumb')); ?>" width="30" height="30" alt="<?php echo htmlentities($this->escape($project->get('title'))); ?>" class="project-image" />
							</span>
							<span class="project-title">
								<?php echo $this->escape($project->get('title')); ?>
							</span>
							<a class="btn btn-success icon-plus" href="<?php echo Route::url('projects/' . $this->escape($project->get('alias')) . '/publications/publication?base=qubesresource'); ?>" />
								<?php echo Lang::txt('COM_PUBLICATIONS_NEW_PUB_ADD'); ?>
							</a>
						</li>
					<?php } ?>
				</ul>
			<?php } else { ?>
				<p><?php echo Lang::txt('COM_PUBLICATIONS_NEW_PUB_NO_PROJECTS'); ?></p>
			<?php } ?>
		</div>
	</div>
</form>

<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<form class="fork-options" method="post" action="<?php echo Route::url($this->publication->link() . '&active=fork&action=fork'); ?>">
	<h3><?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_FORK_PUBLICATION'); ?></h3>
	<div class="grid">
		<div class="col span5 fork-new">
			<h4><?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_FORK_NO_PROJECT'); ?></h4>
			<p><?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_FORK_NO_PROJECT_EXPLANATION'); ?></p>
			<p><a class="btn btn-success" href="<?php echo Route::url('index.php?option=com_publications&task=fork&version=' . $this->publication->version_id); ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_CREATE'); ?></a></p>
		</div>
		<div class="col span7 omega fork-to-project">
			<h4><?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_FORK_EXISTING_PROJECT'); ?></h4>
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
							<a class="btn btn-success icon-plus" href="<?php echo Route::url('index.php?option=com_publications&task=fork&version=' . $this->publication->version_id . '&project=' . $project->get('id')); ?>">
								<?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_ADD'); ?>
							</a>
						</li>
					<?php } ?>
				</ul>
			<?php } else { ?>
				<p><?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_NO_PROJECTS'); ?></p>
			<?php } ?>
		</div>
	</div>
</form>

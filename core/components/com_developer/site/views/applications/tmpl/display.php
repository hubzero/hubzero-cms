<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('applications')
     ->css()
     ->js();
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="btn icon-add" href="<?php echo Route::url('index.php?option=com_developer&controller=applications&task=new'); ?>">
				<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_NEW'); ?>
			</a>
		</p>
	</div>
</header>

<section class="main section full">
	<div class="subject">
		<div class="container">
			<h3><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_MINE'); ?></h3>
			<ul class="entries-list applications your-applications">
				<?php if ($this->applications->count() > 0) : ?>
					<?php foreach ($this->applications as $application) : ?>
						<li>
							<h4>
								<a href="<?php echo Route::url($application->link()); ?>">
									<?php echo $this->escape($application->get('name')) ?>
								</a>
							</h4>
							<dl class="meta">
								<dd><?php echo $application->created('date'); ?></dd>
								<dd><?php echo $application->created('time'); ?></dd>
								<dd><?php echo $application->users(); ?> active users</dd>
							</dl>
							<p><?php echo $this->escape(\Hubzero\Utility\Str::truncate($application->get('description'), 500)); ?></p>
						</li>
					<?php endforeach; ?>
				<?php else : ?>
					<li class="empty">
						<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_MINE_NONE', Route::url('index.php?option=com_developer&controller=applications&task=new')); ?>
					</li>
				<?php endif; ?>
			</ul>
		</div>

		<hr />

		<div class="container" id="authorized">
			<h3><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_AUTHORIZED'); ?></h3>
			<ul class="entries-list applications authorized-applications">
				<?php if ($this->tokens->count() > 0) : ?>
					<?php foreach ($this->tokens as $token) : ?>
						<li>
							<?php $application = $token->application; ?>
							<h4>
								<?php echo $this->escape($application->get('name')) ?>
							</h4>
							<a class="btn btn-secondary revoke confirm" data-txt-confirm="<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_AUTHORIZED_REVOKE_ACCESS_CONFIRM'); ?>" href="<?php echo Route::url($application->link('revoke').'&token=' . $token->get('id')); ?>">
								<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_AUTHORIZED_REVOKE_ACCESS'); ?>
							</a>
							<dl class="meta">
								<dd><?php echo Lang::txt('Authorization Date: %s', $token->created('m/d/Y @ g:ia')); ?></dd>
							</dl>
							<p><?php echo $this->escape(\Hubzero\Utility\Str::truncate($application->get('description'), 500)); ?></p>
						</li>
					<?php endforeach; ?>
				<?php else : ?>
					<li class="empty">
						<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_AUTHORIZED_NONE'); ?>
					</li>
				<?php endif; ?>
			</ul>
			<?php echo $this->tokens->pagination; ?>
		</div>
	</div>
</section>

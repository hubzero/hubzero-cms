<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<div class="section-inner">

		<div class="grid process_steps">
			<div class="col span-third">
				<div class="current">
					<h3><span>1</span> <?php echo Lang::txt('COM_JOBS_STEP_LOGIN') . ' ' . Lang::txt('COM_JOBS_TO') . ' ' . Config::get('sitename'); ?></h3>
				</div>

				<p><?php echo Lang::txt('COM_JOBS_LOGIN_NO_ACCOUNT') . ' <a href="' . Route::url('index.php?option=com_members&controller=register') . '">' . Lang::txt('COM_JOBS_LOGIN_REGISTER_NOW') . '</a>. ' . Lang::txt('COM_JOBS_LOGIN_IT_IS_FREE'); ?></p>
			</div>

			<div class="col span-third">
				<div>
					<h3><span>2</span> <?php echo Lang::txt('COM_JOBS_STEP_SUBSCRIBE'); ?></h3>
				</div>

				<p>
					<?php echo Lang::txt('COM_JOBS_INTRO_TO_ACCESS') . ' '; ?>
					<?php echo Lang::txt('COM_JOBS_EMPLOYER_SERVICES') . ' '; ?>
					<?php echo Lang::txt('COM_JOBS_INTRO_SUBSCRIPTION_REQUIRED') . ' ' . Lang::txt('COM_JOBS_INTRO_HOW_TO_SUBSCRIBE'); ?>
				</p>

			</div>

			<div class="col span-third omega">
				<div>
					<h3><span>3</span> <?php echo ($this->task=='addjob') ? Lang::txt('COM_JOBS_ACTION_POST_AND_BROWSE') : Lang::txt('COM_JOBS_ACTION_BROWSE_AND_POST'); ?></h3>
				</div>

				<p>
					<?php
					echo ($this->task=='addjob')
							? Lang::txt('COM_JOBS_INTRO_POST_UP_TO') . ' ' . $this->config->get('maxads', 3) . ' ' . Lang::txt('COM_JOBS_INTRO_POST_DETAILS')
							: Lang::txt('COM_JOBS_INTRO_BROWSE_INFO') . ' ' . Lang::txt('COM_JOBS_INTRO_BROWSE_DETAILS'); ?>
					<?php
					echo ($this->task=='addjob')
							? '<img src="' . Request::base(true) . '/core/components/' . $this->option . '/site/assets/img/helper_job_search.gif" alt="' . Lang::txt('COM_JOBS_ACTION_POST_JOB') . '" />'
							: '<img src="' . Request::base(true) . '/core/components/' . $this->option . '/site/assets/img/helper_browse_resumes.gif" alt="' . Lang::txt('COM_JOBS_ACTION_BROWSE_RESUMES') . '" />';
					?>
				</p>
			</div>
		</div><!-- / .grid -->

	</div>
</section><!-- / .main section -->
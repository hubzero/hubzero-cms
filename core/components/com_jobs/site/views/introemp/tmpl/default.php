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
<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2)
 */

// No direct access
defined('_HZEXEC_') or die();

	/* Mini-login screen for employers */

	$sitename = Config::get('sitename');
	// get some configs
	$promoline = $this->config->get('promoline') ? $this->config->get('promoline') : '';
	$infolink = $this->config->get('infolink') ? $this->config->get('infolink') : '';
	$maxads = $this->config->get('maxads') ? $this->config->get('maxads') : 3;

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<div class="section-inner">

		<div class="grid process_steps">
			<div class="col span-third">
				<div class="current">
					<h3><span>1</span> <?php echo Lang::txt('COM_JOBS_STEP_LOGIN').' '.Lang::txt('COM_JOBS_TO').' '.$sitename; ?></h3>
				</div>

				<p><?php echo Lang::txt('COM_JOBS_LOGIN_NO_ACCOUNT').' <a href="' . Route::url('index.php?option=com_members&controller=register') . '">'.Lang::txt('COM_JOBS_LOGIN_REGISTER_NOW').'</a>. '.Lang::txt('COM_JOBS_LOGIN_IT_IS_FREE'); ?></p>
			</div>

			<div class="col span-third">
				<div>
					<h3><span>2</span> <?php echo Lang::txt('COM_JOBS_STEP_SUBSCRIBE'); ?></h3>
				</div>

				<p>
					<?php echo Lang::txt('COM_JOBS_INTRO_TO_ACCESS').' '; ?>
					<?php echo Lang::txt('COM_JOBS_EMPLOYER_SERVICES').' '; ?>
					<?php echo Lang::txt('COM_JOBS_INTRO_SUBSCRIPTION_REQUIRED').' '.Lang::txt('COM_JOBS_INTRO_HOW_TO_SUBSCRIBE'); ?>
				</p>
				<?php echo $promoline ? '<p class="promo">'.$promoline.'</p>' : ''; ?>
			</div>

			<div class="col span-third omega">
				<div>
					<h3><span>3</span> <?php echo ($this->task=='addjob') ? Lang::txt('COM_JOBS_ACTION_POST_AND_BROWSE') : Lang::txt('COM_JOBS_ACTION_BROWSE_AND_POST'); ?></h3>
				</div>

				<p>
					<?php
					echo ($this->task=='addjob')
							? Lang::txt('COM_JOBS_INTRO_POST_UP_TO').' '.$maxads.' '.Lang::txt('COM_JOBS_INTRO_POST_DETAILS')
							: Lang::txt('COM_JOBS_INTRO_BROWSE_INFO').' '.Lang::txt('COM_JOBS_INTRO_BROWSE_DETAILS'); ?>
					<?php
					echo ($this->task=='addjob')
							? '<img src="'.Request::base(true).'/core/components/'.$this->option.'/site/assets/img/helper_job_search.gif" alt="'.Lang::txt('COM_JOBS_ACTION_POST_JOB').'" />'
							: '<img src="'.Request::base(true).'/core/components/'.$this->option.'/site/assets/img/helper_browse_resumes.gif" alt="'.Lang::txt('COM_JOBS_ACTION_BROWSE_RESUMES').'" />';
					?>
				</p>
			</div>
		</div><!-- / .grid -->

	</div>
</section><!-- / .main section -->
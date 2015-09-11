<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   GNU General Public License, version 2 (GPLv2)
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
		<?php if (User::isGuest()) { ?>
			<li><?php echo Lang::txt('COM_JOBS_PLEASE') . ' <a href="' . Route::url('index.php?option=' . $this->option . '&task=view') . '?action=login">' . Lang::txt('COM_JOBS_ACTION_LOGIN') . '</a> ' . Lang::txt('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS'); ?></li>
		<?php } else if ($this->emp && $this->config->get('allowsubscriptions', 0)) {  ?>
			<li><a class="icon-dashboard myjobs btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
			<li><a class="icon-list shortlist btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=resumes') . '?filterby=shortlisted'; ?>"><?php echo Lang::txt('COM_JOBS_SHORTLIST'); ?></a></li>
		<?php } else if ($this->admin) { ?>
			<li>
				<!-- <?php echo Lang::txt('COM_JOBS_NOTICE_YOU_ARE_ADMIN'); ?> -->
				<a class="icon-dashboard myjobs btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_ADMIN_DASHBOARD'); ?></a>
			</li>
		<?php } else { ?>
			<li><a class="myresume btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=addresume'); ?>"><?php echo Lang::txt('COM_JOBS_MY_RESUME'); ?></a></li>
		<?php } ?>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<?php if ($this->msg) { ?>
	<p class="help"><?php echo $this->msg; ?></p>
<?php } ?>

<?php if ($this->config->get('allowsubscriptions', 0)) { ?>
<section id="introduction" class="section">
	<div id="introbody">
		<div class="grid">
			<div class="col span4">
				<p class="intronote"><?php echo Lang::txt('COM_JOBS_TIP_ENJOY_COMMUNITY_EXPOSURE', Config::get('sitename')); ?></p>
			</div>
			<div class="col span4">
				<h3><?php echo Lang::txt('COM_JOBS_EMPLOYERS'); ?></h3>
				<ul>
					<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=resumes'); ?>"><?php echo Lang::txt('COM_JOBS_ACTION_BROWSE_RESUMES'); ?></a></li>
					<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=addjob'); ?>"><?php echo Lang::txt('COM_JOBS_ACTION_POST_JOB'); ?></a></li>
				</ul>
			</div>
			<div class="col span4 omega">
				<h3><?php echo Lang::txt('COM_JOBS_SEEKERS'); ?></h3>

				<ul>
					<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo Lang::txt('COM_JOBS_ACTION_BROWSE_JOBS'); ?></a></li>
					<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=addresume'); ?>"><?php echo Lang::txt('COM_JOBS_ACTION_POST_RESUME'); ?></a></li>
				</ul>
			</div>
		</div><!-- / .grid -->
	</div>
</section><!-- / #introduction.section -->
<?php } ?>
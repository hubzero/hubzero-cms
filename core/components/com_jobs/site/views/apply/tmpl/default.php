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

/* Application Form */

// load some classes
$sitename = Config::get('sitename');

$jobsHtml = new \Components\Jobs\Helpers\Html();

$job = $this->job;
$seeker = $this->seeker;
$application = $this->application;
$owner = (User::get('id') == $job->employerid or $this->admin) ? 1 : 0;
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
		<?php if (User::isGuest()) { ?>
			<li><?php echo Lang::txt('COM_JOBS_PLEASE').' <a href="'.Route::url('index.php?option='.$this->option.'&task=view').'?action=login">'.Lang::txt('COM_JOBS_ACTION_LOGIN').'</a> '.Lang::txt('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS'); ?></li>
		<?php } else if ($this->emp && $this->allowsubscriptions) {  ?>
			<li><a class="myjobs btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
			<li><a class="shortlist btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=resumes').'?filterby=shortlisted'; ?>"><?php echo Lang::txt('COM_JOBS_SHORTLIST'); ?></a></li>
		<?php } else if ($this->admin) { ?>
			<li>
				<?php echo Lang::txt('COM_JOBS_NOTICE_YOU_ARE_ADMIN'); ?>
				<a class="myjobs btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_ADMIN_DASHBOARD'); ?></a>
			</li>
		<?php } else { ?>
			<li><a class="alljobs btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse'); ?>"><?php echo Lang::txt('COM_JOBS_ALL_JOBS'); ?></a></li>
		<?php } ?>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<?php if (!$seeker) { ?>
	<p class="warning"><?php echo Lang::txt('COM_JOBS_APPLY_TO_APPLY').' '.$sitename.' '.Lang::txt('COM_JOBS_APPLY_NEED_RESUME') ?></p>
	<p>
		<?php echo '<a href="'. Route::url('index.php?option=com_members&id='.User::get('id').'&active=resume').'" class="add">'.Lang::txt('COM_JOBS_ACTION_CREATE_PROFILE').'</a>'; ?>
	</p>
<?php } else { ?>
	<section class="main section">
		<?php
		$job->title = trim(stripslashes($job->title));
		$appid = $application->status !=2 ? $application->id : 0;
		?>

		<?php if ((!$this->admin && User::get('id') == $job->employerid) or ($this->admin && $job->employerid == 1) ) { ?>
			<p class="warning"><?php echo Lang::txt('COM_JOBS_APPLY_WARNING_OWN_AD'); ?></p>
		<?php } ?>

		<div id="applyinfo">
			<h3>
				<?php echo $job->title; ?> -
				<?php echo preg_match('/(.*)http/i', $job->companyWebsite) ? '<a href="'.$job->companyWebsite.'">'.$job->companyName.'</a>' : $job->companyName; ?>,
				<?php echo $job->companyLocation; ?>,
				<?php echo $job->companyLocationCountry; ?> <span><?php echo Lang::txt('COM_JOBS_JOB_REFERENCE_CODE'); ?>: <?php echo $job->code; ?></span>
			</h3>
		</div>

		<form id="hubForm" method="post" action="<?php echo Route::url('index.php?option='.$this->option); ?>">
			<fieldset>
				<input type="hidden"  name="task" value="saveapp" />
				<input type="hidden" id="code" name="code" value="<?php echo $job->code; ?>" />
				<input type="hidden" id="jid" name="jid" value="<?php echo $job->id; ?>" />
				<input type="hidden" id="appid" name="appid" value="<?php echo $appid; ?>" />
				<input type="hidden" id="uid" name="uid" value="<?php echo User::get('id'); ?>" />
				<h3><?php echo Lang::txt('COM_JOBS_APPLY_MSG_TO_EMPLOYER'); ?> <span class="opt">(<?php echo Lang::txt('COM_JOBS_OPTIONAL'); ?>)</span></h3>
				<label>
					<textarea name="cover" id="cover" rows="10" cols="15"><?php echo $application->cover; ?></textarea>
				</label>
			</fieldset>
			<div class="explaination">
				<p><?php echo Lang::txt('COM_JOBS_APPLY_HINT_COVER_LETTER'); ?></p>
			</div>
			<div class="clear"></div>

			<div class="subject custom">
				<?php
				// profile info
				if ($seeker)
				{
					// show seeker info
					$out = Event::trigger('members.showSeeker', array($seeker, $this->emp, $this->admin, 'com_members', $list=0));
					if (count($out) > 0)
					{
						echo implode("\n", $out);
					}
				}
				?>
			</div>
			<p class="submit">
				<input type="submit" name="submit" value="<?php echo $this->task=='editapp' ? Lang::txt('COM_JOBS_ACTION_SAVE_CHANGES_APPLICATION') : Lang::txt('COM_JOBS_ACTION_APPLY_THIS_JOB'); ?>" />
				<span class="cancelaction">
					<a href="<?php echo Route::url('index.php?option='.$this->option.'&task=job&id='.$job->code); ?>">
						<?php echo Lang::txt('COM_JOBS_CANCEL'); ?>
					</a>
				</span>
			</p>
		</form>
	</section>
<?php } ?>
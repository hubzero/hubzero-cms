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

	/* Job Posting */
	$job        = $this->job;
	$job->cat   = $job->cat ? $job->cat : 'Unspecified';
	$job->type  = $job->type ? $job->type : 'Unspecified';

	$startdate = ($job->startdate && $job->startdate !='0000-00-00 00:00:00') ? Date::of($job->startdate)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : 'Unspecified';
	$closedate = ($job->closedate && $job->closedate !='0000-00-00 00:00:00') ? Date::of($job->closedate)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : 'Unspecified';

	$model = new \Components\Jobs\Models\Job($job);

	$maintext = $model->content('parsed');

	$owner = (User::get('id') == $job->employerid or $this->admin) ? 1 : 0;

?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
	<div id="content-header-extra">
		<ul id="useroptions">
		<?php if (User::isGuest()) { ?>
			<li><?php echo Lang::txt('COM_JOBS_PLEASE') . " " ?><a href="<?php Route::url('index.php?option=' . $this->option . '&task=view') . '?action=login' ?>"><?php echo Lang::txt('COM_JOBS_ACTION_LOGIN') ?></a><?php echo " " . Lang::txt('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS') ?></li>
		<?php } else if ($this->emp && $this->config->get('allowsubscriptions', 0)) {  ?>
			<li><a class="icon-dashboard myjobs btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
			<li><a class="icon-list shortlist btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=resumes') . '?filterby=shortlisted'; ?>"><?php echo Lang::txt('COM_JOBS_SHORTLIST'); ?></a></li>
			<li><a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=addjob'); ?>"><?php echo Lang::txt('COM_JOBS_ADD_ANOTHER_JOB'); ?></a></li>
		<?php } else if ($this->admin) { ?>
			<li><a class="icon-dashboard myjobs btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_ADMIN_DASHBOARD'); ?></a></li>
			<li><a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=addjob'); ?>"><?php echo Lang::txt('COM_JOBS_ADD_ANOTHER_JOB'); ?></a></li>
		<?php } else { ?>
			<li><a class="myresume btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=addresume'); ?>"><?php echo Lang::txt('COM_JOBS_MY_RESUME'); ?></a></li>
		<?php } ?>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<?php
	$job->title = trim(stripslashes($job->title));
	$job->description = trim(stripslashes($job->description));
	$job->description = preg_replace('/<br\\s*?\/??>/i', "", $job->description);
	$job->description = \Components\Jobs\Helpers\Html::txt_unpee($job->description);
?>

<section class="main section">
	<span class="applicationButtons">
	<?php
		if (!$job->applied && !$job->withdrawn && $job->status == 1) { ?>
			<span class="apply"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=apply&code=' . $job->code) ?>"><button class="btn btn-success"><?php echo Lang::txt('COM_JOBS_APPLY_NOW') ?></button></a></span> 
		<?php } if ($job->withdrawn && $job->status == 1) { ?>
			<span class="apply"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=apply&code=' . $job->code) ?>"><button class="btn btn-success"><?php echo Lang::txt('COM_JOBS_ACTION_REAPPLY') ?></button></a></span>
		<?php } if($job->applied) { ?>
			<span class="applied"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=editapp&code=' . $job->code) ?>"><button class="btn btn-success"><?php echo Lang::txt('COM_JOBS_ACTION_EDIT_APPLICATION') ?></button></a><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=withdraw&code=' . $job->code) ?>" id="showconfirm"><button class="btn btn-danger"><?php echo Lang::txt('COM_JOBS_ACTION_WITHDRAW_APPLICATION') ?></button></a>
			</span>
		<?php } if($owner && ($job->status == 1 || $job->status == 3)) {  ?>
			<span class="edit"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=editjob&code=' . $job->code) ?>"><button class="btn btn"><?php echo(' ' . Lang::txt('COM_JOBS_ACTION_EDIT_JOB')) ?></button></a></span> 
		<?php }
                        if ($job->status == 1 && $owner) { ?>
                                <span class="unpublish"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=unpublish&code=' . $job->code) ?>" title="<?php echo Lang::txt('COM_JOBS_NOTICE_ACCESS_PRESERVED') ?>"><button class="btn"><?php echo Lang::txt('COM_JOBS_ACTION_UNPUBLISH_THIS_JOB') ?></button></a></span>
                        <?php }
                        if ($job->status == 3) { ?>
                                <span class="manageroptions"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=reopen&code=' . $job->code) ?>" title="<?php echo Lang::txt('COM_JOBS_ACTION_INCLUDE_INPUBLIC_LISTING') ?>"><button class="btn btn"><?php echo Lang::txt('COM_JOBS_ACTION_REOPEN_THIS') ?></button></a><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=remove&code=' . $job->code) ?>" title="<?php echo Lang::txt('COM_JOBS_ACTION_DELETE_ALL_RECORDS') ?>"><button class="btn btn"><?php echo Lang::txt('COM_JOBS_ACTION_DELETE_THIS_JOB') ?></button></a></span>
                                </p>
                        <?php }
                        if ($job->applied) {
                                 \Components\Jobs\Helpers\Html::confirmscreen(Route::url('index.php?option=' . $this->option . '&task=job&code=' . $job->code), Route::url('index.php?option=' . $this->option . '&task=withdraw&code=' . $job->code), $action = "withdrawapp");
 			} ?>

	</span>
		<div id="jobinfo">
		<h3>
			<span><?php echo Lang::txt('COM_JOBS_JOB_REFERENCE_CODE') . ': ' . $job->code ?></span><?php $job->title . ' - ' ?>
			<?php if (preg_match('/(.*)http/i', $job->companyWebsite)) { ?>
				<a href="<?php echo $job->companyWebsite ?>"><?php echo $job->companyName ?></a>
			<?php } else {
				echo $job->companyName;
			}
			echo (', ' . $job->companyLocation);
			if ($job->companyLocationCountry) {
				echo(', ' . strtoupper($job->companyLocationCountry));
			} else {
				echo  '';
			} ?>
		</h3><?php echo "\n";?>

		<div class="clear"></div><?php echo "\n" ?>
		<div class="apply">
		<p>
			<?php if ($job->applied) { ?>
				<span class="alreadyapplied"><?php echo Lang::txt('COM_JOBS_JOB_APPLIED_ON') . ' ' . Date::of($job->applied)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) ?><span><?php echo "\n";
			} else if ($job->withdrawn) { ?>
				<span class="withdrawn"><?php echo Lang::txt('COM_JOBS_JOB_WITHDREW_ON') . ' ' . Date::of($job->withdrawn)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) ?><span><?php echo "\n";
			} ?>
		</p>
		</div>

		<div>
			<span class="sub-heading"><?php echo Lang::txt('COM_JOBS_TABLE_CATEGORY') ?></span>
				<p><?php echo $job->cat ?></p>
			<span class="sub-heading"><?php echo Lang::txt('COM_JOBS_TABLE_TYPE') ?></span>
				<p><?php echo $job->type ?></p>
			<span class="sub-heading"><?php echo Lang::txt('COM_JOBS_TABLE_START_DATE') ?></span>
				<p><?php echo $startdate ?></p>
			<span class="sub-heading"><?php echo Lang::txt('COM_JOBS_TABLE_EXPIRES') ?></span>
				<p><?php echo $closedate ?></p>
		<div class="reg details"><?php echo $maintext;
		if ($job->contactName) { ?>
			<p class="reg details"><?php echo Lang::txt('COM_JOBS_JOB_INFO_CONTACT') ?>:</p><?php echo "\n" ?>
			<p class="reg"><?php echo "\n"; ?>
			<span class="contactname"><?php echo $job->contactName ?></span><?php "\n";
			if ($job->contactPhone) { ?>
				<span class="contactinfo"><?php echo Lang::txt('COM_JOBS_JOB_TABLE_TEL') . ': ' . $job->contactPhone ?></span><?php echo "\n";
			} else {
				echo '';
			}
			if ($job->contactEmail) { ?>
				<span class="contactinfo"><?php echo Lang::txt('COM_JOBS_JOB_TABLE_EMAIL') . ': ' . $job->contactEmail ?></span><?php echo "\n"; 
			} else {
				echo '';
			} ?>
			</p>
		<?php } ?>
		</div>
		</div>

		<?php if ($owner) {
			if ($job->status == 4) { ?>
				<p class="confirmPublish">
					<span class="makechanges"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=confirmjob&code=' . $job->code) ?>"><button class="btn btn-success"><?php echo Lang::txt('COM_JOBS_ACTION_PUBLISH_AD') ?></button></a></span>
					<span class="makechanges"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=editjob&code=' . $job->code) ?>"><button class="btn"><?php echo Lang::txt('COM_JOBS_ACTION_MAKE_CHANGES') ?></button></a></span>
					<span class="makechanges"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=remove&code=' . $job->code) ?>"><button class="btn"><?php echo Lang::txt('COM_JOBS_ACTION_REMOVE_AD') ?></button></a></span>
				</p>
			<?php } 
		} ?>
	</div>
	<?php if ($owner) { ?>
		<span class="review_applicants"><a href="<?php echo Route::url('index.php?option=com_jobs&task=resumes?filterby=applied'); ?>"><button class="btn btn"><?php echo Lang::txt('COM_JOBS_REVIEW_APPLICANTS'); ?></button></a></span>
		<h3><?php echo Lang::txt('COM_JOBS_APPLICATIONS') . ' (' . count($job->applications) . ' ' . Lang::txt('COM_JOBS_TOTAL') . ')'; ?></h3>
		<?php if (count($job->applications) <= 0 ) { ?>
			<p><?php echo Lang::txt('COM_JOBS_NOTICE_APPLICATIONS_NONE'); ?></p>
		<?php } else { ?>
			<ul id="candidates">
			<?php $k = 1;
			for ($i = 0, $n = count( $job->applications ); $i < $n; $i++) {
				if ($job->applications[$i]->seeker && $job->applications[$i]->status != 2) {
					$applied = ($job->applications[$i]->applied !='0000-00-00 00:00:00') ? Date::of($job->applications[$i]->applied)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : Lang::txt('N/A'); ?>
					<li class="applic">
					<span class="countc"><?php echo $k . ". " ?></span><a href="<?php echo Route::url('members/' . $job->applications[$i]->uid . "/resume"); ?>"><?php echo $job->applications[$i]->seeker->name ?></a><?php echo ' ' . Lang::txt('applied on') . ' ' . $applied;
					if ($job->applications[$i]->cover) { ?>
						<blockquote><?php echo trim(stripslashes($job->applications[$i]->cover)) ?></blockquote> 
					<?php } else {
						echo '';
					} ?>
					</li>
					<li>
					<!-- show seeker info -->
					<?php $out = Event::trigger('members.showSeeker', array($job->applications[$i]->seeker, $this->emp, $this->admin, 'com_members', $list=0));
					if (count($out) > 0) {
						echo $out[0];
					} ?>
					</li>
					<li class="applicbot"></li>
					<?php $k++;
				}
			}
			if (count($job->withdrawnlist) > 0) {
				for ($i=0, $n=count( $job->withdrawnlist ); $i < $n; $i++) {
					$n = $k;
					$n++;
				}
			}?>
			</ul>
			<?php if (count($job->withdrawnlist) > 0) { ?>
				<p><?php echo count($job->withdrawnlist) . ' ' . Lang::txt('COM_JOBS_NOTICE_CANDIDATES_WITHDREW'); ?></p>
			<?php } ?>
		<?php }
	 } ?>
</section>

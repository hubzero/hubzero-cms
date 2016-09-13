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

	/* Post New Job / Edit Job Form */

	$job = $this->job;
	$employer = $this->employer;
	$profile = $this->profile;
	$id = $this->jobid;

	$startdate = ($job->startdate && $job->startdate !='0000-00-00 00:00:00') ? Date::of($job->startdate)->toLocal('Y-m-d') : '';
	$closedate = ($job->closedate && $job->closedate !='0000-00-00 00:00:00') ? Date::of($job->closedate)->toLocal('Y-m-d') : '';
	$defaultExpire = ($this->config->get('expiry', 0) ? Date::of(strtotime('180 days'))->toLocal('Y-m-d') : '');
	$expiredate = ($job->expiredate && $job->expiredate !='0000-00-00 00:00:00') ? Date::of($job->expiredate)->toLocal('Y-m-d') : $defaultExpire;

	$status = $this->task != 'addjob' ? $job->status : 4; // draft mode

	$hubzero_Geo = new \Hubzero\Geocode\Geocode();
	$countries = $hubzero_Geo->countries();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
		<?php if ($this->emp) {  ?>
			<li><a class="icon-dashboard myjobs btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
			<li><a class="icon-list shortlist btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=resumes&filterby=shortlisted'); ?>"><?php echo Lang::txt('COM_JOBS_SHORTLIST'); ?></a></li>
		<?php } else { ?>
			<li>
				<!-- <?php echo Lang::txt('COM_JOBS_NOTICE_YOU_ARE_ADMIN'); ?> -->
				<a class="icon-dashboard myjobs btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_ADMIN_DASHBOARD'); ?></a>
			</li>
		<?php } ?>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<?php
	if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
	<?php }
		$html = '';

		$model = new \Components\Jobs\Models\Job($job);

		$job->title = trim(stripslashes($job->title));
		$job->description = $model->content('raw');
		$job->companyLocation = $id ? $job->companyLocation : $employer->companyLocation;
		$job->companyLocationCountry = $id ? $job->companyLocationCountry : $this->escape($hubzero_Geo->getcountry($profile->get('countryresident')));
		$job->companyName = $id ? $job->companyName : $employer->companyName;
		$job->companyWebsite = $id ? $job->companyWebsite : $employer->companyWebsite;
		$usonly = $this->config->get('usonly', 0);
?>
<section class="main section">
	<form id="hubForm" method="post" action="<?php echo Route::url('index.php?option=' . $this->option); ?>">
		<div class="explaination">
			<p><?php echo Lang::txt('COM_JOBS_EDITJOB_OVERVIEW_INFO'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_JOBS_EDITJOB_JOB_OVERVIEW'); ?></legend>

			<input type="hidden" name="task" value="savejob" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="code" value="<?php echo $job->code; ?>" />
			<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
			<input type="hidden" name="status" value="<?php echo $status; ?>" />
			<input type="hidden" name="employerid" value="<?php echo $this->uid; ?>" />

			<label for="title">
				<?php echo Lang::txt('COM_JOBS_EDITJOB_JOB_TITLE'); ?>: <span class="required"><?php echo Lang::txt('COM_JOBS_REQUIRED'); ?></span>
				<input name="title" maxlength="190" id="title" type="text" value="<?php echo $this->escape($job->title); ?>" />
			</label>

			<label for="companyLocation">
				<?php echo Lang::txt('COM_JOBS_EDITJOB_JOB_LOCATION'); ?>: <span class="required"><?php echo Lang::txt('COM_JOBS_REQUIRED'); ?></span>
				<input name="companyLocation" maxlength="190" id="companyLocation" type="text" value="<?php echo $this->escape(stripslashes($job->companyLocation)); ?>" />
			</label>
		<?php if ($usonly == 0 && !empty($countries)) { ?>
			<label for="companyLocationCountry">
				<?php echo Lang::txt('COM_JOBS_EDITJOB_COUNTRY'); ?>: <span class="required"><?php echo Lang::txt('COM_JOBS_REQUIRED'); ?></span>
				<select name="companyLocationCountry" id="companyLocationCountry">
					<option value=""><?php echo Lang::txt('COM_JOBS_OPTION_SELECT_FROM_LIST'); ?></option>
					<?php
					foreach ($countries as $country)
					{
						$selected = $job->companyLocationCountry ? $job->companyLocationCountry : 'United States';
						?>
						<option value="<?php echo $this->escape($country->name); ?>"<?php if (strtoupper($country->name) == strtoupper($selected)) { echo ' selected="selected"'; } ?>><?php echo $this->escape($country->name); ?></option>
						<?php
					}
					?>
				</select>
			</label>
		<?php } else { ?>
			<p class="warning"><?php echo Lang::txt('COM_JOBS_EDITJOB_US_ONLY'); ?></p>
			<input type="hidden" id="companyLocationCountry" name="companyLocationCountry" value="us" />
		<?php } ?>
			<label>
				<?php echo Lang::txt('COM_JOBS_EMPLOYER_COMPANY_NAME'); ?>: <span class="required"><?php echo Lang::txt('COM_JOBS_REQUIRED'); ?></span>
				<input name="companyName" maxlength="120" id="companyName" type="text" value="<?php echo $this->escape(stripslashes($job->companyName)); ?>" />
			</label>
			<label>
				<?php echo Lang::txt('COM_JOBS_EMPLOYER_COMPANY_WEBSITE'); ?>:
				<input name="companyWebsite" maxlength="190" id="companyWebsite" type="text" value="<?php echo $this->escape(stripslashes($job->companyWebsite)); ?>" />
			</label>
			<p class="hint"><?php echo Lang::txt('COM_JOBS_EDITJOB_HINT_COMPANY'); ?></p>
		</fieldset>

		<div class="explaination">
			<p><?php echo Lang::txt('COM_JOBS_EDITJOB_DESC_INFO'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_JOBS_EDITJOB_JOB_DESCRIPTION'); ?> <span class="required"><?php echo Lang::txt('COM_JOBS_REQUIRED'); ?></span></legend>
			<label>
				&nbsp;
				<?php
				echo $this->editor('description', $this->escape($job->description), 50, 25, 'description');
				?>
			</label>
		</fieldset>

		<div class="explaination">
			<p><?php echo Lang::txt('COM_JOBS_EDITJOB_SPECIFICS_INFO'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_JOBS_EDITJOB_JOB_SPECIFICS'); ?></legend>

			<label>
				<?php echo Lang::txt('COM_JOBS_EDITJOB_CATEGORY'); ?>:
				<?php echo \Components\Jobs\Helpers\Html::formSelect('cid', $this->cats, $job->cid, '', ''); ?>
			</label>
			<label>
				<?php echo Lang::txt('COM_JOBS_EDITJOB_TYPE'); ?>:
				<?php echo \Components\Jobs\Helpers\Html::formSelect('type', $this->types, $job->type, '', ''); ?>
			</label>
			<div class="grid">
				<div class="col span6">
					<label for="startdate">
						<?php echo Lang::txt('COM_JOBS_EDITJOB_START_DATE'); ?>:
						<input type="text" name="startdate" id="startdate" size="10" maxlength="10" value="<?php echo $startdate; ?>" />
						<span class="hint"><?php echo Lang::txt('COM_JOBS_EDITJOB_HINT_DATE_FORMAT'); ?></span>
					</label>
				</div>
				<div class="col span6 omega">
					<label for="closedate">
						<?php echo Lang::txt('COM_JOBS_EDITJOB_CLOSE_DATE'); ?>:
						<input  type="text" name="closedate" id="closedate" size="10" maxlength="10" value="<?php echo $closedate; ?>" />
						<span class="hint"><?php echo Lang::txt('COM_JOBS_EDITJOB_HINT_DATE_FORMAT'); ?></span>
					</label>
				</div>
			</div>
			<div class="grid">
				<div class="col span6">
					<label for="expiredate">
						<?php echo Lang::txt('COM_JOBS_EDITJOB_EXPIRE_DATE'); ?>:
						<input  type="text" name="expiredate" id="expiredate" size="10" maxlength="10" value="<?php echo $expiredate; ?>" />
						<?php if ($this->config->get('expiry', 0)): ?>
						<span class="hint"><?php echo Lang::txt('COM_JOBS_EDITJOB_HINT_DATE_FORMAT_EXPIRY'); ?></span>
						<?php else: ?>
						<span class="hint"><?php echo Lang::txt('COM_JOBS_EDITJOB_HINT_DATE_FORMAT'); ?></span>
						<?php endif; ?>
					</label>
				</div> <!-- /.col .span6 -->
				<div class="col span6 omega">	
					<label for="applyExternalUrl">
						<?php echo Lang::txt('COM_JOBS_EDITJOB_EXTERNAL_URL'); ?>:
						<input  type="text" name="applyExternalUrl" id="applyExternalUrl" size="100" maxlength="250" value="<?php echo $this->escape(stripslashes($job->applyExternalUrl)); ?>" />
					</label>
					<label for="applyInternal">
						<input type="checkbox" class="option" name="applyInternal" id="applyInternal" value="1"<?php echo $job->applyInternal ? ' checked="checked" ' : ''; ?> />
						<?php echo Lang::txt('COM_JOBS_EDITJOB_ALLOW_INTERNAL_APPLICATION'); ?>
					</label>
				</div> <!-- /.col .span6 .omega -->
		</fieldset>

		<div class="explaination">
			<p><?php echo Lang::txt('COM_JOBS_EDITJOB_CONTACT_DETAILS'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_JOBS_EDITJOB_CONTACT_INFO'); ?> <span>(<?php echo Lang::txt('COM_JOBS_OPTIONAL'); ?>)</span></legend>

			<label for="contactName">
				<?php echo Lang::txt('COM_JOBS_EDITJOB_CONTACT_NAME'); ?>:
				<input name="contactName" id="contactName" maxlength="100"  type="text" value="<?php echo $job->contactName ? $this->escape(stripslashes($job->contactName)) : $this->escape(stripslashes($profile->get('name'))); ?>" />
			</label>
			<label for="contactEmail">
				<?php echo Lang::txt('COM_JOBS_EDITJOB_CONTACT_EMAIL'); ?>:
				<input name="contactEmail" id="contactEmail" maxlength="100"  type="text" value="<?php echo $job->contactEmail ? $this->escape(stripslashes($job->contactEmail)) : $this->escape(stripslashes($profile->get('email'))); ?>" />
			</label>
			<label for="contactPhone">
				<?php echo Lang::txt('COM_JOBS_EDITJOB_CONTACT_PHONE'); ?>:
				<input name="contactPhone" id="contactPhone" maxlength="100"  type="text" value="<?php echo $job->contactPhone ? $this->escape(stripslashes($job->contactPhone)) : $this->escape(stripslashes($profile->get('phone'))); ?>" />
			</label>
		</fieldset>
		<p class="submit">
			<input type="submit" class="btn btn-success" name="submit" value="<?php echo ($this->task=='addjob' or $job->status == 4) ? Lang::txt('COM_JOBS_ACTION_SAVE_PREVIEW') : Lang::txt('COM_JOBS_ACTION_SAVE'); ?>" />

			<a class="btn btn-secondary" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_CANCEL'); ?></a>
		</p>
	</form>
</section>

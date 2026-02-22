<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

    /* Post New Job / Edit Job Form */

    $job = $this->job;
    $employer = $this->employer;
    $profile = $this->profile;
    $id = $this->jobid;

    $startdate = ($job->startdate && $job->startdate != '0000-00-00 00:00:00')
        ? Date::of($job->startdate)->toLocal('Y-m-d 00:00:00')
        : '';
    $closedate = ($job->closedate && $job->closedate != '0000-00-00 00:00:00')
        ? Date::of($job->closedate)->toLocal('Y-m-d 00:00:00')
        : '';
    $defaultExpire = ($this->config->get('expiry', 0)
        ? Date::of(strtotime('180 days'))->toLocal('Y-m-d 00:00:00')
        : '');
    $expiredate = ($job->expiredate && $job->expiredate != '0000-00-00 00:00:00')
        ? Date::of($job->expiredate)->toLocal('Y-m-d 00:00:00')
        : $defaultExpire;

    $status = $this->task != 'addjob' ? $job->status : 4; // draft mode

    $hubzero_Geo = new \Hubzero\Geocode\Geocode();
    $countries = $hubzero_Geo->countries();

    $dashboardUrl = Route::url(
        'index.php?option=' . $this->option . '&task=dashboard'
    );
    $shortlistUrl = Route::url(
        'index.php?option=' . $this->option
        . '&task=resumes&filterby=shortlisted'
    );
    ?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <div id="content-header-extra">
        <ul id="useroptions">
        <?php if ($this->emp) { ?>
            <li>
                <a class="icon-dashboard myjobs btn" href="<?php echo $dashboardUrl; ?>">
                    <?php echo Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD'); ?>
                </a>
            </li>
            <li>
                <a class="icon-list shortlist btn" href="<?php echo $shortlistUrl; ?>">
                    <?php echo Lang::txt('COM_JOBS_SHORTLIST'); ?>
                </a>
            </li>
        <?php } else { ?>
            <li>
                <!-- <?php echo Lang::txt('COM_JOBS_NOTICE_YOU_ARE_ADMIN'); ?> -->
                <a class="icon-dashboard myjobs btn" href="<?php echo $dashboardUrl; ?>">
                    <?php echo Lang::txt('COM_JOBS_ADMIN_DASHBOARD'); ?>
                </a>
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
        $job->companyLocationCountry = $id
            ? $job->companyLocationCountry
            : $this->escape($hubzero_Geo->getcountry($profile->get('countryresident')));
        $job->companyName = $id ? $job->companyName : $employer->companyName;
        $job->companyWebsite = $id ? $job->companyWebsite : $employer->companyWebsite;
        $usonly = $this->config->get('usonly', 0);
        $requiredTxt = Lang::txt('COM_JOBS_REQUIRED');
?>
<section class="main section">
    <?php $formUrl = Route::url('index.php?option=' . $this->option); ?>
    <form id="hubForm" method="post" action="<?php echo $formUrl; ?>">
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
                <?php echo Lang::txt('COM_JOBS_EDITJOB_JOB_TITLE'); ?>:
                <span class="required"><?php echo $requiredTxt; ?></span>
                <input name="title" maxlength="190" id="title" type="text"
                    value="<?php echo $this->escape($job->title); ?>" />
            </label>

            <label for="companyLocation">
                <?php echo Lang::txt('COM_JOBS_EDITJOB_JOB_LOCATION'); ?>:
                <span class="required"><?php echo $requiredTxt; ?></span>
                <?php $locVal = $this->escape(stripslashes($job->companyLocation)); ?>
                <input name="companyLocation" maxlength="190"
                    id="companyLocation" type="text"
                    value="<?php echo $locVal; ?>" />
            </label>
        <?php if ($usonly == 0 && !empty($countries)) { ?>
            <label for="companyLocationCountry">
                <?php echo Lang::txt('COM_JOBS_EDITJOB_COUNTRY'); ?>:
                <span class="required"><?php echo $requiredTxt; ?></span>
                <select name="companyLocationCountry" id="companyLocationCountry">
                    <option value=""><?php echo Lang::txt('COM_JOBS_OPTION_SELECT_FROM_LIST'); ?></option>
                    <?php
                    foreach ($countries as $country) {
                        $selected = $job->companyLocationCountry
                            ? $job->companyLocationCountry
                            : 'United States';
                        ?>
                        <option value="<?php echo $this->escape($country->name); ?>"
                            <?php if (strtoupper($country->name) == strtoupper($selected)) {
                                echo ' selected="selected"';
                            } ?>><?php echo $this->escape($country->name); ?></option>
                        <?php
                    }
                    ?>
                </select>
            </label>
        <?php } else { ?>
            <p class="warning"><?php echo Lang::txt('COM_JOBS_EDITJOB_US_ONLY'); ?></p>
            <input type="hidden" id="companyLocationCountry"
                name="companyLocationCountry" value="us" />
        <?php } ?>
            <label>
                <?php echo Lang::txt('COM_JOBS_EMPLOYER_COMPANY_NAME'); ?>:
                <span class="required"><?php echo $requiredTxt; ?></span>
                <?php $nameVal = $this->escape(stripslashes($job->companyName)); ?>
                <input name="companyName" maxlength="120" id="companyName"
                    type="text" value="<?php echo $nameVal; ?>" />
            </label>
            <label>
                <?php echo Lang::txt('COM_JOBS_EMPLOYER_COMPANY_WEBSITE'); ?>:
                <?php $webVal = $this->escape(stripslashes($job->companyWebsite)); ?>
                <input name="companyWebsite" maxlength="190" id="companyWebsite"
                    type="text" value="<?php echo $webVal; ?>" />
            </label>
            <p class="hint"><?php echo Lang::txt('COM_JOBS_EDITJOB_HINT_COMPANY'); ?></p>
        </fieldset>

        <div class="explaination">
            <p><?php echo Lang::txt('COM_JOBS_EDITJOB_DESC_INFO'); ?></p>
        </div>
        <fieldset>
            <legend>
                <?php echo Lang::txt('COM_JOBS_EDITJOB_JOB_DESCRIPTION'); ?>
                <span class="required"><?php echo $requiredTxt; ?></span>
            </legend>
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
                        <input type="text" name="startdate" id="startdate"
                            size="10" maxlength="10" value="<?php echo $startdate; ?>" />
                        <span class="hint"><?php echo Lang::txt('COM_JOBS_EDITJOB_HINT_DATE_FORMAT'); ?></span>
                    </label>
                </div>
                <div class="col span6 omega">
                    <label for="closedate">
                        <?php echo Lang::txt('COM_JOBS_EDITJOB_CLOSE_DATE'); ?>:
                        <input type="text" name="closedate" id="closedate"
                            size="10" maxlength="10" value="<?php echo $closedate; ?>" />
                        <span class="hint"><?php echo Lang::txt('COM_JOBS_EDITJOB_HINT_DATE_FORMAT'); ?></span>
                    </label>
                </div>
            </div>
            <div class="grid">
                <div class="col span6">
                    <label for="expiredate">
                        <?php echo Lang::txt('COM_JOBS_EDITJOB_EXPIRE_DATE'); ?>:
                        <span class="required"><?php echo $requiredTxt; ?></span>
                        <input type="text" name="expiredate" id="expiredate"
                            size="10" maxlength="10" value="<?php echo $expiredate; ?>" />
                        <?php if ($this->config->get('expiry', 0)) : ?>
                            <span class="hint">
                                <?php echo Lang::txt('COM_JOBS_EDITJOB_HINT_DATE_FORMAT_EXPIRY'); ?>
                            </span>
                        <?php else : ?>
                            <span class="hint">
                                <?php echo Lang::txt('COM_JOBS_EDITJOB_HINT_DATE_FORMAT'); ?>
                            </span>
                        <?php endif; ?>
                        <span class="hint"><?php echo Lang::txt('COM_JOBS_EDITJOB_HINT_MAX_DATE'); ?></span>
                    </label>
                </div> <!-- /.col .span6 -->
                <div class="col span6 omega">
                    <label for="applyExternalUrl">
                        <?php echo Lang::txt('COM_JOBS_EDITJOB_EXTERNAL_URL'); ?>:
                        <?php $extUrlVal = $this->escape(stripslashes($job->applyExternalUrl)); ?>
                        <input type="text" name="applyExternalUrl"
                            id="applyExternalUrl" size="100" maxlength="250"
                            value="<?php echo $extUrlVal; ?>" />
                    </label>
                    <label for="applyInternal">
                        <?php $checkedAttr = $job->applyInternal ? ' checked="checked" ' : ''; ?>
                        <input type="checkbox" class="option"
                            name="applyInternal" id="applyInternal"
                            value="1"<?php echo $checkedAttr; ?> />
                        <?php echo Lang::txt('COM_JOBS_EDITJOB_ALLOW_INTERNAL_APPLICATION'); ?>
                    </label>
                </div> <!-- /.col .span6 .omega -->
        </fieldset>

        <div class="explaination">
            <p><?php echo Lang::txt('COM_JOBS_EDITJOB_CONTACT_DETAILS'); ?></p>
        </div>
        <fieldset>
            <legend>
                <?php echo Lang::txt('COM_JOBS_EDITJOB_CONTACT_INFO'); ?>
                <span>(<?php echo Lang::txt('COM_JOBS_OPTIONAL'); ?>)</span>
            </legend>

            <label for="contactName">
                <?php echo Lang::txt('COM_JOBS_EDITJOB_CONTACT_NAME'); ?>:
                <?php
                $contactNameVal = $job->contactName
                    ? $this->escape(stripslashes($job->contactName))
                    : $this->escape(stripslashes($profile->get('name')));
                ?>
                <input name="contactName" id="contactName" maxlength="100"
                    type="text" value="<?php echo $contactNameVal; ?>" />
            </label>
            <label for="contactEmail">
                <?php echo Lang::txt('COM_JOBS_EDITJOB_CONTACT_EMAIL'); ?>:
                <?php
                $contactEmailVal = $job->contactEmail
                    ? $this->escape(stripslashes($job->contactEmail))
                    : $this->escape(stripslashes($profile->get('email')));
                ?>
                <input name="contactEmail" id="contactEmail" maxlength="100"
                    type="text" value="<?php echo $contactEmailVal; ?>" />
            </label>
            <label for="contactPhone">
                <?php echo Lang::txt('COM_JOBS_EDITJOB_CONTACT_PHONE'); ?>:
                <?php
                $contactPhoneVal = $job->contactPhone
                    ? $this->escape(stripslashes($job->contactPhone))
                    : $this->escape(stripslashes($profile->get('phone')));
                ?>
                <input name="contactPhone" id="contactPhone" maxlength="100"
                    type="text" value="<?php echo $contactPhoneVal; ?>" />
            </label>
        </fieldset>
        <p class="submit">
            <?php
            $submitLabel = ($this->task == 'addjob' or $job->status == 4)
                ? Lang::txt('COM_JOBS_ACTION_SAVE_PREVIEW')
                : Lang::txt('COM_JOBS_ACTION_SAVE');
            ?>
            <input type="submit" class="btn btn-success" name="submit"
                value="<?php echo $submitLabel; ?>" />

            <a class="btn" href="<?php echo $dashboardUrl; ?>">
                <?php echo Lang::txt('JCANCEL'); ?>
            </a>
        </p>
    </form>
</section>

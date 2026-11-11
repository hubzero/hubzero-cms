<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Event;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{
 *     admin: mixed,
 *     config: mixed,
 *     emp: mixed,
 *     job: mixed,
 *     jobcat: mixed,
 *     jobtype: mixed,
 *     title: mixed,
 * } $this
 */

    /* Job Posting */
    $job        = $this->job;
    $jobcat     = !empty($this->jobcat) ? $this->jobcat : 'Unspecified';
    $jobtype    = !empty($this->jobtype) ? $this->jobtype : 'Unspecified';

    $startdate = ($job->startdate && $job->startdate != '0000-00-00 00:00:00')
        ? Date::of($job->startdate)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
        : 'Unspecified';
    $closedate = ($job->closedate && $job->closedate != '0000-00-00 00:00:00')
        ? Date::of($job->closedate)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
        : 'Unspecified';

    $model = new \Components\Jobs\Models\Job($job);

    $maintext = $model->content('parsed');

    $owner = (User::get('id') == $job->employerid or $this->admin) ? 1 : 0;

?>

<header id="content-header">
    <h2><?php echo $this->title; ?></h2>
    <div id="content-header-extra">
        <ul id="useroptions">
        <?php if (User::isGuest()) { ?>
            <?php
            $loginUrl = Route::url('index.php?option=' . $this->option . '&task=view') . '?action=login';
            $viewOptionsText = " " . Lang::txt('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS');
            ?>
            <li><?php echo Lang::txt('COM_JOBS_PLEASE') . " "; ?><a
                href="<?= $loginUrl ?>"><?= Lang::txt('COM_JOBS_ACTION_LOGIN') ?></a><?= $viewOptionsText ?></li>
        <?php } elseif ($this->emp && $this->config->get('allowsubscriptions', 0)) {  ?>
            <?php
            $linkUrl = Route::url('index.php?option=' . $this->option . '&task=dashboard');
            ?>
            <li><a class="icon-dashboard myjobs btn"
                href="<?= $linkUrl ?>"><?= Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD') ?></a></li>
            <?php
            $resumesUrl = Route::url('index.php?option=' . $this->option . '&task=resumes') . '?filterby=shortlisted';
            ?>
            <li><a class="icon-list shortlist btn"
                href="<?= $resumesUrl ?>"><?= Lang::txt('COM_JOBS_SHORTLIST') ?></a></li>
            <?php
            $addjobUrl = Route::url('index.php?option=' . $this->option . '&task=addjob');
            ?>
            <li><a class="icon-add add btn"
                href="<?= $addjobUrl ?>"><?= Lang::txt('COM_JOBS_ADD_ANOTHER_JOB') ?></a></li>
        <?php } elseif ($this->admin) {
            $dashboardUrl = Route::url('index.php?option=' . $this->option . '&task=dashboard');
            ?>
            <li><a class="icon-dashboard myjobs btn"
                href="<?= $dashboardUrl ?>"><?= Lang::txt('COM_JOBS_ADMIN_DASHBOARD') ?></a></li>
            <?php
            $addjobUrl = Route::url('index.php?option=' . $this->option . '&task=addjob');
            ?>
            <li><a class="icon-add add btn"
                href="<?= $addjobUrl ?>"><?= Lang::txt('COM_JOBS_ADD_ANOTHER_JOB') ?></a></li>
        <?php } else {
            $addresumeUrl = Route::url('index.php?option=' . $this->option . '&task=addresume');
            ?>
            <li><a class="myresume btn" href="<?= $addresumeUrl ?>"><?= Lang::txt('COM_JOBS_MY_RESUME') ?></a></li>
        <?php } ?>
        </ul>
    </div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<?php
    $job->title = trim(stripslashes($job->title));
    $job->description = trim(stripslashes($job->description));
    $job->description = preg_replace('/<br\\s*?\/??>/i', "", $job->description);
    $job->description = \Components\Jobs\Helpers\Html::txtUnpee($job->description);
?>

<section class="main section">
    <span class="applicationButtons">
        <?php if (!$job->applied && !$job->withdrawn && $job->status == 1) {
            $applyUrl = Route::url('index.php?option=' . $this->option . '&task=apply&code=' . $job->code);
            ?>
            <span class="apply"><a href="<?= $applyUrl ?>"><button
                class="btn btn-success"><?php echo Lang::txt('COM_JOBS_APPLY_NOW'); ?></button></a></span> 
        <?php } ?>
        <?php if ($job->withdrawn && $job->status == 1) {
            $applyUrl = Route::url('index.php?option=' . $this->option . '&task=apply&code=' . $job->code);
            ?>
            <span class="apply"><a href="<?= $applyUrl ?>"><button
                class="btn btn-success"><?php echo Lang::txt('COM_JOBS_ACTION_REAPPLY'); ?></button></a></span>
        <?php } ?>
        <?php if ($job->applied) {
            $withdrawUrl = Route::url('index.php?option=' . $this->option . '&task=withdraw&code=' . $job->code);
            $editappUrl = Route::url('index.php?option=' . $this->option . '&task=editapp&code=' . $job->code);
            ?>
            <span class="applied"><a href="<?= $editappUrl ?>"><button
                class="btn btn-success"><?php echo Lang::txt('COM_JOBS_ACTION_EDIT_APPLICATION'); ?></button></a><button
                type="submit" form="hz-post-action" formaction="<?= $withdrawUrl ?>" id="showconfirm"
                class="btn btn-danger"><?php echo Lang::txt('COM_JOBS_ACTION_WITHDRAW_APPLICATION'); ?></button>
            </span>
        <?php } ?>
        <?php if ($owner && ($job->status == 1 || $job->status == 3)) {
            $editjobUrl = Route::url('index.php?option=' . $this->option . '&task=editjob&code=' . $job->code);
            ?>
            <span class="edit"><a href="<?= $editjobUrl ?>"><button
                class="btn btn"><?= ' ' . Lang::txt('COM_JOBS_ACTION_EDIT_JOB') ?></button></a></span> 
        <?php } ?>
        <?php if ($job->status == 1 && $owner) {
            $unpublishUrl = Route::url('index.php?option=' . $this->option . '&task=unpublish&code=' . $job->code);
            $titleText = Lang::txt('COM_JOBS_NOTICE_ACCESS_PRESERVED');
            ?>
             <span class="unpublish"><button type="submit" form="hz-post-action" formaction="<?= $unpublishUrl ?>"
                 class="btn"
                 title="<?= $titleText ?>"><?= Lang::txt('COM_JOBS_ACTION_UNPUBLISH_THIS_JOB') ?></button></span>
        <?php } ?>
        <?php if ($job->status == 3) {
            $reopenUrl = Route::url('index.php?option=' . $this->option . '&task=reopen&code=' . $job->code);
            $removeUrl = Route::url('index.php?option=' . $this->option . '&task=remove&code=' . $job->code);
            ?>
            <?php
            $titleText = Lang::txt('COM_JOBS_ACTION_INCLUDE_INPUBLIC_LISTING');
            $buttonTitleText = Lang::txt('COM_JOBS_ACTION_DELETE_ALL_RECORDS');
            $thisJobText = Lang::txt('COM_JOBS_ACTION_DELETE_THIS_JOB');
            ?>
            <span class="manageroptions"><button type="submit" form="hz-post-action" formaction="<?= $reopenUrl ?>"
                class="btn"
                title="<?= $titleText ?>"><?php echo Lang::txt('COM_JOBS_ACTION_REOPEN_THIS'); ?></button><button
                type="submit" form="hz-post-action" formaction="<?= $removeUrl ?>" class="btn"
                title="<?= $buttonTitleText ?>"><?= $thisJobText ?></button></span>
        <?php } ?>
        <?php
        if ($job->applied) {
            \Components\Jobs\Helpers\Html::confirmscreen(
                Route::url('index.php?option=' . $this->option . '&task=job&code=' . $job->code),
                Route::url('index.php?option=' . $this->option . '&task=withdraw&code=' . $job->code),
                $action = "withdrawapp",
            );
        }
        ?>
    </span>

    <div id="jobinfo">
        <h3>
            <?php
            $spanText = Lang::txt('COM_JOBS_JOB_REFERENCE_CODE') . ': ' . $job->code;
            ?>
            <span><?= $spanText ?></span><?php $job->title . ' - '; ?>
            <?php if (preg_match('#^https?://#i', (string) $job->companyWebsite)) { ?>
                <?php
                $linkUrl = $this->escape($job->companyWebsite);
                ?>
                <a href="<?= $linkUrl ?>"><?php echo $this->escape($job->companyName); ?></a>
            <?php } else {
                echo $this->escape($job->companyName);
            }
            echo ', ' . $this->escape($job->companyLocation);
            if ($job->companyLocationCountry) {
                echo ', ' . $this->escape(strtoupper($job->companyLocationCountry));
            } else {
                echo '';
            } ?>
        </h3><?php echo "\n"; ?>

        <div class="clear"></div><?php echo "\n"; ?>
        <div class="apply">
        <p>
            <?php if ($job->applied) { ?>
                <?php
                $alreadyappliedText = Lang::txt('COM_JOBS_JOB_APPLIED_ON') . ' '
                    . Date::of($job->applied)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                ?>
                <span class="alreadyapplied"><?= $alreadyappliedText ?><span><?php echo "\n";
            } elseif ($job->withdrawn) { ?>
                <?php
                $withdrawnText = Lang::txt('COM_JOBS_JOB_WITHDREW_ON') . ' '
                    . Date::of($job->withdrawn)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                ?>
                <span class="withdrawn"><?= $withdrawnText ?><span><?php echo "\n";
            } ?>
        </p>
        </div>

        <div>
            <span class="sub-heading"><?php echo Lang::txt('COM_JOBS_TABLE_CATEGORY'); ?></span>
                <p><?php echo $this->escape($jobcat); ?></p>
            <span class="sub-heading"><?php echo Lang::txt('COM_JOBS_TABLE_TYPE'); ?></span>
                <p><?php echo $this->escape($jobtype); ?></p>
            <span class="sub-heading"><?php echo Lang::txt('COM_JOBS_TABLE_START_DATE'); ?></span>
                <p><?php echo $startdate; ?></p>
            <span class="sub-heading"><?php echo Lang::txt('COM_JOBS_TABLE_EXPIRES'); ?></span>
                <p><?php echo $closedate; ?></p>
        <div class="reg details"><?php echo $maintext;
        if ($job->contactName) { ?>
            <p class="reg details"><?php echo Lang::txt('COM_JOBS_JOB_INFO_CONTACT'); ?>:</p><?php echo "\n"; ?>
            <p class="reg"><?php echo "\n"; ?>
            <span class="contactname"><?php echo $this->escape($job->contactName); ?></span><?php echo "\n";
            if ($job->contactPhone) { ?>
                <?php
                $contactinfoText = Lang::txt('COM_JOBS_JOB_TABLE_TEL') . ': ' . $this->escape($job->contactPhone);
                ?>
                <span class="contactinfo"><?= $contactinfoText ?></span><?php echo "\n";
            } else {
                echo '';
            }
            if ($job->contactEmail) { ?>
                <?php
                $contactinfoText = Lang::txt('COM_JOBS_JOB_TABLE_EMAIL') . ': ' . $this->escape($job->contactEmail);
                ?>
                <span class="contactinfo"><?= $contactinfoText ?></span><?php echo "\n";
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
                    <?php
                    $confirmjobUrl = Route::url(
                        'index.php?option=' . $this->option . '&task=confirmjob&code=' . $job->code,
                    );
                    ?>
                    <span class="makechanges"><button type="submit" form="hz-post-action"
                        formaction="<?= $confirmjobUrl ?>"
                        class="btn btn-success"><?php echo Lang::txt('COM_JOBS_ACTION_PUBLISH_AD'); ?></button></span>
                    <?php
                    $linkUrl = Route::url('index.php?option=' . $this->option . '&task=editjob&code=' . $job->code);
                    ?>
                    <span class="makechanges"><a href="<?= $linkUrl ?>"><button
                        class="btn"><?php echo Lang::txt('COM_JOBS_ACTION_MAKE_CHANGES'); ?></button></a></span>
                    <?php
                    $actionUrl = Route::url('index.php?option=' . $this->option . '&task=remove&code=' . $job->code);
                    ?>
                    <span class="makechanges"><button type="submit" form="hz-post-action" formaction="<?= $actionUrl ?>"
                        class="btn"><?php echo Lang::txt('COM_JOBS_ACTION_REMOVE_AD'); ?></button></span>
                </p>
            <?php }
        } ?>
    </div>
    <?php if ($owner) { ?>
        <span class="review_applicants"><a
            href="<?= Route::url('index.php?option=com_jobs&task=resumes?filterby=applied') ?>"><button
            class="btn btn"><?= Lang::txt('COM_JOBS_REVIEW_APPLICANTS') ?></button></a></span>
        <h3>
            <?= Lang::txt('COM_JOBS_APPLICATIONS') . ' (' . count($job->applications) . ' '
                . Lang::txt('COM_JOBS_TOTAL') . ')' ?>
        </h3>
        <?php if (count($job->applications) <= 0) { ?>
            <p><?php echo Lang::txt('COM_JOBS_NOTICE_APPLICATIONS_NONE'); ?></p>
        <?php } else { ?>
            <ul id="candidates">
            <?php $k = 1;
            for ($i = 0, $n = count($job->applications); $i < $n; $i++) {
                if ($job->applications[$i]->seeker && $job->applications[$i]->status != 2) {
                    $applied = (
                        $job->applications[$i]->applied && $job->applications[$i]->applied != '0000-00-00 00:00:00'
                    )
                        ? Date::of($job->applications[$i]->applied)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
                        : Lang::txt('N/A');
                    $resumeUrl = Route::url('members/' . $job->applications[$i]->uid . "/resume");
                    $seekerName = $this->escape($job->applications[$i]->seeker->name);
                    $appliedOn = ' ' . Lang::txt('applied on') . ' ' . $applied;
                    $countText = $k . ". "; ?>
                    <li class="applic">
                    <span class="countc"><?= $countText ?></span><a href="<?= $resumeUrl ?>"><?= $seekerName ?></a><?php
                    echo $appliedOn;
                    if ($job->applications[$i]->cover) { ?>
                        <?php
                        $coverText = nl2br($this->escape(trim(stripslashes($job->applications[$i]->cover))));
                        ?>
                        <blockquote><?= $coverText ?></blockquote> 
                    <?php } else {
                        echo '';
                    } ?>
                    </li>
                    <li>
                    <!-- show seeker info -->
                    <?php
                    $out = Event::trigger(
                        'members.showSeeker',
                        [$job->applications[$i]->seeker, $this->emp, $this->admin, 'com_members', $list = 0],
                    );
                    if (count($out) > 0) {
                        echo $out[0];
                    } ?>
                    </li>
                    <li class="applicbot"></li>
                    <?php $k++;
                }
            }
            if (count($job->withdrawnlist) > 0) {
                for ($i = 0, $n = count($job->withdrawnlist); $i < $n; $i++) {
                    $n = $k;
                    $n++;
                }
            } ?>
            </ul>
            <?php if (count($job->withdrawnlist) > 0) { ?>
                <p><?= count($job->withdrawnlist) . ' ' . Lang::txt('COM_JOBS_NOTICE_CANDIDATES_WITHDREW') ?></p>
            <?php } ?>
        <?php }
    } ?>
</section>

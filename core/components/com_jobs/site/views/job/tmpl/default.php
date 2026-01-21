<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

    /* Job Posting */
    $job        = $this->job;
    $job->cat   = $job->cat ? $job->cat : 'Unspecified';
    $job->type  = $job->type ? $job->type : 'Unspecified';

    $startdate = ($job->startdate && $job->startdate != '0000-00-00 00:00:00')
        ? Date::of($job->startdate)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
        : 'Unspecified';
    $closedate = ($job->closedate && $job->closedate != '0000-00-00 00:00:00')
        ? Date::of($job->closedate)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
        : 'Unspecified';

    $model = new \Components\Jobs\Models\Job($job);

    $maintext = $model->content('parsed');

    $owner = (User::get('id') == $job->employerid or $this->admin) ? 1 : 0;

    $dashboardUrl = Route::url(
        'index.php?option=' . $this->option . '&task=dashboard'
    );
    $shortlistUrl = Route::url(
        'index.php?option=' . $this->option . '&task=resumes'
    ) . '?filterby=shortlisted';
    $addjobUrl = Route::url(
        'index.php?option=' . $this->option . '&task=addjob'
    );
    $resumeUrl = Route::url(
        'index.php?option=' . $this->option . '&task=addresume'
    );
    $applyUrl = Route::url(
        'index.php?option=' . $this->option . '&task=apply&code=' . $job->code
    );
    $editappUrl = Route::url(
        'index.php?option=' . $this->option . '&task=editapp&code=' . $job->code
    );
    $withdrawUrl = Route::url(
        'index.php?option=' . $this->option . '&task=withdraw&code=' . $job->code
    );
    $editjobUrl = Route::url(
        'index.php?option=' . $this->option . '&task=editjob&code=' . $job->code
    );
    $unpublishUrl = Route::url(
        'index.php?option=' . $this->option . '&task=unpublish&code=' . $job->code
    );
    $reopenUrl = Route::url(
        'index.php?option=' . $this->option . '&task=reopen&code=' . $job->code
    );
    $removeUrl = Route::url(
        'index.php?option=' . $this->option . '&task=remove&code=' . $job->code
    );
    $confirmUrl = Route::url(
        'index.php?option=' . $this->option . '&task=confirmjob&code=' . $job->code
    );
    $jobViewUrl = Route::url(
        'index.php?option=' . $this->option . '&task=job&code=' . $job->code
    );
    ?>

<header id="content-header">
    <h2><?php echo $this->title; ?></h2>
    <div id="content-header-extra">
        <ul id="useroptions">
        <?php if (User::isGuest()) { ?>
            <li>
                <?php
                $loginUrl = Route::url(
                    'index.php?option=' . $this->option . '&task=view'
                ) . '?action=login';
                echo Lang::txt('COM_JOBS_PLEASE') . " ";
                ?>
                <a href="<?php echo $loginUrl; ?>"><?php echo Lang::txt('COM_JOBS_ACTION_LOGIN') ?></a>
                <?php echo " " . Lang::txt('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS') ?>
            </li>
        <?php } elseif ($this->emp && $this->config->get('allowsubscriptions', 0)) { ?>
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
            <li>
                <a class="icon-add add btn" href="<?php echo $addjobUrl; ?>">
                    <?php echo Lang::txt('COM_JOBS_ADD_ANOTHER_JOB'); ?>
                </a>
            </li>
        <?php } elseif ($this->admin) { ?>
            <li>
                <a class="icon-dashboard myjobs btn" href="<?php echo $dashboardUrl; ?>">
                    <?php echo Lang::txt('COM_JOBS_ADMIN_DASHBOARD'); ?>
                </a>
            </li>
            <li>
                <a class="icon-add add btn" href="<?php echo $addjobUrl; ?>">
                    <?php echo Lang::txt('COM_JOBS_ADD_ANOTHER_JOB'); ?>
                </a>
            </li>
        <?php } else { ?>
            <li>
                <a class="myresume btn" href="<?php echo $resumeUrl; ?>">
                    <?php echo Lang::txt('COM_JOBS_MY_RESUME'); ?>
                </a>
            </li>
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
        <?php if (!$job->applied && !$job->withdrawn && $job->status == 1) { ?>
            <span class="apply">
                <a href="<?php echo $applyUrl ?>">
                    <button class="btn btn-success"><?php echo Lang::txt('COM_JOBS_APPLY_NOW') ?></button>
                </a>
            </span>
        <?php } ?>
        <?php if ($job->withdrawn && $job->status == 1) { ?>
            <span class="apply">
                <a href="<?php echo $applyUrl ?>">
                    <button class="btn btn-success"><?php echo Lang::txt('COM_JOBS_ACTION_REAPPLY') ?></button>
                </a>
            </span>
        <?php } ?>
        <?php if ($job->applied) { ?>
            <span class="applied">
                <a href="<?php echo $editappUrl ?>">
                    <button class="btn btn-success">
                        <?php echo Lang::txt('COM_JOBS_ACTION_EDIT_APPLICATION') ?>
                    </button>
                </a>
                <a href="<?php echo $withdrawUrl ?>" id="showconfirm">
                    <button class="btn btn-danger">
                        <?php echo Lang::txt('COM_JOBS_ACTION_WITHDRAW_APPLICATION') ?>
                    </button>
                </a>
            </span>
        <?php } ?>
        <?php if ($owner && ($job->status == 1 || $job->status == 3)) { ?>
            <span class="edit">
                <a href="<?php echo $editjobUrl ?>">
                    <button class="btn btn">
                        <?php echo ' ' . Lang::txt('COM_JOBS_ACTION_EDIT_JOB'); ?>
                    </button>
                </a>
            </span>
        <?php } ?>
        <?php if ($job->status == 1 && $owner) { ?>
            <?php $preserveTitle = Lang::txt('COM_JOBS_NOTICE_ACCESS_PRESERVED'); ?>
             <span class="unpublish">
                <a href="<?php echo $unpublishUrl ?>"
                    title="<?php echo $preserveTitle ?>">
                    <button class="btn">
                        <?php echo Lang::txt('COM_JOBS_ACTION_UNPUBLISH_THIS_JOB') ?>
                    </button>
                </a>
            </span>
        <?php } ?>
        <?php if ($job->status == 3) { ?>
            <?php $includeTitle = Lang::txt('COM_JOBS_ACTION_INCLUDE_INPUBLIC_LISTING'); ?>
            <?php $deleteTitle = Lang::txt('COM_JOBS_ACTION_DELETE_ALL_RECORDS'); ?>
            <span class="manageroptions">
                <a href="<?php echo $reopenUrl ?>"
                    title="<?php echo $includeTitle ?>">
                    <button class="btn btn">
                        <?php echo Lang::txt('COM_JOBS_ACTION_REOPEN_THIS') ?>
                    </button>
                </a>
                <a href="<?php echo $removeUrl ?>"
                    title="<?php echo $deleteTitle ?>">
                    <button class="btn btn">
                        <?php echo Lang::txt('COM_JOBS_ACTION_DELETE_THIS_JOB') ?>
                    </button>
                </a>
            </span>
        <?php } ?>
        <?php
        if ($job->applied) {
            \Components\Jobs\Helpers\Html::confirmscreen(
                $jobViewUrl,
                $withdrawUrl,
                $action = "withdrawapp"
            );
        }
        ?>
    </span>

    <div id="jobinfo">
        <h3>
            <span><?php echo Lang::txt('COM_JOBS_JOB_REFERENCE_CODE') . ': ' . $job->code ?></span>
            <?php $job->title . ' - ' ?>
            <?php if (preg_match('/(.*)http/i', $job->companyWebsite)) { ?>
                <a href="<?php echo $job->companyWebsite ?>"><?php echo $job->companyName ?></a>
            <?php } else {
                echo $job->companyName;
            }
            echo ', ' . $job->companyLocation;
            if ($job->companyLocationCountry) {
                echo ', ' . strtoupper($job->companyLocationCountry);
            } else {
                echo '';
            } ?>
        </h3><?php echo "\n";?>

        <div class="clear"></div><?php echo "\n" ?>
        <div class="apply">
        <p>
            <?php if ($job->applied) { ?>
                <span class="alreadyapplied">
                    <?php
                    $appliedDate = Date::of($job->applied)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                    echo Lang::txt('COM_JOBS_JOB_APPLIED_ON') . ' ' . $appliedDate;
                    ?>
                <span><?php echo "\n";
            } elseif ($job->withdrawn) { ?>
                <span class="withdrawn">
                    <?php
                    $withdrewDate = Date::of($job->withdrawn)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                    echo Lang::txt('COM_JOBS_JOB_WITHDREW_ON') . ' ' . $withdrewDate;
                    ?>
                <span><?php echo "\n";
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
            <p class="reg details">
                <?php echo Lang::txt('COM_JOBS_JOB_INFO_CONTACT') ?>:
            </p><?php echo "\n"; ?>
            <p class="reg"><?php echo "\n"; ?>
            <span class="contactname"><?php echo $job->contactName ?></span>
            <?php echo "\n";
            if ($job->contactPhone) { ?>
                <span class="contactinfo">
                    <?php echo Lang::txt('COM_JOBS_JOB_TABLE_TEL') . ': ' . $job->contactPhone ?>
                </span><?php echo "\n";
            } else {
                echo '';
            }
            if ($job->contactEmail) { ?>
                <span class="contactinfo">
                    <?php echo Lang::txt('COM_JOBS_JOB_TABLE_EMAIL') . ': ' . $job->contactEmail ?>
                </span><?php echo "\n";
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
                    <span class="makechanges">
                        <a href="<?php echo $confirmUrl ?>">
                            <button class="btn btn-success">
                                <?php echo Lang::txt('COM_JOBS_ACTION_PUBLISH_AD') ?>
                            </button>
                        </a>
                    </span>
                    <span class="makechanges">
                        <a href="<?php echo $editjobUrl ?>">
                            <button class="btn">
                                <?php echo Lang::txt('COM_JOBS_ACTION_MAKE_CHANGES') ?>
                            </button>
                        </a>
                    </span>
                    <span class="makechanges">
                        <a href="<?php echo $removeUrl ?>">
                            <button class="btn">
                                <?php echo Lang::txt('COM_JOBS_ACTION_REMOVE_AD') ?>
                            </button>
                        </a>
                    </span>
                </p>
            <?php }
        } ?>
    </div>
    <?php if ($owner) { ?>
        <?php
        $applicantsUrl = Route::url(
            'index.php?option=com_jobs&task=resumes?filterby=applied'
        );
        ?>
        <span class="review_applicants">
            <a href="<?php echo $applicantsUrl; ?>">
                <button class="btn btn">
                    <?php echo Lang::txt('COM_JOBS_REVIEW_APPLICANTS'); ?>
                </button>
            </a>
        </span>
        <h3>
            <?php echo Lang::txt('COM_JOBS_APPLICATIONS')
                . ' (' . count($job->applications) . ' '
                . Lang::txt('COM_JOBS_TOTAL') . ')'; ?>
        </h3>
        <?php if (count($job->applications) <= 0) { ?>
            <p><?php echo Lang::txt('COM_JOBS_NOTICE_APPLICATIONS_NONE'); ?></p>
        <?php } else { ?>
            <ul id="candidates">
            <?php $k = 1;
            for ($i = 0, $n = count($job->applications); $i < $n; $i++) {
                if ($job->applications[$i]->seeker && $job->applications[$i]->status != 2) {
                    $applied = ($job->applications[$i]->applied
                        && $job->applications[$i]->applied != '0000-00-00 00:00:00')
                        ? Date::of($job->applications[$i]->applied)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
                        : Lang::txt('N/A');
                    $applicantUrl = Route::url(
                        'members/' . $job->applications[$i]->uid . "/resume"
                    );
                    ?>
                    <li class="applic">
                    <span class="countc"><?php echo $k . ". " ?></span>
                    <a href="<?php echo $applicantUrl; ?>">
                        <?php echo $job->applications[$i]->seeker->name ?>
                    </a>
                    <?php echo ' ' . Lang::txt('applied on') . ' ' . $applied;
                    if ($job->applications[$i]->cover) { ?>
                        <blockquote><?php echo trim(stripslashes($job->applications[$i]->cover)) ?></blockquote>
                    <?php } else {
                        echo '';
                    } ?>
                    </li>
                    <li>
                    <!-- show seeker info -->
                    <?php
                    $out = Event::trigger(
                        'members.showSeeker',
                        array(
                            $job->applications[$i]->seeker,
                            $this->emp,
                            $this->admin,
                            'com_members',
                            $list = 0
                        )
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
            }?>
            </ul>
            <?php if (count($job->withdrawnlist) > 0) { ?>
                <p>
                    <?php echo count($job->withdrawnlist) . ' '
                        . Lang::txt('COM_JOBS_NOTICE_CANDIDATES_WITHDREW'); ?>
                </p>
            <?php } ?>
        <?php }
    } ?>
</section>

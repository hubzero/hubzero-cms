<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/* Application Form */

$job         = $this->job;
$seeker      = $this->seeker;
$application = $this->application;
$owner       = (User::get('id') == $job->employerid or $this->admin) ? 1 : 0;

$loginUrl = Route::url(
    'index.php?option=' . $this->option . '&task=view'
) . '?action=login';
$loginText = Lang::txt('COM_JOBS_ACTION_LOGIN');
$loginSuffix = Lang::txt('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS');
$dashboardUrl = Route::url(
    'index.php?option=' . $this->option . '&task=dashboard'
);
$shortlistUrl = Route::url(
    'index.php?option=' . $this->option . '&task=resumes'
) . '?filterby=shortlisted';
$browseUrl = Route::url(
    'index.php?option=' . $this->option . '&task=browse'
);
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <div id="content-header-extra">
        <ul id="useroptions">
        <?php if (User::isGuest()) { ?>
            <li>
                <?php echo Lang::txt('COM_JOBS_PLEASE')
                    . ' <a href="' . $loginUrl . '">'
                    . $loginText . '</a> ' . $loginSuffix; ?>
            </li>
        <?php } elseif ($this->emp && $this->config->get('allowsubscriptions', 0)) {  ?>
            <li>
                <a class="myjobs btn" href="<?php echo $dashboardUrl; ?>">
                    <?php echo Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD'); ?>
                </a>
            </li>
            <li>
                <a class="shortlist btn" href="<?php echo $shortlistUrl; ?>">
                    <?php echo Lang::txt('COM_JOBS_SHORTLIST'); ?>
                </a>
            </li>
        <?php } elseif ($this->admin) { ?>
            <li>
                <?php echo Lang::txt('COM_JOBS_NOTICE_YOU_ARE_ADMIN'); ?>
                <a class="myjobs btn" href="<?php echo $dashboardUrl; ?>">
                    <?php echo Lang::txt('COM_JOBS_ADMIN_DASHBOARD'); ?>
                </a>
            </li>
        <?php } else { ?>
            <li>
                <a class="alljobs btn" href="<?php echo $browseUrl; ?>">
                    <?php echo Lang::txt('COM_JOBS_ALL_JOBS'); ?>
                </a>
            </li>
        <?php } ?>
        </ul>
    </div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<?php
$resumeUrl = Route::url(
    'index.php?option=com_members&id=' . User::get('id')
    . '&active=resume'
);
$createProfileLink = '<a href="' . $resumeUrl
    . '" class="add">'
    . Lang::txt('COM_JOBS_ACTION_CREATE_PROFILE') . '</a>';
?>
<?php if (!$seeker) { ?>
    <p class="warning">
        <?php echo Lang::txt('COM_JOBS_APPLY_TO_APPLY')
            . ' ' . Config::get('sitename')
            . ' ' . Lang::txt('COM_JOBS_APPLY_NEED_RESUME'); ?>
        <?php echo $createProfileLink; ?>
    </p>
<?php } else { ?>
    <section class="main section">
        <?php
        $job->title = trim(stripslashes($job->title));
        $appid = $application->status != 2 ? $application->id : 0;
        ?>

        <?php
        $isOwnAd = (!$this->admin && User::get('id') == $job->employerid)
            or ($this->admin && $job->employerid == 1);
        if ($isOwnAd) {
            ?>
            <p class="warning"><?php echo Lang::txt('COM_JOBS_APPLY_WARNING_OWN_AD'); ?></p>
        <?php } ?>

        <div id="applyinfo">
            <h3>
                <?php echo $job->title; ?> -
                <?php
                echo preg_match('/(.*)http/i', $job->companyWebsite)
                    ? '<a href="' . $job->companyWebsite . '">' . $job->companyName . '</a>'
                    : $job->companyName;
                ?>,
                <?php echo $job->companyLocation; ?>,
                <?php echo $job->companyLocationCountry; ?>
                <span>
                    <?php echo Lang::txt('COM_JOBS_JOB_REFERENCE_CODE'); ?>: <?php echo $job->code; ?>
                </span>
            </h3>
        </div>

        <?php
        $saveAppUrl = Route::url(
            'index.php?option=' . $this->option . '&task=saveapp'
        );
        ?>
        <form id="hubForm" method="post" action="<?php echo $saveAppUrl; ?>">
            <div class="explaination">
                <p><?php echo Lang::txt('COM_JOBS_APPLY_HINT_COVER_LETTER'); ?></p>
            </div>
            <fieldset>
                <input type="hidden"  name="task" value="saveapp" />
                <input type="hidden" id="code" name="code" value="<?php echo $job->code; ?>" />
                <input type="hidden" id="jid" name="jid" value="<?php echo $job->id; ?>" />
                <input type="hidden" id="appid" name="appid" value="<?php echo $appid; ?>" />
                <input type="hidden" id="uid" name="uid" value="<?php echo User::get('id'); ?>" />
                <h3>
                    <?php echo Lang::txt('COM_JOBS_APPLY_MSG_TO_EMPLOYER'); ?>
                    <span class="opt">(<?php echo Lang::txt('COM_JOBS_OPTIONAL'); ?>)</span>
                </h3>
                <label>
                    <textarea name="cover" id="cover" rows="10" cols="15"><?php echo $application->cover; ?></textarea>
                </label>
            </fieldset>
            <div class="clear"></div>

            <div class="subject custom">
                <?php
                // profile info
                if ($seeker) {
                    // show seeker info
                    $out = Event::trigger(
                        'members.showSeeker',
                        array(
                            $seeker,
                            $this->emp,
                            $this->admin,
                            'com_members',
                            $list = 0
                        )
                    );
                    if (count($out) > 0) {
                        echo implode("\n", $out);
                    }
                }
                ?>
            </div>
            <p class="submit">
                <?php
                $submitLabel = $this->task == 'editapp'
                    ? Lang::txt('COM_JOBS_ACTION_SAVE_CHANGES_APPLICATION')
                    : Lang::txt('COM_JOBS_ACTION_APPLY_THIS_JOB');
                $jobUrl = Route::url(
                    'index.php?option=' . $this->option
                    . '&task=job&id=' . $job->code
                );
                ?>
                <input class="btn btn-success" type="submit" name="submit" value="<?php echo $submitLabel; ?>" />
                    <a href="<?php echo $jobUrl; ?>">
                        <button type="button" class="btn btn-secondary">
                            <?php echo Lang::txt('JCANCEL'); ?>
                        </button>
                    </a>
            </p>
        </form>
    </section>
<?php }

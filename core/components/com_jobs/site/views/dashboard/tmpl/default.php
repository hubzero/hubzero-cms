<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

    $allowed_ads = $this->service->maxads - $this->activejobs;
    $allowed_ads = $allowed_ads < 0 ? 0 : $allowed_ads;

    $class = 'no';
switch ($this->subscription->status) {
    case '0':
        $status = Lang::txt('COM_JOBS_JOB_STATUS_PENDING');
        break;
    case '1':
        $status = Lang::txt('COM_JOBS_JOB_STATUS_ACTIVE');
        $class  = 'yes';
        break;
    case '2':
        $status = Lang::txt('COM_JOBS_JOB_STATUS_CANCELLED');
        break;
    default:
        $status = Lang::txt('N/A');
        break;
}

    $today = date('Y-m-d');

    $isExpired = $this->subscription->expires < $today
        && $this->subscription->status == 1;
    $status = $isExpired
        ? Lang::txt('COM_JOBS_SUBSCRIPTION_STATUS_EXPIRED')
        : $status;
    $length = $this->subscription->status == 0
                ? $this->subscription->pendingunits
                : $this->subscription->units;
    $hasPending = $this->subscription->pendingunits
        && $this->subscription->status == 1;
if ($hasPending) {
    $addlTxt = Lang::txt('COM_JOBS_ADDITIONAL');
    $pendTxt = Lang::txt('COM_JOBS_MONTHS_PENDING');
    $pending = ' <span class="no">'
        . '(' . $this->subscription->pendingunits
        . ' ' . $addlTxt
        . ' ' . $this->service->unitmeasure . 'MULTIPLE_S'
        . ' ' . $pendTxt . ')</span>';
} else {
    $pending = '';
}
    $expiredate = $this->subscription->expires
                ? Date::of($this->subscription->expires)->toLocal(
                    Lang::txt('DATE_FORMAT_HZ1')
                )
                : Lang::txt('N/A');

    // site admins
    if ($this->masterAdmin) {
        $this->subscription->code = Lang::txt(' N/A');
        $this->service->title = Lang::txt(
            'COM_JOBS_NOTICE_ADMIN_UNLIMITED_ACCESS'
        );
        $class  = 'yes';
        $status = Lang::txt(
            'COM_JOBS_SUBSCRIPTION_STATUS_ACTIVE_ADMIN'
        );
    }

    $shortlistUrl = Route::url(
        'index.php?option=' . $this->option . '&task=resumes'
    ) . '?filterby=shortlisted';
    $resumesUrl = Route::url(
        'index.php?option=' . $this->option . '&task=resumes'
    );
    $batchUrl = Route::url(
        'index.php?option=' . $this->option . '&task=batch'
    );
    $subscribeUrl = Route::url(
        'index.php?option=' . $this->option . '&task=subscribe'
    );
    $addjobUrl = Route::url(
        'index.php?option=' . $this->option . '&task=addjob'
    );
    $dashUrl = Route::url(
        'index.php?option=' . $this->option
        . '&task=dashboard&uid=' . $this->uid
    );
    $cancelUrl = Route::url(
        'index.php?option=' . $this->option
        . '&task=cancel&uid=' . $this->uid
    );

    $browseTxt = Lang::txt('COM_JOBS_ACTION_BROWSE_RESUMES');
    $viewTxt = Lang::txt('COM_JOBS_DASHBOARD_VIEW');
    $downloadTxt = Lang::txt('COM_JOBS_DASHBOARD_DOWNLOAD');
    ?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <?php if ($this->emp && !$this->masterAdmin) { ?>
        <div id="content-header-extra">
            <ul id="useroptions">
                <li>
                    <a class="shortlist btn"
                        href="<?php echo $shortlistUrl; ?>">
                        <?php echo Lang::txt('COM_JOBS_SHORTLIST'); ?>
                    </a>
                </li>
            </ul>
        </div><!-- / #content-header-extra -->
    <?php } ?>
</header><!-- / #content-header -->

<section class="main section">
    <div class="grid">
        <div class="col span6">
            <div id="activities">
                <h3><?php echo Lang::txt('COM_JOBS_DASHBOARD_ACTIVITIES'); ?></h3>
                <h4>
                    <?php
                    $totalResumes = $this->stats['total_resumes'];
                    ?>
                    <a href="<?php echo $resumesUrl; ?>">
                        <?php echo $browseTxt
                            . ' (' . $totalResumes . ')'; ?>
                    </a>
                </h4>
                <?php $poolTxt = Lang::txt('COM_JOBS_DASHBOARD_TOTAL_POOL'); ?>
                <span class="sub-heading"><?php echo $poolTxt; ?></span>
                <p>
                    <span class="view">
                        <a href="<?php echo $resumesUrl; ?>"
                            class="cancelit">
                            [ <?php echo $viewTxt; ?> ]
                        </a>
                    </span><?php echo $totalResumes; ?>
                </p>
                <?php $shortTxt = Lang::txt('COM_JOBS_DASHBOARD_SHORTLISTED'); ?>
                <span class="sub-heading"><?php echo $shortTxt; ?></span>
                <p>
                    <span class="view">
                        <?php if ($this->stats['shortlisted'] > 0) {
                            $dlUrl = $batchUrl . '?pile=shortlisted';
                            ?>
                            <a href="<?php echo $dlUrl; ?>"
                                class="cancelit">
                                [ <?php echo $downloadTxt; ?> ]
                            </a> &nbsp;&nbsp;&nbsp;
                        <?php }
                        $viewShortUrl = $resumesUrl
                            . '?filterby=shortlisted';
                        ?>
                        <a href="<?php echo $viewShortUrl; ?>"
                            class="cancelit">
                            [ <?php echo $viewTxt; ?> ]
                        </a>
                    </span><?php echo $this->stats['shortlisted']; ?>
                </p>
                <?php $appliedTxt = Lang::txt('COM_JOBS_DASHBOARD_APPLIED_TO_ADS'); ?>
                <span class="sub-heading"><?php echo $appliedTxt; ?></span>
                <p>
                    <span class="view">
                        <?php if ($this->stats['applied'] > 0) {
                            $dlAppUrl = $batchUrl . '?pile=applied';
                            ?>
                            <a href="<?php echo $dlAppUrl; ?>"
                                class="cancelit">
                                [ <?php echo $downloadTxt; ?> ]
                            </a> &nbsp;&nbsp;&nbsp;
                        <?php }
                        $viewAppUrl = $resumesUrl
                            . '?filterby=applied';
                        ?>
                        <a href="<?php echo $viewAppUrl; ?>"
                            class="cancelit">
                            [ <?php echo $viewTxt; ?> ]
                        </a>
                    </span><?php echo $this->stats['applied']; ?>
                </p>
                <div class="spacer"></div>
                <?php
                $manageTxt = Lang::txt('COM_JOBS_DASHBOARD_MANAGE_ADS');
                $jobCount = count($this->myjobs);
                ?>
                <h4>
                    <span>
                        <?php echo $manageTxt
                            . ' (' . $jobCount . ')'; ?>
                    </span>
                </h4>

                <p class="reg">
                    <?php
                    $haveTxt = Lang::txt(
                        'COM_JOBS_DASHBOARD_YOU_HAVE_CURRENTLY'
                    );
                    $pubTxt = Lang::txt(
                        'COM_JOBS_DASHBOARD_PUBLISHED_ADS'
                    );
                    ?>
                    <span>
                        <?php echo $haveTxt . ' '
                            . $this->activejobs . ' ' . $pubTxt;
                        if (!$this->masterAdmin) {
                            $stillTxt = Lang::txt(
                                'COM_JOBS_DASHBOARD_NUMBER_ADS_STILL_ALLOWED'
                            );
                            ?> <br /><?php
                            echo $allowed_ads . ' ' . $stillTxt;
                        } ?>
                    </span>
                </p>
                <?php if ($jobCount > 0) {
                    foreach ($this->myjobs as $mj) {
                        $jobUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&task=job&code=' . $mj->code
                        );
                        ?>
                <p class="reg myjob<?php
                if ($mj->status == 3) {
                    echo '_inactive';
                } elseif ($mj->status == 4 or $mj->status == 0) {
                    echo '_pending';
                } ?>">
                    <span class="view"><?php
                    if ($mj->status == 1) {
                        $appsTxt = Lang::txt(
                            'COM_JOBS_DASHBOARD_APPLICATIONS'
                        );
                        $appsUrl = $jobUrl . '#applications';
                        echo $mj->applications . ' '
                            . $appsTxt . ' ';
                        ?>
                        <a href="<?php echo $appsUrl; ?>"
                            class="cancelit">
                            [ <?php echo $viewTxt; ?> ]
                        </a>
                        <?php
                    } elseif ($mj->status == 4) {
                        $draftTxt = strtolower(
                            Lang::txt('COM_JOBS_JOB_STATUS_DRAFT')
                        );
                        echo '(' . $draftTxt . ')';
                    } elseif ($mj->status == 0) {
                        $pendingTxt = strtolower(
                            Lang::txt('COM_JOBS_JOB_STATUS_PENDING')
                        );
                        echo '(' . $pendingTxt . ')';
                    } elseif ($mj->status == 3) {
                        $inactiveTxt = strtolower(
                            Lang::txt('COM_JOBS_JOB_STATUS_INACTIVE')
                        );
                        echo '(' . $inactiveTxt . ')';
                    } ?>
                    </span>
                        <?php
                        $truncTitle = \Hubzero\Utility\Str::truncate(
                            $mj->title,
                            50
                        );
                        ?>
                    <span class="code"><?php echo $mj->code; ?></span>:
                    <a href="<?php echo $jobUrl; ?>">
                        <?php echo $truncTitle; ?>
                    </a>
                    </p>
                    <?php }
                } ?>
            <?php if ($this->subscription->status == 1 or $this->masterAdmin) { ?>
                <p class="reg">
                    <a class="add btn"
                        href="<?php echo $addjobUrl; ?>">
                        <?php echo Lang::txt('COM_JOBS_DASHBOARD_AD_NEW_JOB'); ?>
                    </a>
                </p>
            <?php } ?>
            </div>
        </div>
        <div class="col span6 omega">
            <div id="subinfo">
                <?php
                $detailsTxt = Lang::txt('COM_JOBS_SUBSCRIPTION_DETAILS');
                $refTxt = Lang::txt('COM_JOBS_JOB_REFERENCE_CODE');
                $subCode = $this->subscription->code;
                ?>
                <h3>
                    <?php echo $detailsTxt; ?>
                    <span>
                        <?php echo $refTxt . ': ' . $subCode; ?>
                    </span>
                </h3>

                <?php $svcTxt = Lang::txt('COM_JOBS_SUBSCRIPTION_SERVICE'); ?>
                <span class="sub-heading"><?php echo $svcTxt; ?></span>
                <p><?php echo $this->service->title; ?></p>

                <?php $statusTxt = Lang::txt('COM_JOBS_TABLE_STATUS'); ?>
                <span class="sub-heading"><?php echo $statusTxt; ?></span>
                <p class="<?php echo $class; ?>"><?php echo $status; ?></p>

                <?php if (!$this->masterAdmin) { ?>
                    <?php $lenTxt = Lang::txt('COM_JOBS_SUBSCRIPTION_LENGTH'); ?>
                    <span class="sub-heading"><?php echo $lenTxt; ?></span>
                    <p>
                        <?php echo $length . '-'
                            . $this->service->unitmeasure . $pending; ?>
                    </p>

                    <?php $expTxt = Lang::txt('COM_JOBS_SUBSCRIPTION_EXPIRE_DATE'); ?>
                    <span class="sub-heading"><?php echo $expTxt; ?></span>
                    <p><?php echo $expiredate; ?></p>
                    <p>
                        <?php
                        $renewTxt = Lang::txt(
                            'COM_JOBS_SUBSCRIPTION_EXTEND_OR_RENEW_OR_CANCEL'
                        );
                        ?>
                        <a href="<?php echo $subscribeUrl; ?>"
                            class="cancelit">
                            [ <?php echo $renewTxt; ?> ]
                        </a>
                    </p>
                    <?php
                    echo \Components\Jobs\Helpers\Html::confirmscreen(
                        $dashUrl,
                        $cancelUrl
                    );
                    ?>
                    <div class="spacer"></div>

                    <?php
                    $empInfoTxt = Lang::txt(
                        'COM_JOBS_SUBSCRIPTION_EMPLOYER_INFORMATION'
                    );
                    $usernameTxt = Lang::txt(
                        'COM_JOBS_EMPLOYER_USERNAME'
                    );
                    ?>
                    <h3>
                        <?php echo $empInfoTxt; ?>
                        <span>
                            <?php echo $usernameTxt
                                . ': ' . $this->login; ?>
                        </span>
                    </h3>

                    <?php $compTxt = Lang::txt('COM_JOBS_EMPLOYER_COMPANY'); ?>
                    <span class="sub-heading"><?php echo $compTxt; ?></span>
                    <p>
                        <?php
                        $unspecTxt = Lang::txt('COM_JOBS_NOTICE_UNSPECIFIED');
                        echo $this->employer->companyName
                            ? $this->employer->companyName
                            : $unspecTxt;
                        ?>
                    </p>

                    <?php $locTxt = Lang::txt('COM_JOBS_EMPLOYER_LOCATION'); ?>
                    <span class="sub-heading"><?php echo $locTxt; ?></span>
                    <p>
                        <?php
                        echo $this->employer->companyLocation
                            ? $this->employer->companyLocation
                            : $unspecTxt;
                        ?>
                    </p>

                    <?php $webTxt = Lang::txt('COM_JOBS_EMPLOYER_WEBSITE'); ?>
                    <span class="sub-heading"><?php echo $webTxt; ?></span>
                    <p>
                        <?php
                        echo $this->employer->companyWebsite
                            ? $this->employer->companyWebsite
                            : $unspecTxt;
                        ?>
                    </p>
                    <p>
                        <?php
                        $editTxt = Lang::txt('COM_JOBS_EMPLOYER_EDIT_INFO');
                        ?>
                        <a href="<?php echo $subscribeUrl; ?>"
                            class="cancelit">
                            [ <?php echo $editTxt; ?> ]
                        </a>
                    </p>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

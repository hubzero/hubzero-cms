<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$this->css('diskspace')
     ->js('diskspace');

$minGitSize = 61440;

$usageGit = $this->params->get('disk_usage');

// Check used space against quota (percentage)
$inuse = round((($this->dirsize * 100 ) / $this->quota), 1);
if ($this->total > 0 && $inuse < 1) {
    $inuse = round((($this->dirsize * 100 ) / $this->quota), 2);
    if ($inuse < 0.1) {
        $inuse = 0.01;
    }
}
$working = ($usageGit || $this->by == 'admin') ? $this->totalspace - $this->dirsize : $this->totalspace;

$actual  = $working > 0 ? round((($working * 100 ) / $this->quota), 1) : null;
$actual  = $actual > 100 ? 100 : $actual;
if ($this->versionTracking == '0') {
    $versions = 0;
    $working = $this->dirsize;
} else {
    $versions = $this->dirsize - $working;
    $versions = ($versions > $minGitSize && ($usageGit || $this->by == 'admin')) ?
    \Hubzero\Utility\Number::formatBytes($versions) : 0;
}

$inuse = ($inuse > 100) ? '> 100' : $inuse;

$quota = \Hubzero\Utility\Number::formatBytes($this->quota);

$used  = ($usageGit)
        ? \Hubzero\Utility\Number::formatBytes($this->dirsize)
        : \Hubzero\Utility\Number::formatBytes($working);

$unused = $usageGit
        ? \Hubzero\Utility\Number::formatBytes($this->quota - $this->dirsize)
        : \Hubzero\Utility\Number::formatBytes($this->quota - $working);
$unused = $unused <= 0 ? 'none' : $unused;
$approachingQuota = $this->config->get('approachingQuota', 85);
$approachingQuota = intval($approachingQuota) > 0 ? $approachingQuota : 85;
$warning = ($inuse > $approachingQuota) ? 1 : 0;

?>
<?php if ($this->by != 'admin') { ?>
    <?php
    $filesUrl = Route::url(
        'index.php?option=' . $this->option
        . '&alias=' . $this->model->get('alias')
        . '&active=files'
    );
    $diskUsageLabel = Lang::txt(
        'PLG_PROJECTS_FILES_DISK_USAGE'
    );
    ?>
    <div id="plg-header">
        <h3 class="files">
            <a href="<?php echo $filesUrl; ?>">
                <?php echo $this->title; ?>
            </a>
            &raquo;
            <span class="subheader">
                <?php echo $diskUsageLabel; ?>
            </span>
        </h3>
    </div>
<?php } ?>
    <div id="disk-usage" <?php if ($warning) {
        echo 'class="quota-warning"';
                         } ?>>
        <div class="disk-usage-wrapper">
            <?php
            $h3Text = ($this->by != 'admin')
                ? Lang::txt('PLG_PROJECTS_FILES_QUOTA')
                    . ': ' . $quota
                : Lang::txt('PLG_PROJECTS_FILES_DISK_USAGE');
            ?>
            <h3><?php echo $h3Text; ?></h3>
            <?php if ($this->by != 'admin') { ?>
                <?php
                $indicatorText = $inuse . '% '
                    . Lang::txt('PLG_PROJECTS_FILES_USED')
                    . ' (' . $used . ' '
                    . Lang::txt('COM_PROJECTS_OUT_OF')
                    . ' ' . $quota . ')';
                $quotaAlert = ($inuse == '> 100')
                    ? Lang::txt('PLG_PROJECTS_FILES_OVER_QUOTA')
                    : Lang::txt(
                        'PLG_PROJECTS_FILES_APPROACHING_QUOTA'
                    );
                ?>
                <span id="indicator-value">
                    <span>
                        <?php echo $indicatorText; ?>
                    </span>
                    <?php if ($warning) { ?>
                        <span class="approaching-quota">
                            - <?php echo $quotaAlert; ?>
                        </span>
                    <?php } ?>
                </span>
            <?php } ?>
            <?php if ($this->by != 'admin') { ?>
            <div id="indicator-wrapper">
                <span id="indicator-area" class="used:<?php echo $inuse; ?>">&nbsp;</span>  <?php if ($actual > 0) { ?>
                    <span id="actual-area" class="actual:<?php echo $actual; ?>">&nbsp;</span>
                                                      <?php } ?>
            </div>
            <div id="usage-labels">
                    <?php
                    $workingFormatted = \Hubzero\Utility\Number::formatBytes($working);
                    $filesLabel = Lang::txt('PLG_PROJECTS_FILES_FILES')
                        . ' (' . $workingFormatted . ')';
                    ?>
                    <span class="l-actual">&nbsp;</span>
                    <?php echo $filesLabel; ?>
                    <?php if ($versions > 0) {
                        $versionLabel = $this->by == 'admin'
                            ? Lang::txt('Versions')
                            : Lang::txt('Version History*');
                        $versionLabel .= ' (' . $versions . ')';
                        ?>
                    <span class="l-regular">&nbsp;</span>
                        <?php echo $versionLabel;
                    } ?>
                    <?php
                    $unusedLabel = Lang::txt(
                        'PLG_PROJECTS_FILES_UNUSED_SPACE'
                    ) . ' (' . $unused . ')';
                    ?>
                    <span class="l-unused">&nbsp;</span>
                    <?php echo $unusedLabel; ?>
            </div>
            <?php } else { ?>
            <div id="usage-labels" class="usage-admin">
                <?php
                $adminFilesLabel = Lang::txt(
                    'PLG_PROJECTS_FILES_FILES'
                ) . ': ' . \Hubzero\Utility\Number::formatBytes(
                    $working
                );
                $adminHistoryLabel = Lang::txt(
                    'PLG_PROJECTS_FILES_HISTORY'
                ) . ': ' . $versions;
                $adminAvailLabel = Lang::txt(
                    'PLG_PROJECTS_FILES_AVAILABLE'
                ) . ': ' . $unused;
                $projectFilesLabel = Lang::txt(
                    'PLG_PROJECTS_FILES_PROJECT_FILES'
                );
                ?>
                <span class="l-h">
                    <?php echo $projectFilesLabel; ?>
                    <span class="l-actual">&nbsp;</span>
                    <?php echo $adminFilesLabel; ?>
                    <span class="l-regular">&nbsp;</span>
                    <?php echo $adminHistoryLabel; ?>
                    <span class="l-unused">&nbsp;</span>
                    <?php echo $adminAvailLabel; ?>
                <span>
                <?php if (isset($this->pubDiskUsage)) {
                    $unusedPub = $this->pubQuota - $this->pubDiskUsage;
                    $unusedPub = $unusedPub <= 0 ? 'none' : \Hubzero\Utility\Number::formatBytes($unusedPub);
                    $pubLabel = Lang::txt(
                        'PLG_PROJECTS_FILES_SPACE_PUBLISHED'
                    ) . ': ' . \Hubzero\Utility\Number::formatBytes(
                        $this->pubDiskUsage
                    );
                    $pubAvailLabel = Lang::txt(
                        'PLG_PROJECTS_FILES_AVAILABLE'
                    ) . ': ' . $unusedPub;
                    $pubHeadLabel = Lang::txt(
                        'PLG_PROJECTS_FILES_PUBLICATIONS'
                    );
                    ?>
                <span class="l-h">
                    <?php echo $pubHeadLabel; ?>
                    <span class="l-pub">&nbsp;</span>
                    <?php echo $pubLabel; ?>
                    <span class="l-unused">&nbsp;</span>
                    <?php echo $pubAvailLabel; ?>
                <span>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php if ($versions && $this->by != 'admin') { ?>
    <p class="mini faded"><?php echo Lang::txt('PLG_PROJECTS_FILES_ABOUT_HISTORY_SPACE'); ?></p>
    <?php } ?>

    <?php if (
    $this->by != 'admin' && $this->model->access('manager')
        && $this->params->get('diskspace_options') && $versions > 0
) { ?>
    <?php
    $optimizeUrl = Route::url(
        'index.php?option=' . $this->option
        . '&alias=' . $this->model->get('alias')
        . '&active=files&action=optimize'
    );
    $optimizeLabel = Lang::txt(
        'PLG_PROJECTS_FILES_OPTIMIZE'
    );
    $optimizeAbout = Lang::txt(
        'PLG_PROJECTS_FILES_ABOUT_FILE_OPTIMIZE'
    );
    $advOptimizeUrl = Route::url(
        'index.php?option=' . $this->option
        . '&alias=' . $this->model->get('alias')
        . '&active=files&action=advoptimize'
    );
    $advOptimizeLabel = Lang::txt(
        'PLG_PROJECTS_FILES_OPTIMIZE_ADV'
    );
    $advOptimizeAbout = Lang::txt(
        'PLG_PROJECTS_FILES_ABOUT_FILE_OPTIMIZE_ADV'
    );
    ?>
    <div id="disk-manage">
        <h4><?php echo Lang::txt('PLG_PROJECTS_FILES_MANAGE_SPACE'); ?></h4>
        <p class="mini faded">
            <?php echo Lang::txt('PLG_PROJECTS_FILES_ABOUT_DISK_MANAGE_OPTIONS'); ?>
        </p>
        <p class="disk-manage-option">
            <a class="btn manage disk-usage-optimize"
               href="<?php echo $optimizeUrl; ?>">
                <?php echo $optimizeLabel; ?>
            </a>
            <span class="diskmanage-about">
                <?php echo $optimizeAbout; ?>
            </span>
        </p>

        <p class="disk-manage-option">
            <a class="btn manage disk-usage-optimize"
               href="<?php echo $advOptimizeUrl; ?>">
                <?php echo $advOptimizeLabel; ?>
            </a>
            <span class="diskmanage-about">
                <?php echo $advOptimizeAbout; ?>
            </span>
        </p>
    </div>
    <?php }

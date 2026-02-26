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

// Check used space against quota (percentage)
$inuse = round((($this->dirsize / $this->quota) * 100), 1);
if ($this->total > 0 && $inuse < 1) {
    $inuse = round((($this->dirsize / $this->quota) * 100), 2);
    if ($inuse < 0.1) {
        $inuse = 0.01;
    }
}

$inuse  = ($inuse > 100) ? 100 : $inuse;
$quota  = \Hubzero\Utility\Number::formatBytes($this->quota);
$used   = $this->dirsize ? \Hubzero\Utility\Number::formatBytes($this->dirsize) : 0;
$unused = \Hubzero\Utility\Number::formatBytes($this->quota - $this->dirsize);
$unused = $unused <= 0 ? 'none' : $unused;
$approachingQuota = $this->project->config()->get('approachingQuota', 85);
$approachingQuota = intval($approachingQuota) > 0 ? $approachingQuota : 85;
$warning = ($inuse > $approachingQuota) ? 1 : 0;

$publicationsUrl = Route::url(
    'index.php?option=' . $this->option
    . '&alias=' . $this->project->get('alias')
    . '&active=publications'
);
$diskUsageLabel = Lang::txt(
    'PLG_PROJECTS_PUBLICATIONS_DISK_USAGE'
);

?>
<div id="plg-header">
    <h3 class="publications">
        <a href="<?php echo $publicationsUrl; ?>">
            <?php echo $this->title; ?>
        </a>
        &raquo;
        <span class="subheader">
            <?php echo $diskUsageLabel; ?>
        </span>
    </h3>
</div>
    <div id="disk-usage" <?php if ($warning) {
        echo 'class="quota-warning"';
                         } ?>>
        <div class="disk-usage-wrapper">
            <h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_QUOTA') . ': ' . $quota; ?></h3>
<?php
$usageText = $inuse . '% '
    . Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_USED')
    . ' (' . $used . ' '
    . Lang::txt('PLG_PROJECTS_PUBLICATIONS_OUT_OF')
    . ' ' . $quota . ')';
$quotaMsg = ($inuse == 100)
    ? Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_OVER_QUOTA')
    : Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_APPROACHING_QUOTA');
?>
                <div id="indicator-wrapper">
                    <span id="indicator-area"
                        class="used:<?php echo $inuse; ?>"
                    >&nbsp;</span>
                    <span id="indicator-value">
                        <span><?php echo $usageText; ?></span>
                        <?php if ($warning) { ?>
                            <span class="approaching-quota">
                                - <?php echo $quotaMsg; ?>
                            </span>
                        <?php } ?>
                    </span>
                </div>

<?php
$contentLabel = Lang::txt(
    'PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_CONTENT'
) . ' (' . $used . ')';
$unusedLabel = Lang::txt(
    'PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_UNUSED_SPACE'
) . ' (' . $unused . ')';
?>
                <div id="usage-labels">
                    <span class="l-regular">&nbsp;</span>
                    <?php echo $contentLabel; ?>
                    <span class="l-unused">&nbsp;</span>
                    <?php echo $unusedLabel; ?>
                </div>
        </div>
    </div>

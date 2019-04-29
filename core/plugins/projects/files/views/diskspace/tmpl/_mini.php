<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Check used space against quota (percentage)
$inuse = round(($this->dirsize * 100 ) / $this->quota);
if ($inuse < 1)
{
	$inuse = round((($this->dirsize * 100 ) / $this->quota), 1);
	if ($inuse < 0.1)
	{
		$inuse = 0.0;
	}
}
$inuse = ($inuse > 100) ? 100 : $inuse;
$approachingQuota = $this->config->get('approachingQuota', 85);
$approachingQuota = intval($approachingQuota) > 0 ? $approachingQuota : 85;
$warning 		  = ($inuse > $approachingQuota) ? 1 : 0;

?>
<?php echo Lang::txt('PLG_PROJECTS_FILES_DISK_SPACE'); ?>
<a href="<?php echo $this->url . '/?action=diskspace'; ?>" title="<?php echo Lang::txt('PLG_PROJECTS_FILES_DISK_SPACE_TOOLTIP'); ?>"><span id="indicator-wrapper" <?php if ($warning) { echo 'class="quota-warning"'; } ?>><span id="indicator-area" class="used:<?php echo $inuse; ?>">&nbsp;</span><span id="indicator-value"><span><?php echo $inuse.'% '.Lang::txt('PLG_PROJECTS_FILES_USED'); ?></span></span></span></a>
	 <span class="show-quota"><?php echo Lang::txt('PLG_PROJECTS_FILES_QUOTA') . ': ' . \Hubzero\Utility\Number::formatBytes($this->quota); ?></span>

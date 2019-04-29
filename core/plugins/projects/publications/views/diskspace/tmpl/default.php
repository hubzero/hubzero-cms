<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Check used space against quota (percentage)
$inuse = round((($this->dirsize / $this->quota)*100), 1);
if ($this->total > 0 && $inuse < 1)
{
	$inuse = round((($this->dirsize / $this->quota)*100), 2);
	if ($inuse < 0.1)
	{
		$inuse = 0.01;
	}
}

$inuse 	= ($inuse > 100) ? 100 : $inuse;
$quota 	= \Hubzero\Utility\Number::formatBytes($this->quota);
$used  	= $this->dirsize ? \Hubzero\Utility\Number::formatBytes($this->dirsize) : 0;
$unused = \Hubzero\Utility\Number::formatBytes($this->quota - $this->dirsize);
$unused = $unused <= 0 ? 'none' : $unused;
$approachingQuota = $this->project->config()->get('approachingQuota', 85);
$approachingQuota = intval($approachingQuota) > 0 ? $approachingQuota : 85;
$warning = ($inuse > $approachingQuota) ? 1 : 0;

?>
<div id="plg-header">
	<h3 class="publications"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->get('alias') . '&active=publications'); ?>"><?php echo $this->title; ?></a> &raquo; <span class="subheader"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE'); ?></span></h3>
</div>
	<div id="disk-usage" <?php if ($warning) { echo 'class="quota-warning"'; } ?>>
		<div class="disk-usage-wrapper">
			<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_QUOTA') . ': ' . $quota; ?></h3>
				<div id="indicator-wrapper">
					<span id="indicator-area" class="used:<?php echo $inuse; ?>">&nbsp;</span><span id="indicator-value"><span><?php echo $inuse . '% ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_USED') . ' (' . $used . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_OUT_OF') . ' ' . $quota . ')'; ?></span> <?php if ($warning) { ?><span class="approaching-quota"> - <?php echo ($inuse == 100) ? Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_OVER_QUOTA')  : Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_APPROACHING_QUOTA'); ?></span><?php } ?></span>
				</div>

				<div id="usage-labels">
						<span class="l-regular">&nbsp;</span><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_CONTENT') . ' (' . $used . ')'; ?>
						<span class="l-unused">&nbsp;</span><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_UNUSED_SPACE') . ' (' . $unused . ')'; ?>
				</div>
		</div>
	</div>

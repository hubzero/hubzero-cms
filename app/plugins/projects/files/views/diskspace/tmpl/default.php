<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('diskspace')
     ->js('diskspace');

$minGitSize = 61440;

$usageGit = $this->params->get('disk_usage');

// Check used space against quota (percentage)
$inuse = round((($this->dirsize * 100 )/ $this->quota), 1);
if ($this->total > 0 && $inuse < 1)
{
	$inuse = round((($this->dirsize * 100 )/ $this->quota), 2);
	if ($inuse < 0.1) {
		$inuse = 0.01;
	}
}
$working = ($usageGit || $this->by == 'admin') ? $this->totalspace - $this->dirsize : $this->totalspace;

$actual  = $working > 0 ? round((($working * 100 )/ $this->quota), 1) : null;
$actual  = $actual > 100 ? 100 : $actual;
if ($this->versionTracking == '0')
{
	$versions = 0;
	$working = $this->dirsize;
}
else
{
	$versions = $this->dirsize - $working;
	$versions = ($versions > $minGitSize && ($usageGit || $this->by == 'admin')) ? \Hubzero\Utility\Number::formatBytes($versions) : 0;
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
	<div id="plg-header">
		<h3 class="files"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=files'); ?>"><?php echo $this->title; ?></a> &raquo; <span class="subheader"><?php echo Lang::txt('PLG_PROJECTS_FILES_DISK_USAGE'); ?></span></h3>
	</div>
<?php } ?>
	<div id="disk-usage" <?php if ($warning) { echo 'class="quota-warning"'; } ?>>
		<div class="disk-usage-wrapper">
			<h3><?php echo ($this->by != 'admin') ? Lang::txt('PLG_PROJECTS_FILES_QUOTA').': '.$quota : Lang::txt('PLG_PROJECTS_FILES_DISK_USAGE'); ?></h3>
			<?php if ($this->by != 'admin') { ?>
				<span id="indicator-value"><span><?php echo $inuse . '% '.Lang::txt('PLG_PROJECTS_FILES_USED') . ' (' . $used . ' ' . Lang::txt('COM_PROJECTS_OUT_OF').' ' . $quota . ')'; ?></span> <?php if ($warning) { ?><span class="approaching-quota"> - <?php echo ($inuse == '> 100') ? Lang::txt('PLG_PROJECTS_FILES_OVER_QUOTA')  : Lang::txt('PLG_PROJECTS_FILES_APPROACHING_QUOTA'); ?></span><?php } ?></span>
			<?php } ?>
			<?php if ($this->by != 'admin') { ?>
			<div id="indicator-wrapper">
				<span id="indicator-area" class="used:<?php echo $inuse; ?>">&nbsp;</span>	<?php if ($actual > 0) { ?>
					<span id="actual-area" class="actual:<?php echo $actual; ?>">&nbsp;</span>
					<?php } ?>
			</div>
			<div id="usage-labels">
					<span class="l-actual">&nbsp;</span><?php echo Lang::txt('PLG_PROJECTS_FILES_FILES') . ' ('.\Hubzero\Utility\Number::formatBytes($working) . ')'; ?>
					<?php if ($versions > 0) { ?>
					<span class="l-regular">&nbsp;</span><?php echo $this->by == 'admin' ? Lang::txt('Versions') : Lang::txt('Version History*');
						echo ' (' . $versions . ')';
					} ?>
					<span class="l-unused">&nbsp;</span><?php echo Lang::txt('PLG_PROJECTS_FILES_UNUSED_SPACE').' ('.$unused.')'; ?>
			</div>
			<?php } else { ?>
			<div id="usage-labels" class="usage-admin">
				<span class="l-h"><?php echo Lang::txt('PLG_PROJECTS_FILES_PROJECT_FILES'); ?>
					<span class="l-actual">&nbsp;</span><?php echo Lang::txt('PLG_PROJECTS_FILES_FILES').': '.\Hubzero\Utility\Number::formatBytes($working); ?>
					<span class="l-regular">&nbsp;</span><?php echo Lang::txt('PLG_PROJECTS_FILES_HISTORY') . ': ' . $versions; ?>
					<span class="l-unused">&nbsp;</span><?php echo Lang::txt('PLG_PROJECTS_FILES_AVAILABLE') . ': ' . $unused; ?>
				<span>
				<?php if (isset($this->pubDiskUsage))
				{
					$unusedPub = $this->pubQuota - $this->pubDiskUsage;
					$unusedPub = $unusedPub <= 0 ? 'none' : \Hubzero\Utility\Number::formatBytes($unusedPub);
				?>
				<span class="l-h"><?php echo Lang::txt('PLG_PROJECTS_FILES_PUBLICATIONS'); ?>
					<span class="l-pub">&nbsp;</span><?php echo Lang::txt('PLG_PROJECTS_FILES_SPACE_PUBLISHED') .': ' . \Hubzero\Utility\Number::formatBytes($this->pubDiskUsage); ?>
					<span class="l-unused">&nbsp;</span><?php echo Lang::txt('PLG_PROJECTS_FILES_AVAILABLE') .': ' . $unusedPub; ?>
				<span>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php if ($versions && $this->by != 'admin') { ?>
	<p class="mini faded"><?php echo Lang::txt('PLG_PROJECTS_FILES_ABOUT_HISTORY_SPACE'); ?></p>
	<?php } ?>

	<?php if ($this->by != 'admin' && $this->model->access('manager')
		&& $this->params->get('diskspace_options') && $versions > 0) { ?>
	<div id="disk-manage">
		<h4><?php echo Lang::txt('PLG_PROJECTS_FILES_MANAGE_SPACE'); ?></h4>
		<p class="mini faded"><?php echo Lang::txt('PLG_PROJECTS_FILES_ABOUT_DISK_MANAGE_OPTIONS'); ?></p>
		<p class="disk-manage-option"><a class="btn manage disk-usage-optimize" href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=files&action=optimize'); ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_OPTIMIZE'); ?></a><span class="diskmanage-about"><?php echo Lang::txt('PLG_PROJECTS_FILES_ABOUT_FILE_OPTIMIZE'); ?></span></p>

		<p class="disk-manage-option"><a class="btn manage disk-usage-optimize" href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=files&action=advoptimize'); ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_OPTIMIZE_ADV'); ?></a><span class="diskmanage-about"><?php echo Lang::txt('PLG_PROJECTS_FILES_ABOUT_FILE_OPTIMIZE_ADV'); ?></span></p>
	</div>
<?php }

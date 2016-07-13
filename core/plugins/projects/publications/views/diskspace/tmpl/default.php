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

// Check used space against quota (percentage)
$inuse = round(((($this->dirsize)/ $this->quota)*100), 1);
if ($this->total > 0 && $inuse < 1)
{
	$inuse = round((($this->dirsize * 100 )/ $this->quota*100), 2);
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
					<span id="indicator-area" class="used:<?php echo $inuse; ?>">&nbsp;</span><span id="indicator-value"><span><?php echo $inuse . '% ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_USED') . ' (' . $used . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_OUT_OF') . ' ' . $quota . ')'; ?></span> <?php if ($warning) { ?><span class="approaching-quota"> - <?php echo ($inuse == 100) ? Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_OVER_QUOTA')  : Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_APPROACHING_QUOTA') ; ?></span><?php } ?></span>
				</div>

				<div id="usage-labels">
						<span class="l-regular">&nbsp;</span><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_CONTENT') . ' (' . $used . ')'; ?>
						<span class="l-unused">&nbsp;</span><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_UNUSED_SPACE') . ' (' . $unused . ')'; ?>
				</div>
		</div>
	</div>

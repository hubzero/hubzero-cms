<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juser =& JFactory::getUser();
$jconfig =& JFactory::getConfig();
?>
<div id="content-header">
	<h2><?php echo JText::_('COM_TOOLS_STORAGE'); ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
<?php 
echo $this->monitor; //MwHtml::writeMonitor($this->percentage, '', 0, 0, 0, 0);
if ($this->percentage >= 100) {
	//echo MwHTML::storageQuotaWarning($this->percentage);
}
?>
</div><!-- / #content-header-extra -->

<div class="main section">

<?php if ($this->exceeded) { ?>
		<p class="warning"><?php echo JText::_('COM_TOOLS_ERROR_STORAGE_EXCEEDED'); ?></p>
<?php } ?>

<?php if ($this->output) { ?>
		<p class="passed"><?php echo $this->output; ?></p>
<?php } else if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

		<form action="index.php" method="post" id="hubForm">
			<div class="explaination">
				<p class="help">
					<strong>What does "purge" do to my files?</strong><br />
					The <strong>purge</strong> option is an easy way to free up space on your account.  It goes through the "data" directory where your simulation results are stored, and discards all of the results that have built up since you started using the site, or since your last purge. <a href="/kb/tools/purge">Learn more</a>.
				</p>
			</div>
			<fieldset>	
				<h3>Automatic</h3>
				<div class="group">
					<label>
						Clean up Disk Space
						<select name="degree">
							<option value="default">minimally</option>
							<option value="olderthan1">older than 1 day</option>
							<option value="olderthan7">older than 7 days</option>
							<option value="olderthan30">older than 30 days</option>
							<option value="all">all</option>
						</select>
					</label>
					<label>
						<br />
						<input type="submit" class="option" name="action" value="Purge" /> 
					</label>
				</div>
				<p class="hint"><strong>minimally</strong> means the purge operation will delete the oldest simulation results first, and continue deleting newer and newer results, stopping as soon as you are under quota.</p>
			</fieldset>
			<fieldset>
				<h3>Manual</h3>
				<div class="filebrowser">
					Browse your storage space
					<iframe src="<?php echo JRoute::_('index.php?option='.$this->option.'&task=listfiles&no_html=1'); ?>" name="imgManager" id="imgManager" width="98%" height="180" border="0" frameborder="0"></iframe>
				</div>
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="purge" />
			</fieldset><div class="clear"></div>
		</form>

</div><!-- / .main section -->
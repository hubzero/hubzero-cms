<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juser = JFactory::getUser();
$jconfig = JFactory::getConfig();

$this->css('storage.css');
?>
<header id="content-header">
	<h2><?php echo JText::_('COM_TOOLS_STORAGE'); ?></h2>

	<div id="content-header-extra">
		<?php echo $this->monitor; ?>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<?php if ($this->exceeded) { ?>
		<p class="warning"><?php echo JText::_('COM_TOOLS_ERROR_STORAGE_EXCEEDED'); ?></p>
	<?php } ?>

	<?php if ($this->output) { ?>
		<p class="passed"><?php echo $this->output; ?></p>
	<?php } else if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>

	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=storage'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p class="help">
				<strong>What does "purge" do to my files?</strong><br />
				The <strong>purge</strong> option is an easy way to free up space on your account.  It goes through the "data" directory where your simulation results are stored, and discards all of the results that have built up since you started using the site, or since your last purge. <a href="<?php echo JURI::base(true); ?>/kb/tools/purge">Learn more</a>.
			</p>
		</div>
		<fieldset>
			<legend>Automatic</legend>
			<div class="grid">
				<div class="col span6">
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
				</div>
				<div class="col span6 omega">
					<label>
						<br />
						<input type="submit" class="option" name="action" value="Purge" /> 
					</label>
				</div>
			</div>
			<p class="hint"><strong>minimally</strong> means the purge operation will delete the oldest simulation results first, and continue deleting newer and newer results, stopping as soon as you are under quota.</p>
		</fieldset>
		<fieldset>
			<legend>Manual</legend>
			<div class="filebrowser field-wrap">
				Browse your storage space
				<iframe src="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=filelist&tmpl=component'); ?>" name="filer" id="filer" width="98%" height="300" border="0" frameborder="0"></iframe>
			</div>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="purge" />
		</fieldset><div class="clear"></div>
	</form>
</section><!-- / .main section -->

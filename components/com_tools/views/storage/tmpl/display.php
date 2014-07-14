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
				<strong><?php echo JText::_('COM_TOOLS_STORAGE_WHAT_DOES_PURGE_DO'); ?></strong><br />
				<?php echo JText::_('COM_TOOLS_STORAGE_WHAT_PURGE_DOES'); ?>
			</p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_TOOLS_STORAGE_AUTOMATIC'); ?></legend>
			<div class="grid">
				<div class="col span6">
					<label>
						<?php echo JText::_('COM_TOOLS_STORAGE_CLEAN_UP_DISCK_SPACE'); ?>
						<select name="degree">
							<option value="default"><?php echo JText::_('COM_TOOLS_STORAGE_OPT_MINIMALLY'); ?></option>
							<option value="olderthan1"><?php echo JText::_('COM_TOOLS_STORAGE_OPT_OLDER_DAY'); ?></option>
							<option value="olderthan7"><?php echo JText::_('COM_TOOLS_STORAGE_OPT_OLDER_WEEK'); ?></option>
							<option value="olderthan30"><?php echo JText::_('COM_TOOLS_STORAGE_OPT_OLDER_MONTH'); ?></option>
							<option value="all"><?php echo JText::_('COM_TOOLS_STORAGE_OPT_ALL'); ?></option>
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
			<p class="hint"><?php echo JText::_('COM_TOOLS_STORAGE_AUTOMATIC_HINT'); ?></p>
		</fieldset>
		<fieldset>
			<legend><?php echo JText::_('COM_TOOLS_STORAGE_MANUAL'); ?></legend>
			<div class="filebrowser field-wrap">
				<?php echo JText::_('COM_TOOLS_STORAGE_BROWSE_STORAGE'); ?>
				<iframe src="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=filelist&tmpl=component'); ?>" name="filer" id="filer" width="98%" height="300" border="0" frameborder="0"></iframe>
			</div>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="purge" />
		</fieldset><div class="clear"></div>
	</form>
</section><!-- / .main section -->

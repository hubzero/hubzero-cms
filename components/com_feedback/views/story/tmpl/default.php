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

$jconfig =& JFactory::getConfig();
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
<?php if ($this->verified == 1) { ?>
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_MISSING_FIELDS'); ?></p>
	<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=success_story'); ?>" method="post" id="hubForm" enctype="multipart/form-data">
		<div class="explaination">
			<p><?php echo JText::_('COM_FEEDBACK_STORY_OTHER_OPTIONS'); ?></p>
		</div>
		<fieldset>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="sendstory" />
			<input type="hidden" name="verified" value="<?php echo $this->verified; ?>" />
			<input type="hidden" name="userid" value="<?php echo $this->user['uid']; ?>" id="userid" />
			<input type="hidden" name="useremail" value="<?php echo $this->user['email']; ?>" id="useremail" />

			<h3><?php echo JText::_('COM_FEEDBACK_STORY_YOUR_STORY'); ?></h3>

			<input type="hidden" name="picture" id="picture" value="" />
			<iframe width="100%" height="130" scrolling="no" name="filer" frameborder="0" id="filer" src="index.php?option=<?php echo $this->option; ?>&amp;task=img&amp;no_html=1&amp;id=<?php echo $this->user['uid']; ?>"></iframe>

			<label>
				<?php echo JText::_('COM_FEEDBACK_NAME'); ?> <span class="required"><?php echo JText::_('COM_FEEDBACK_REQUIRED'); ?></span>
				<input type="text" name="fullname" value="<?php echo $this->user['name']; ?>" size="30" id="fullname" />
			</label>

			<label>
				<?php echo JText::_('COM_FEEDBACK_ORGANIZATION'); ?>	
				<input type="text" name="org" value="<?php echo $this->user['org']; ?>" size="30" id="org" />
			</label>

			<label<?php echo ($this->getError() && $this->quote == '') ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::_('COM_FEEDBACK_STORY_DESCRIPTION'); ?>
				<textarea name="quote" rows="50" cols="15"><?php echo $this->quote['long']; ?></textarea>
			</label>
<?php if ($this->getError() && $quote == '') { ?>
			<p class="error"><?php echo JText::_('COM_FEEDBACK_STORY_MISSING_DESCRIPTION'); ?></p>
<?php } ?>

			<label>
				<input type="checkbox" name="publish_ok" value="1" class="option" />
				<?php echo JText::sprintf('COM_FEEDBACK_STORY_AUTHORIZE_QUOTE', $jconfig->getValue('config.sitename'), $jconfig->getValue('config.sitename')); ?>
			</label>
	
			<label>
				<input type="checkbox" name="contact_ok" value="1" class="option" />
				<?php echo JText::sprintf('COM_FEEDBACK_STORY_AUTHORIZE_CONTACT', $jconfig->getValue('config.sitename')); ?>
			</label>
	
		</fieldset><div class="clear"></div>
		<p class="submit"><input type="submit" name="submit" value="<?php echo JText::_('COM_FEEDBACK_SUBMIT'); ?>" /></p>
	</form>
<?php } else { ?>
	<p class="warning"><?php echo JText::_('COM_FEEDBACK_STORY_LOGIN'); ?></p>
	<?php JModuleHelper::renderModule( JModuleHelper::getModule( 'mod_xlogin' ) ); ?>
<?php } ?>
</div><!-- / .main section -->


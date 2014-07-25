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

$this->css()
     ->js();

$jconfig = JFactory::getConfig();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-main main-page btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
				<?php echo JText::_('COM_FEEDBACK_MAIN'); ?>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_MISSING_FIELDS'); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=story'); ?>" method="post" id="hubForm" enctype="multipart/form-data">
		<div class="explaination">
			<p><?php echo JText::_('COM_FEEDBACK_STORY_OTHER_OPTIONS'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_FEEDBACK_STORY_YOUR_STORY'); ?></legend>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="sendstory" />

			<input type="hidden" name="fields[userid]" value="<?php echo $this->row->userid; ?>" id="userid" />
			<input type="hidden" name="fields[useremail]" value="<?php echo $this->row->useremail; ?>" id="useremail" />

			<!-- <input type="hidden" name="fields[picture]" id="picture" value="" />
			<div class="field-wrap">
				<iframe width="100%" height="130" scrolling="no" name="filer" frameborder="0" id="filer" src="index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;tmpl=component&amp;id=<?php echo $this->user->get('uidNumber'); ?>"></iframe>
			</div> -->

			<label for="field-fullname">
				<?php echo JText::_('COM_FEEDBACK_NAME'); ?> <span class="required"><?php echo JText::_('COM_FEEDBACK_REQUIRED'); ?></span>
				<input type="text" name="fields[fullname]" id="field-fullname" value="<?php echo $this->escape($this->row->fullname); ?>" size="30" />
			</label>

			<label for="field-org">
				<?php echo JText::_('COM_FEEDBACK_ORGANIZATION'); ?>
				<input type="text" name="fields[org]" id="field-org" value="<?php echo $this->escape($this->row->org); ?>" size="30" />
			</label>

			<label for="field-files">
				<?php echo JText::_('COM_FEEDBACK_PICTURES'); ?>
				<input type="file" name="files[]" id="field-files" multiple="multiple" />
				<span id="uploadImages"></span>
			</label>

			<label<?php echo ($this->getError() && $this->row->quote == '') ? ' class="fieldWithErrors"' : ''; ?> for="field-quote">
				<?php echo JText::_('COM_FEEDBACK_STORY_DESCRIPTION'); ?>
				<textarea name="fields[quote]" id="field-quote" rows="40" cols="15"><?php echo $this->escape($this->row->quote); ?></textarea>
			</label>
			<?php if ($this->getError() && $this->row->quote == '') { ?>
				<p class="error"><?php echo JText::_('COM_FEEDBACK_STORY_MISSING_DESCRIPTION'); ?></p>
			<?php } ?>

			<label for="field-publish_ok">
				<input type="checkbox" name="fields[publish_ok]" id="field-publish_ok" value="1" class="option"<?php if ($this->row->publish_ok) { echo ' checked="checked"'; } ?> />
				<?php echo JText::sprintf('COM_FEEDBACK_STORY_AUTHORIZE_QUOTE', $jconfig->getValue('config.sitename'), $jconfig->getValue('config.sitename')); ?>
			</label>

			<label for="field-contact_ok">
				<input type="checkbox" name="fields[contact_ok]" id="field-contact_ok" value="1" class="option"<?php if ($this->row->contact_ok) { echo ' checked="checked"'; } ?> />
				<?php echo JText::sprintf('COM_FEEDBACK_STORY_AUTHORIZE_CONTACT', $jconfig->getValue('config.sitename')); ?>
			</label>
		</fieldset><div class="clear"></div>

		<p class="submit">
			<input class="btn btn-success" type="submit" name="submit" value="<?php echo JText::_('COM_FEEDBACK_SUBMIT'); ?>" />

			<a class="btn btn-secondary" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
				<?php echo JText::_('COM_FEEDBACK_CANCEL'); ?>
			</a>
		</p>
	</form>
</section><!-- / .main section -->
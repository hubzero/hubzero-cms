<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_redirect
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
?>

	<fieldset class="batch">
		<legend><?php echo JText::_('COM_REDIRECT_HEADING_UPDATE_LINKS'); ?></legend>

		<div class="col width-70 fltlft">
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_REDIRECT_FIELD_NEW_URL_DESC'); ?>">
				<label for="new_url"><?php echo JText::_('COM_REDIRECT_FIELD_NEW_URL_LABEL'); ?></label>
				<input type="text" name="new_url" id="new_url" value="" size="50" title="<?php echo JText::_('COM_REDIRECT_FIELD_NEW_URL_DESC'); ?>" />
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_REDIRECT_FIELD_COMMENT_DESC'); ?>">
				<label for="comment"><?php echo JText::_('COM_REDIRECT_FIELD_COMMENT_LABEL'); ?></label>
				<input type="text" name="comment" id="comment" value="" size="50" title="<?php echo JText::_('COM_REDIRECT_FIELD_COMMENT_DESC'); ?>" />
			</div>
		</div>
		<div class="col width-30 fltrt">
			<div class="input-wrap">
				<button type="button" onclick="this.form.task.value='links.activate';this.form.submit();"><?php echo JText::_('COM_REDIRECT_BUTTON_UPDATE_LINKS'); ?></button>
			</div>
		</div>
	</fieldset>

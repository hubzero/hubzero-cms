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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//set title
$text = ($this->task == 'edit' ? JText::_('COM_NEWSLETTER_EDIT') : JText::_('COM_NEWSLETTER_NEW'));
JToolBarHelper::title(JText::_('COM_NEWSLETTER_NEWSLETTER_TEMPLATES') . ': ' . $text, 'template.png');

//add toolbar buttons
JToolBarHelper::help('index.php?option=com_help&component=com_newsletter&page=template', true);
JToolBarHelper::spacer();
JToolBarHelper::save();
JToolBarHelper::cancel();
?>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-70 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_TEMPLATES') . ': '. $text; ?></span></legend>

			<div class="input-wrap">
				<label for="field-name"><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_NAME'); ?>:</label><br />
				<input type="text" name="template[name]" id="field-name" value="<?php echo $this->escape($this->template->name); ?>" />
			</div>

			<div class="col width-50 fltlft">
				<div class="input-wrap" data-hint="<?php echo JText::_('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?>">
					<label for="field-primary_title_color"><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_PRIMARY_TITLE_COLOR'); ?>:</label><br />
					<span class="hint"><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?></span>
					<input type="text" name="template[primary_title_color]" id="field-primary_title_color" value="<?php echo $this->escape($this->template->primary_title_color); ?>" />
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap" data-hint="<?php echo JText::_('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?>">
					<label for="field-primary_text_color"><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_PRIMARY_TEXT_COLOR'); ?>:</label><br />
					<span class="hint"><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?></span>
					<input type="text" name="template[primary_text_color]" id="field-primary_text_color" value="<?php echo $this->escape($this->template->primary_text_color); ?>" />
				</div>
			</div>
			<div class="clr"></div>

			<div class="col width-50 fltlft">
				<div class="input-wrap" data-hint="<?php echo JText::_('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?>">
					<label for="field-secondary_title_color"><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_SECONDARY_TITLE_COLOR'); ?>:</label><br />
					<span class="hint"><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?></span>
					<input type="text" name="template[secondary_title_color]" id="field-secondary_title_color" value="<?php echo $this->escape($this->template->secondary_title_color); ?>" />
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap" data-hint="<?php echo JText::_('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?>">
					<label for="field-secondary_text_color"><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_SECONDARY_TEXT_COLOR'); ?>:</label><br />
					<span class="hint"><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?></span>
					<input type="text" name="template[secondary_text_color]" id="field-secondary_text_color" value="<?php echo $this->escape($this->template->secondary_text_color); ?>" />
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<label for="field-template"><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_TEMPLATE') ?>:</label><br />
				<textarea name="template[template]" id="field-template" cols="100" rows="30"><?php echo $this->escape( $this->template->template ); ?></textarea>
			</div>
		</fieldset>
	</div>
	<div class="col width-30 fltrt">
		<?php if ($this->config->get('template_tips')) : ?>
			<span class="hint">
				<?php echo JText::_('COM_NEWSLETTER_TEMPLATE_TIPS') ?><br />
				<a target="_blank" href="<?php echo $this->config->get('template_tips'); ?>"><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_TIPS_HINT'); ?></a>
			</span>
			<br /><br />
		<?php endif; ?>
		<?php if ($this->config->get('template_templates')) : ?>
			<span class="hint">
				<?php echo JText::_('COM_NEWSLETTER_TEMPLATE_EXAMPLES'); ?><br />
				<a target="_blank" href="<?php echo $this->config->get('template_templates'); ?>"><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_EXAMPLES_HINT'); ?></a>
			</span>
			<br /><br />
		<?php endif; ?>
		<span class="hint"><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_PLACEHOLDERS'); ?></span><br />
		<?php echo JText::_('COM_NEWSLETTER_TEMPLATE_PLACEHOLDERS_HINT'); ?>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="template[id]" value="<?php echo $this->template->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
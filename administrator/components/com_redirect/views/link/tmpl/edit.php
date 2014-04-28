<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_redirect
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Include the HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'link.cancel' || document.formvalidator.isValid($('#item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_redirect&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo empty($this->item->id) ? JText::_('COM_REDIRECT_NEW_LINK') : JText::sprintf('COM_REDIRECT_EDIT_LINK', $this->item->id); ?></span></legend>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('old_url'); ?>
				<?php echo $this->form->getInput('old_url'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('new_url'); ?>
				<?php echo $this->form->getInput('new_url'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('comment'); ?>
				<?php echo $this->form->getInput('comment'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?>
			</div>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_REDIRECT_OPTIONS'); ?></span></legend>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?>
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_REDIRECT_DETAILS'); ?></span></legend>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('created_date'); ?>
				<?php echo $this->form->getInput('created_date'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('modified_date'); ?>
				<?php echo $this->form->getInput('modified_date'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('hits'); ?>
				<?php echo $this->form->getInput('hits'); ?>
			</div>
		</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<div class="clr"></div>
</form>

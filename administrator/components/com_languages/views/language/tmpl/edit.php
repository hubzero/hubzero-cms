<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$canDo = LanguagesHelper::getActions();
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'language.cancel' || document.formvalidator.isValid($('#item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_languages&layout=edit&lang_id='.(int) $this->item->lang_id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<?php if ($this->item->lang_id) : ?>
				<legend><span><?php echo JText::sprintf('JGLOBAL_RECORD_NUMBER', $this->item->lang_id); ?></span></legend>
			<?php else : ?>
				<legend><span><?php echo JText::_('COM_LANGUAGES_VIEW_LANGUAGE_EDIT_NEW_TITLE'); ?></span></legend>
			<?php endif; ?>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('title'); ?><br />
				<?php echo $this->form->getInput('title'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('title_native'); ?><br />
				<?php echo $this->form->getInput('title_native'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('sef'); ?><br />
				<?php echo $this->form->getInput('sef'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('image'); ?><br />
				<?php echo $this->form->getInput('image'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('lang_code'); ?><br />
				<?php echo $this->form->getInput('lang_code'); ?>
			</div>

				<?php if ($canDo->get('core.edit.state')) : ?>
					<div class="input-wrap">
						<?php echo $this->form->getLabel('published'); ?><br />
						<?php echo $this->form->getInput('published'); ?>
					</div>
				<?php endif; ?>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('access'); ?><br />
				<?php echo $this->form->getInput('access'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('description'); ?><br />
				<?php echo $this->form->getInput('description'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('lang_id'); ?><br />
				<?php echo $this->form->getInput('lang_id'); ?>
			</div>
		</fieldset>
	</div>
	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'language-sliders-'.$this->item->lang_code, array('useCookie'=>1)); ?>

		<?php echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'metadata'); ?>
			<fieldset class="panelform">
				<?php foreach($this->form->getFieldset('metadata') as $field): ?>
					<?php if (!$field->hidden): ?>
						<div class="input-wrap">
							<?php echo $field->label; ?><br />
							<?php echo $field->input; ?>
						</div>
					<?php else : ?>
						<?php echo $field->input; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</fieldset>

		<?php echo JHtml::_('sliders.panel', JText::_('COM_LANGUAGES_FIELDSET_SITE_NAME_LABEL'), 'site_name'); ?>
			<fieldset class="panelform">
				<?php foreach($this->form->getFieldset('site_name') as $field): ?>
					<?php if (!$field->hidden): ?>
						<div class="input-wrap">
							<?php echo $field->label; ?><br />
							<?php echo $field->input; ?>
						</div>
					<?php else : ?>
						<?php echo $field->input; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</fieldset>

		<?php echo JHtml::_('sliders.end'); ?>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<div class="clr"> </div>
</form>

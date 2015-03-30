<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_newsfeeds
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
		if (task == 'newsfeed.cancel' || document.formvalidator.isValid($('#item-form'))) {
			Joomla.submitform(task, document.getElementById('newsfeed-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_newsfeeds&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_NEWSFEEDS_NEW_NEWSFEED') : JText::sprintf('COM_NEWSFEEDS_EDIT_NEWSFEED', $this->item->id); ?></legend>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('alias'); ?>
				<?php echo $this->form->getInput('alias'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('link'); ?>
				<?php echo $this->form->getInput('link'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('catid'); ?>
				<?php echo $this->form->getInput('catid'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('access'); ?>
				<?php echo $this->form->getInput('access'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('ordering'); ?>
				<?php echo $this->form->getInput('ordering'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('language'); ?>
				<?php echo $this->form->getInput('language'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?>
			</div>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'newsfeed-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

			<?php echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'publishing-details'); ?>

			<fieldset class="panelform">

				<div class="input-wrap">
					<?php echo $this->form->getLabel('created_by'); ?>
					<?php echo $this->form->getInput('created_by'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('created_by_alias'); ?>
					<?php echo $this->form->getInput('created_by_alias'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('created'); ?>
					<?php echo $this->form->getInput('created'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('publish_up'); ?>
					<?php echo $this->form->getInput('publish_up'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('publish_down'); ?>
					<?php echo $this->form->getInput('publish_down'); ?>
				</div>

				<?php if ($this->item->modified_by) : ?>
					<div class="input-wrap">
						<?php echo $this->form->getLabel('modified_by'); ?>
						<?php echo $this->form->getInput('modified_by'); ?>
					</div>

					<div class="input-wrap">
						<?php echo $this->form->getLabel('modified'); ?>
						<?php echo $this->form->getInput('modified'); ?>
					</div>
				<?php endif; ?>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('numarticles'); ?>
					<?php echo $this->form->getInput('numarticles'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('cache_time'); ?>
					<?php echo $this->form->getInput('cache_time'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('rtl'); ?>
					<?php echo $this->form->getInput('rtl'); ?>
				</div>

				<?php //echo $this->form->getLabel('xreference'); // Missing from schema! ?>
				<?php //echo $this->form->getInput('xreference'); ?>
			</ul>
			</fieldset>

			<?php echo $this->loadTemplate('params'); ?>

			<?php echo $this->loadTemplate('metadata'); ?>

		<?php echo JHtml::_('sliders.end'); ?>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>

	<div class="clr"></div>
</form>

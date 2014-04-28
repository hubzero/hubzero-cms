<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die; ?>

<?php echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'publishing-details'); ?>

	<fieldset class="panelform">
		<div class="input-wrap">
			<?php echo $this->form->getLabel('created_user_id'); ?>
			<?php echo $this->form->getInput('created_user_id'); ?>
		</div>

		<?php if (intval($this->item->created_time)) : ?>
			<div class="input-wrap">
				<?php echo $this->form->getLabel('created_time'); ?>
				<?php echo $this->form->getInput('created_time'); ?>
			</div>
		<?php endif; ?>

		<?php if ($this->item->modified_user_id) : ?>
			<div class="input-wrap">
				<?php echo $this->form->getLabel('modified_user_id'); ?>
				<?php echo $this->form->getInput('modified_user_id'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('modified_time'); ?>
				<?php echo $this->form->getInput('modified_time'); ?>
			</div>
		<?php endif; ?>
	</fieldset>

<?php $fieldSets = $this->form->getFieldsets('params');

foreach ($fieldSets as $name => $fieldSet) :
	$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_CATEGORIES_'.$name.'_FIELDSET_LABEL';
	echo JHtml::_('sliders.panel', JText::_($label), $name.'-options');
	if (isset($fieldSet->description) && trim($fieldSet->description)) :
		echo '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
	endif;
	?>
	<fieldset class="panelform">
		<?php foreach ($this->form->getFieldset($name) as $field) : ?>
			<div class="input-wrap">
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
			</div>
		<?php endforeach; ?>

		<?php if ($name=='basic'):?>
			<div class="input-wrap">
				<?php echo $this->form->getLabel('note'); ?>
				<?php echo $this->form->getInput('note'); ?>
			</div>
		<?php endif;?>
	</fieldset>
<?php endforeach; ?>

<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die(); ?>


<?php $fieldSets = $this->form->getFieldsets('params'); ?>

<?php foreach ($fieldSets as $name => $fieldSet): ?>

	<?php $label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_CATEGORIES_'.$name.'_FIELDSET_LABEL'; ?>
	<?php echo Html::sliders('panel', Lang::txt($label), $name.'-options'); ?>
	<p class="hasTip">
		<?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
			<?php echo $this->escape(Lang::txt($fieldSet->description)); ?></p>
		<?php endif;?>
	</p>
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

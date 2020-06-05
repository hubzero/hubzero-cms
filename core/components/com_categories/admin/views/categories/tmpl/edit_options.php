<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>

<?php $fieldSets = $this->form->getFieldsets('params'); ?>

<?php foreach ($fieldSets as $name => $fieldSet): ?>

	<?php $label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_CATEGORIES_'.$name.'_FIELDSET_LABEL'; ?>
	<?php echo Html::sliders('panel', Lang::txt($label), $name.'-options'); ?>

	<?php if (isset($fieldSet->description) && trim($fieldSet->description)): ?>
		<p><?php echo $this->escape(Lang::txt($fieldSet->description)); ?></p>
	<?php endif;?>

	<fieldset class="panelform">
		<?php foreach ($this->form->getFieldset($name) as $field): ?>
			<div class="input-wrap">
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
			</div>
		<?php endforeach; ?>

		<?php if ($name == 'basic'): ?>
			<div class="input-wrap">
				<?php echo $this->form->getLabel('note'); ?>
				<?php echo $this->form->getInput('note'); ?>
			</div>
		<?php endif; ?>
	</fieldset>
<?php endforeach;

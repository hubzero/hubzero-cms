<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();
?>
<?php
	$fieldSets = $this->form->getFieldsets('request');

	if (!empty($fieldSets)) {
		$fieldSet = array_shift($fieldSets);
		$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$fieldSet->name.'_FIELDSET_LABEL';
		echo Html::sliders('panel', Lang::txt($label), 'request-options');
		if (isset($fieldSet->description) && trim($fieldSet->description)) :
			echo '<p class="tip">'.$this->escape(Lang::txt($fieldSet->description)).'</p>';
		endif;
	?>
		<fieldset class="panelform">
			<?php $hidden_fields = ''; ?>

			<?php foreach ($this->form->getFieldset('request') as $field) : ?>
				<?php if (!$field->hidden) : ?>
				<div class="input-wrap">
					<?php echo $field->label; ?>
					<?php echo $field->input; ?>
				</div>
				<?php else : $hidden_fields .= $field->input; ?>
				<?php endif; ?>
			<?php endforeach; ?>

			<?php echo $hidden_fields; ?>
		</fieldset>
<?php
	}

	$fieldSets = $this->form->getFieldsets('params');

	foreach ($fieldSets as $name => $fieldSet) :
		$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$name.'_FIELDSET_LABEL';
		echo Html::sliders('panel', Lang::txt($label), $name.'-options');
			if (isset($fieldSet->description) && trim($fieldSet->description)) :
				echo '<p class="tip">'.$this->escape(Lang::txt($fieldSet->description)).'</p>';
			endif;
			?>
		<div class="clr"></div>
		<fieldset class="panelform">
				<?php foreach ($this->form->getFieldset($name) as $field) : ?>
					<div class="input-wrap">
						<?php echo $field->label; ?>
						<?php echo $field->input; ?>
					</div>
				<?php endforeach; ?>
		</fieldset>
<?php endforeach;?>
<?php

	$fieldSets = $this->form->getFieldsets('associations');

	foreach ($fieldSets as $name => $fieldSet) :
		$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$name.'_FIELDSET_LABEL';
		echo Html::sliders('panel', Lang::txt($label), $name.'-options');
			if (isset($fieldSet->description) && trim($fieldSet->description)) :
				echo '<p class="tip">'.$this->escape(Lang::txt($fieldSet->description)).'</p>';
			endif;
			?>
		<div class="clr"></div>
		<fieldset class="panelform">
				<?php foreach ($this->form->getFieldset($name) as $field) : ?>
					<div class="input-wrap">
						<?php echo $field->label; ?>
						<?php echo $field->input; ?>
					</div>
				<?php endforeach; ?>
		</fieldset>
<?php endforeach;
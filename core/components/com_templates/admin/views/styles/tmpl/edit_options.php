<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

	$fieldSets = $this->form->getFieldsets('params');

	$k = 0;
	foreach ($fieldSets as $name => $fieldSet) :
		$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_TEMPLATES_' . $name . '_FIELDSET_LABEL';
		echo Html::sliders('panel', Lang::txt($label), $name . '-options');

			if (isset($fieldSet->description) && trim($fieldSet->description)) :
				echo '<p class="tip">' . $this->escape(Lang::txt($fieldSet->description)) . '</p>';
			endif;
		$k++;
			?>
		<fieldset class="panelform">
			<?php foreach ($this->form->getFieldset($name) as $field) : ?>
				<div class="input-wrap">
					<?php if (!$field->hidden) : ?>
						<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
				</div>
			<?php endforeach; ?>
		</fieldset>
	<?php endforeach; ?>
	<?php if (!$k) { ?>
		<p class="warning"><?php echo Lang::txt('No options found for this template.'); ?></p>
	<?php } 
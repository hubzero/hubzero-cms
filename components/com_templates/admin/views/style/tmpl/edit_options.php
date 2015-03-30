<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

	$fieldSets = $this->form->getFieldsets('params');

	$k = 0;
	foreach ($fieldSets as $name => $fieldSet) :
		$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_TEMPLATES_'.$name.'_FIELDSET_LABEL';
		echo JHtml::_('sliders.panel', JText::_($label), $name.'-options');

			if (isset($fieldSet->description) && trim($fieldSet->description)) :
				echo '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
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
	<?php endforeach;  ?>
	<?php if (!$k) { ?>
		<p class="warning"><?php echo JText::_('No options found for this template.'); ?></p>
	<?php } ?>

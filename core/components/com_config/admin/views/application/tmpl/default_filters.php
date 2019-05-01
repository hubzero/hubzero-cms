<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div class="width-100">
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_CONFIG_TEXT_FILTER_SETTINGS'); ?></span></legend>

		<p><?php echo Lang::txt('COM_CONFIG_TEXT_FILTERS_DESC'); ?></p>

		<?php foreach ($this->form->getFieldset('filters') as $field): ?>
			<?php //echo $field->label; ?>
			<?php echo $field->input; ?>
		<?php endforeach; ?>
	</fieldset>
</div>

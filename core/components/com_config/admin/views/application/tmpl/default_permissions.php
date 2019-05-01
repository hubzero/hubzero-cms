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
	<fieldset>
		<legend class="hidden"><span><?php echo Lang::txt('COM_CONFIG_PERMISSION_SETTINGS'); ?></span></legend>

		<?php foreach ($this->form->getFieldset('permissions') as $field): ?>
			<?php //echo $field->label; ?>
			<?php echo $field->input; ?>
		<?php endforeach; ?>
	</fieldset>
</div>

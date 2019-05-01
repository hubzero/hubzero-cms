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
		<legend><span><?php echo Lang::txt('COM_CONFIG_SESSION_SETTINGS'); ?></span></legend>

		<?php
		foreach ($this->form->getFieldset('session') as $field):
		?>
			<div class="input-wrap">
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
			</div>
		<?php
		endforeach;
		?>
	</fieldset>
</div>

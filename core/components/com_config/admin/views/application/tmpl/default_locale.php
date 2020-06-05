<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div class="width-100">
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_CONFIG_LOCATION_SETTINGS'); ?></span></legend>

		<?php
		foreach ($this->form->getFieldset('locale') as $field):
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

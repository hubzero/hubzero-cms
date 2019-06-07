<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div class="grid">
	<div class="col span6">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_CONFIG_API_RATELIMIT_SHORT'); ?></span></legend>

			<?php
			foreach ($this->form->getFieldset('rl_short') as $field):
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
	<div class="col span6">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_CONFIG_API_RATELIMIT_LONG'); ?></span></legend>

			<?php
			foreach ($this->form->getFieldset('rl_long') as $field):
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
</div>

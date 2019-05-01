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
		<legend><span><?php echo Lang::txt('COM_CONFIG_OTHER_SETTINGS', $this->section); ?></span></legend>

		<?php
		foreach ($this->values as $key => $val):
			if (is_array($val)):
				foreach ($val as $k => $v):
					?>
					<div class="input-wrap">
						<label for="hzform_<?php echo $this->section; ?>_<?php echo $key; ?>_<?php echo $k; ?>"><?php echo $key; ?></label>
						<input type="text" name="hzother[<?php echo $this->section; ?>][<?php echo $key; ?>][<?php echo $k; ?>]" id="hzform_<?php echo $this->section; ?>_<?php echo $key; ?>_<?php echo $k; ?>" value="<?php echo $this->escape($v); ?>" />
					</div>
					<?php
				endforeach;
			else:
				?>
				<div class="input-wrap">
					<label for="hzform_<?php echo $this->section; ?>_<?php echo $key; ?>"><?php echo $key; ?></label>
					<input type="text" name="hzother[<?php echo $this->section; ?>][<?php echo $key; ?>]" id="hzform_<?php echo $this->section; ?>_<?php echo $key; ?>" value="<?php echo $this->escape($val); ?>" />
				</div>
				<?php
			endif;
		endforeach;
		?>
	</fieldset>
</div>

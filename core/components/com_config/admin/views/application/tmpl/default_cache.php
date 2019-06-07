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
		<legend><span><?php echo Lang::txt('COM_CONFIG_CACHE_SETTINGS'); ?></span></legend>

		<?php
		foreach ($this->form->getFieldset('cache') as $field):
		?>
			<div class="input-wrap">
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
			</div>
		<?php
		endforeach;
		?>

		<?php if (isset($this->data['cache']['cache_handler']) && $this->data['cache']['cache_handler'] == 'memcache' || $this->data['session']['session_handler'] == 'memcache') : ?>
			<?php
			foreach ($this->form->getFieldset('memcache') as $mfield):
			?>
				<div class="input-wrap">
					<?php echo $mfield->label; ?>
					<?php echo $mfield->input; ?>
				</div>
			<?php
			endforeach;
			?>
		<?php endif; ?>
	</fieldset>
</div>

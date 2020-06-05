<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();
?>
	<div class="input-wrap">
		<?php echo $this->form->getLabel('metadesc'); ?>
		<?php echo $this->form->getInput('metadesc'); ?>
	</div>

	<div class="input-wrap">
		<?php echo $this->form->getLabel('metakey'); ?>
		<?php echo $this->form->getInput('metakey'); ?>
	</div>

<?php foreach ($this->form->getGroup('metadata') as $field): ?>
	<div class="input-wrap">
		<?php if (!$field->hidden): ?>
			<?php echo $field->label; ?>
		<?php endif; ?>
		<?php echo $field->input; ?>
	</div>
<?php endforeach; 
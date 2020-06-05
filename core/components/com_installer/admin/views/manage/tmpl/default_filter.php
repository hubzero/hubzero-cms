<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

?>
<fieldset id="filter-bar">
	<div class="grid">
		<div class="filter-search col span4">
			<?php foreach ($this->form->getFieldSet('search') as $field): ?>
				<?php if (!$field->hidden): ?>
					<?php echo $field->label; ?>
				<?php endif; ?>
				<?php echo $field->input; ?>
			<?php endforeach; ?>
		</div>
		<div class="filter-select col span8">
			<?php foreach ($this->form->getFieldSet('select') as $field): ?>
				<?php if (!$field->hidden): ?>
					<?php echo $field->label; ?>
				<?php endif; ?>
				<?php echo $field->input; ?>
			<?php endforeach; ?>
		</div>
	</div>
</fieldset>

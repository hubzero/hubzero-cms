<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div class="file-preview markdown">
	<?php if ($this->getError()): ?>
		<div class="error">
			<?php echo $this->escape($this->getError()); ?>
		</div>
	<?php endif; ?>

	<div class="file-preview-rendered">
		<?php echo \Hubzero\Utility\Sanitize::html($this->rendered); ?>
	</div>
</div>

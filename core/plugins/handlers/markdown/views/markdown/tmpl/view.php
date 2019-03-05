<?php
/**
 * @package   hubzero-cms
 * @copyright Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div class="file-preview markdown">
	<?php if ($this->getError()): ?>
		<div class="error">
			<?php echo $this->getError(); ?>
		</div>
	<?php endif; ?>

	<div class="file-preview-rendered">
		<?php echo $this->rendered; ?>
	</div>
</div>

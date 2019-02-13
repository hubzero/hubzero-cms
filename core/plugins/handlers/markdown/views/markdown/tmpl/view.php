<?php
/**
 * @package   hubzero-cms
 * @copyright Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<?php if ($this->getError()): ?>
	<div class="error">
		<?php echo $this->getError(); ?>
	</div>
<?php endif; ?>

<div class="file-preview">
	<div class="file-preview-rendered markdown">
		<?php echo $this->rendered; ?>
	</div>
</div>

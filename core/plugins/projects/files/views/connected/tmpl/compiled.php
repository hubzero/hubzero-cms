<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<?php if ($this->getErrors()) : ?>
	<div class="error width-content">
		<?php echo implode("\n", $this->getErrors()); ?>
	</div>
<?php else : ?>
	<div id="abox-content">
		<?php echo $this->handler->loadTemplate(); ?>
	</div>
<?php endif; ?>
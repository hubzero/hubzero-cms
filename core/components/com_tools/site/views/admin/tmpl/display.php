<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>
<div id="output">
<?php if ($this->getError()) { ?>
	<p class="error">
		<strong><?php echo Lang::txt('COM_TOOLS_NOTICE_PROBLEMS'); ?></strong><br />
		<?php echo implode('<br />* ', $this->getErrors()); ?>
	</p>
<?php } ?>
<?php if ($this->messages && !empty($this->messages)) { ?>
	<p class="passed">
		<strong><?php echo Lang::txt('COM_TOOLS_NOTICE_OK_ACTIONS'); ?></strong><br />
		<?php echo implode('<br />* ', $this->messages); ?>
	</p>
<?php } ?>
</div>
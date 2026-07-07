<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<section class="main section">
	<div class="section-inner">
		<h2><?php echo Lang::txt('COM_MASSMAIL_UNSUBSCRIBE_TITLE'); ?></h2>
		<?php if ($this->ok) { ?>
			<p class="passed"><?php echo Lang::txt('COM_MASSMAIL_UNSUBSCRIBE_OK', $this->escape($this->email)); ?></p>
		<?php } else { ?>
			<p class="error"><?php echo Lang::txt('COM_MASSMAIL_UNSUBSCRIBE_INVALID'); ?></p>
		<?php } ?>
	</div>
</section>

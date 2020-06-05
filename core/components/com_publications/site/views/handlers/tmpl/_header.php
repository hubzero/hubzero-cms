<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
// no direct access
defined('_HZEXEC_') or die();

?>
<div class="handler-controls">
	<h6><?php echo Lang::txt('COM_PUBLICATIONS_HANDLER_CHOICE'); ?>
		<a class="pub-info-pop more-content" title="Click to learn more" href="#handler-hint"></a>
	</h6>
	<div class="hidden">
		<div id="handler-hint" class="full-content"><?php echo Lang::txt('COM_PUBLICATIONS_HANDLER_CHOICE_HINT'); ?></div>
	</div>
</div>
<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$allowupload = ($this->version=='current' or !$this->status['published']) ? 1 : 0;
?>
	<div class="explaination">
		<h4><?php echo Lang::txt('COM_TOOLS_ATTACH_WHAT_ARE_ATTACHMENTS'); ?></h4>
		<p><?php echo Lang::txt('COM_TOOLS_ATTACH_EXPLANATION'); ?></p>
	</div>
	<fieldset>
		<legend><?php echo Lang::txt('COM_TOOLS_ATTACH_ATTACHMENTS'); ?></legend>
		<div class="field-wrap">
			<iframe width="100%" height="200" frameborder="0" name="attaches" id="attaches" src="index.php?option=<?php echo $this->option; ?>&amp;controller=attachments&amp;rid=<?php echo $this->row->id; ?>&amp;tmpl=component&amp;type=7&amp;allowupload=<?php echo $allowupload; ?>"></iframe>
		</div>
	</fieldset><div class="clear"></div>

	<div class="explaination">
		<h4><?php echo Lang::txt('COM_TOOLS_ATTACH_WHAT_ARE_SCREENSHOTS'); ?></h4>
		<p><?php echo Lang::txt('COM_TOOLS_ATTACH_SCREENSHOTS_EXPLANATION'); ?></p>
	</div>
	<fieldset>
		<legend><?php echo Lang::txt('COM_TOOLS_ATTACH_SCREENSHOTS'); ?></legend>
		<div class="field-wrap">
			<iframe width="100%" height="400" frameborder="0" name="screens" id="screens" src="index.php?option=<?php echo $this->option; ?>&amp;controller=screenshots&amp;rid=<?php echo $this->row->id; ?>&amp;tmpl=component&amp;version=<?php echo $this->version; ?>"></iframe>
		</div>
	</fieldset><div class="clear"></div>
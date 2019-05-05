<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>
<div class="explaination">
	<h4><?php echo Lang::txt('COM_TOOLS_AUTHORS_NO_LOGIN'); ?></h4>
	<p><?php echo Lang::txt('COM_TOOLS_AUTHORS_NO_LOGIN_EXPLANATION'); ?></p>
</div>
<fieldset>
	<legend><?php echo Lang::txt('COM_TOOLS_AUTHORS_AUTHORS'); ?></legend>
	<div class="field-wrap">
		<iframe name="authors" id="authors" src="index.php?option=<?php echo $this->option; ?>&amp;controller=authors&amp;rid=<?php echo $this->row->id; ?>&amp;tmpl=component&amp;version=<?php echo $this->version; ?>" width="100%" height="400" frameborder="0"></iframe>
	</div>
</fieldset><div class="clear"></div>
<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>
	<li id="acluser_<?php echo $this->id; ?>">
		<a class="state trash" data-parent="acluser_<?php echo $this->id; ?>" href="#" onclick="HUB.ResourcesACL.removeUser('acluser_<?php echo $this->id; ?>');return false;"><span><?php echo Lang::txt('JACTION_DELETE'); ?></span></a>
		<?php echo $this->escape(stripslashes($this->name)); ?> (<?php echo $this->id; ?>)
		<input type="hidden" class="acluserid" name="<?php echo $this->id; ?>acluserid" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="<?php echo $this->id; ?>_name" value="<?php echo $this->escape(stripslashes($this->name)); ?>" />
	</li>

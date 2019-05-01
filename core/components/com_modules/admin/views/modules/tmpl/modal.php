<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>
<div class="configuration">
	<div class="configuration-options">
		<button type="button" onclick="Hubzero.submitbutton('save');"><?php echo Lang::txt('JSAVE');?></button>
		<button type="button" onclick="window.parent.$.fancybox.close();"><?php echo Lang::txt('JCANCEL');?></button>
	</div>
	<?php echo Lang::txt('COM_MODULES_MANAGER_MODULE', Lang::txt($this->item->module)) ?>
</div>

<?php
$this->setLayout('edit');
echo $this->loadTemplate();

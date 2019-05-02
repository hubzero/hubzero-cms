<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<fieldset class="adminform">
	<legend><?php echo Lang::txt('COM_SYSTEM_INFO_PHP_INFORMATION'); ?></legend>
	<?php echo $this->php_info; ?>
</fieldset>

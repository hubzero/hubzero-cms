<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div class="group-login">
	<h2><?php echo Lang::txt('COM_GROUPS_VIEW_LOGIN'); ?></h2>
	<?php echo \Hubzero\Module\Helper::renderModule('mod_login'); ?>
</div>
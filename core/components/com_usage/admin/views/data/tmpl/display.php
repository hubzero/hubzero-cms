<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_USAGE'), 'usage.png');
Toolbar::preferences('com_usage', '550');
?>

<form action="<?php echo Route::url('index.php?option=com_usage'); ?>" name="adminForm" id="item-form">
	<p class="warning"><?php echo Lang::txt('COM_USAGE_WARNING'); ?></p>
</form>
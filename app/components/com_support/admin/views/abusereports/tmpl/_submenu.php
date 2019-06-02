<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$task = Request::getCmd('task');
?>

<div role="navigation" class="sub sub-navigation">
	<ul id="subsubmenu">
		<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"<?php if ($task != 'check') { echo ' class="active"'; } ?>><?php echo Lang::txt('COM_SUPPORT_ABUSE_REPORTS'); ?></a></li>
		<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=check'); ?>"<?php if ($task == 'check') { echo ' class="active"'; } ?>><?php echo Lang::txt('COM_SUPPORT_ABUSE_CHECK'); ?></a></li>
	</ul>
</div>
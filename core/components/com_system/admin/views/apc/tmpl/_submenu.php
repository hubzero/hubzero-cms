<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$task = Request::getCmd('task');
?>

<div role="navigation" class="sub sub-navigation">
	<ul id="subsubmenu">
		<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"<?php if (!$task || $task == 'host') { echo ' class="active"'; } ?>><?php echo Lang::txt('COM_SYSTEM_APC_MENU_HOST'); ?></a></li>
		<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=system'); ?>"<?php if ($task == 'system') { echo ' class="active"'; } ?>><?php echo Lang::txt('COM_SYSTEM_APC_MENU_SYSTEM'); ?></a></li>
		<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=user'); ?>"<?php if ($task == 'user') { echo ' class="active"'; } ?>><?php echo Lang::txt('COM_SYSTEM_APC_MENU_USER'); ?></a></li>
		<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=dircache'); ?>"<?php if ($task == 'dircache') { echo ' class="active"'; } ?>><?php echo Lang::txt('COM_SYSTEM_APC_MENU_DIR'); ?></a></li>
		<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=version'); ?>"<?php if ($task == 'version') { echo ' class="active"'; } ?>><?php echo Lang::txt('COM_SYSTEM_APC_MENU_VERSION'); ?></a></li>
	</ul>
</div>
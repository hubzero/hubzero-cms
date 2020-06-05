<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$task = strtolower(Request::getCmd('task', ''));
?>
<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li><a<?php if (!$task) { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"><?php echo Lang::txt('COM_TOOLS_WINDOWS_APPS'); ?></a></li>
		<li><a<?php if ($task == 'sessions') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=sessions'); ?>"><?php echo Lang::txt('COM_TOOLS_WINDOWS_SESSIONS'); ?></a></li>
		<li><a<?php if ($task == 'usage') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=usage'); ?>"><?php echo Lang::txt('COM_TOOLS_WINDOWS_USAGE'); ?></a></li>
	</ul>
</nav><!-- / .sub-navigation -->

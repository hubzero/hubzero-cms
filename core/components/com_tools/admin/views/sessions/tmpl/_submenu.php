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
		<li><a<?php if (!$task) { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"><?php echo Lang::txt('COM_TOOLS_SESSION_ACTIVE'); ?></a></li>
		<li><a<?php if ($task == 'classes') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=classes'); ?>"><?php echo Lang::txt('COM_TOOLS_SESSION_CLASSES'); ?></a></li>
	</ul>
</nav><!-- / .sub-navigation -->
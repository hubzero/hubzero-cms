<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$task = strtolower(Request::getCmd('task', ''));
?>
<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li><a<?php if (!$task) { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"><?php echo Lang::txt('COM_MEMBERS_QUOTAS'); ?></a></li>
		<li><a<?php if ($task == 'displayclasses') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=displayClasses'); ?>"><?php echo Lang::txt('COM_MEMBERS_QUOTA_CLASSES'); ?></a></li>
		<li><a<?php if ($task == 'import') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=import'); ?>"><?php echo Lang::txt('COM_MEMBERS_QUOTAS_IMPORT'); ?></a></li>
	</ul>
</nav><!-- / .sub-navigation -->
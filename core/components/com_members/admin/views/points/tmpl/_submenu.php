<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$task = Request::getCmd('task', '');
?>
<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li><a<?php if (!$task) { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>">Summary</a></li>
		<li><a<?php if ($task == 'edit') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit'); ?>">Look up User Balance</a></li>
		<li><a<?php if ($task == 'config') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=config'); ?>">Configuration</a></li>
		<li><a<?php if ($task == 'batch') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=batch'); ?>">Batch Transaction</a></li>
	</ul>
</nav><!-- / .sub-navigation -->
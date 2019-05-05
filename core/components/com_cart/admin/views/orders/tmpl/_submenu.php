<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$task = strtolower(Request::getCmd('task', ''));
?>
<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li><a<?php if (!$task || $task == 'display') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"><?php echo Lang::txt('COM_CART_OREDERS_REPORT_ALL'); ?></a></li>
		<li><a<?php if ($task == 'items') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=items'); ?>"><?php echo Lang::txt('COM_CART_ORDER_ITEMS_REPORT'); ?></a></li>
	</ul>
</nav><!-- / .sub-navigation -->
<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_WINDOWS'), 'tools');
Toolbar::help('windows');

Html::behavior('tooltip');
?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">

	<p class="warning"><?php echo Lang::txt('COM_TOOLS_WINDOWS_CONFIGURATION_REQUIRED'); ?></p>

</form>
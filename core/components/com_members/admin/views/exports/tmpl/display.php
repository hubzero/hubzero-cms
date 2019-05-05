<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_MENU_EXPORT'), 'export.png');

?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span4">
			<a class='permissions button' href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=run&delimiter=,'); ?>">Download CSV of all users</a>
		</div>
	</div>

	<?php echo Html::input('token'); ?>
</form>

<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<div class="admin-header">
	<a class="icon-add button push-module" href="<?php echo Route::url('index.php?option=com_members&controller=plugins&task=manage&plugin=dashboard&action=push'); ?>">
		<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_TITLE'); ?>
	</a>
	<a class="icon-add button add-module" href="<?php echo Route::url('index.php?option=com_members&controller=plugins&task=manage&plugin=dashboard&action=add'); ?>">
		<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES'); ?>
	</a>
	<h3>
		<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_MANAGE'); ?>
	</h3>
</div>

<div class="member_dashboard">

	<div class="modules customizable">
		<?php
			foreach ($this->modules as $module)
			{
				// create view object
				$this->view('module', 'display')
				     ->set('admin', $this->admin)
				     ->set('module', $module)
				     ->display();
			}
		?>
	</div>

	<div class="modules-empty">
		<h3><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADMIN_EMPTY_TITLE'); ?></h3>
		<p><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADMIN_EMPTY_DESC'); ?></p>
	</div>
</div>
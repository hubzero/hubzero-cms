<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SYSTEM_INFO'), 'systeminfo');
Toolbar::help('sysinfo');

// Add specific helper files for html generation
Html::addIncludePath(dirname(PATH_COMPONENT) . '/helpers/html');

// Load switcher behavior
Html::behavior('switcher', 'submenu');

Document::setBuffer($this->loadTemplate('navigation'), 'modules', 'submenu');

$this->css('
.writable {
	color: green;
}
.unwritable {
	color: red;
}
');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<div id="config-document">
		<div id="page-site" class="tab">
			<div class="noshow">
				<div class="width-100">
					<?php echo $this->loadTemplate('system'); ?>
				</div>
			</div>
		</div>

		<div id="page-phpsettings" class="tab">
			<div class="noshow">
				<div class="width-100">
					<?php echo $this->loadTemplate('phpsettings'); ?>
				</div>
			</div>
		</div>

		<div id="page-config" class="tab">
			<div class="noshow">
				<div class="width-100">
					<?php echo $this->loadTemplate('config'); ?>
				</div>
			</div>
		</div>

		<div id="page-directory" class="tab">
			<div class="noshow">
				<div class="width-100">
					<?php echo $this->loadTemplate('directory'); ?>
				</div>
			</div>
		</div>

		<div id="page-phpinfo" class="tab">
			<div class="noshow">
				<div class="width-100">
					<?php echo $this->loadTemplate('phpinfo'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="clr"></div>
</form>

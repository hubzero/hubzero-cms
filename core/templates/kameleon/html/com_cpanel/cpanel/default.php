<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Set toolbar items for the page
Toolbar::title(Lang::txt('COM_CPANEL'), 'cpanel');
Toolbar::help('cpanel');

$attr = array('style' => 'cpanel');
?>
<div class="hero width-100">
	<?php
	foreach (Module::byPosition('cpanelhero') as $module):
		echo Module::render($module, $attr);
	endforeach;
	?>
</div>
<div class="cpanel-wrap">
	<div class="cpanel col width-48 fltlft">
		<?php
		foreach (Module::byPosition('icon') as $module):
			echo Module::render($module, $attr);
		endforeach;
		?>
	</div>
	<div class="cpanel col width-48 fltrt">
		<?php
		foreach (Module::byPosition('cpanel') as $module):
			echo Module::render($module, $attr);
		endforeach;
		?>
	</div>
</div>

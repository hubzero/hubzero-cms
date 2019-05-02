<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_LOCATIONS'), 'tools');
Toolbar::addNew();
Toolbar::spacer();
Toolbar::apply();
Toolbar::save();
Toolbar::cancel();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">

	<nav role="navigation" class="sub-navigation">
		<div id="submenu-box">
			<div class="submenu-box">
				<div class="submenu-pad">
					<ul id="submenu" class="member">
						<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=zones&task=edit&id=' . $this->row->id); ?>" id="profile"><?php echo Lang::txt('JDETAILS'); ?></a></li>
						<li><a href="#page-locations" id="locations" class="active"><?php echo Lang::txt('COM_TOOLS_LOCATIONS'); ?></a></li>
					</ul>
					<div class="clr"></div>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</nav><!-- / .sub-navigation -->

	<div id="zone-document">
		<div id="page-locations" class="tab">
			<p>--</p>
		</div>
		<div class="clr"></div>
	</div>
</form>

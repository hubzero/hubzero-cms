<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div id="submenu-box">
	<div class="submenu-box">
		<div class="submenu-pad">
			<ul id="submenu" class="configuration">
				<li><a href="#page-site" id="site" class="active"><?php echo Lang::txt('JSITE'); ?></a></li>
				<li><a href="#page-system" id="system"><?php echo Lang::txt('COM_CONFIG_SYSTEM'); ?></a></li>
				<li><a href="#page-server" id="server"><?php echo Lang::txt('COM_CONFIG_SERVER'); ?></a></li>
				<li><a href="#page-api" id="api"><?php echo Lang::txt('COM_CONFIG_API'); ?></a></li>
				<li><a href="#page-permissions" id="permissions"><?php echo Lang::txt('COM_CONFIG_PERMISSIONS'); ?></a></li>
				<li><a href="#page-filters" id="filters"><?php echo Lang::txt('COM_CONFIG_TEXT_FILTERS')?></a></li>
				<?php foreach ($this->others as $key => $data): ?>
					<li><a href="#page-<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo $key; ?></a></li>
				<?php endforeach; ?>
			</ul>
			<div class="clr"></div>
		</div>
	</div>
	<div class="clr"></div>
</div>

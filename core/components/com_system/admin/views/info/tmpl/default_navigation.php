<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div id="submenu-box">
	<div class="submenu-box">
		<div class="submenu-pad">
			<ul id="submenu" class="information">
				<li>
					<a href="#page-site" id="site" class="active"><?php echo Lang::txt('COM_SYSTEM_INFO_SYSTEM_INFORMATION'); ?></a>
				</li>
				<li>
					<a href="#page-phpsettings" id="phpsettings"><?php echo Lang::txt('COM_SYSTEM_INFO_PHP_SETTINGS'); ?></a>
				</li>
				<li>
					<a href="#page-config" id="config"><?php echo Lang::txt('COM_SYSTEM_INFO_CONFIGURATION_FILE'); ?></a>
				</li>
				<li>
					<a href="#page-directory" id="directory"><?php echo Lang::txt('COM_SYSTEM_INFO_DIRECTORY_PERMISSIONS'); ?></a>
				</li>
				<li>
					<a href="#page-phpinfo" id="phpinfo"><?php echo Lang::txt('COM_SYSTEM_INFO_PHP_INFORMATION'); ?></a>
				</li>
			</ul>
			<div class="clr"></div>
		</div>
	</div>
	<div class="clr"></div>
</div>

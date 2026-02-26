<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

// no direct access
defined('_HZEXEC_') or die();
?>
<div id="submenu-box">
    <div class="submenu-box">
        <div class="submenu-pad">
            <ul id="submenu" class="information">
                <li>
                    <?php $sysInfoLabel = Lang::txt('COM_SYSTEM_INFO_SYSTEM_INFORMATION'); ?>
                    <a href="#page-site" id="site" class="active"><?php echo $sysInfoLabel; ?></a>
                </li>
                <li>
                    <?php $phpSettingsLabel = Lang::txt('COM_SYSTEM_INFO_PHP_SETTINGS'); ?>
                    <a href="#page-phpsettings" id="phpsettings"><?php echo $phpSettingsLabel; ?></a>
                </li>
                <li>
                    <?php $configLabel = Lang::txt('COM_SYSTEM_INFO_CONFIGURATION_FILE'); ?>
                    <a href="#page-config" id="config"><?php echo $configLabel; ?></a>
                </li>
                <li>
                    <?php $dirPermsLabel = Lang::txt('COM_SYSTEM_INFO_DIRECTORY_PERMISSIONS'); ?>
                    <a href="#page-directory" id="directory"><?php echo $dirPermsLabel; ?></a>
                </li>
                <li>
                    <?php $phpInfoLabel = Lang::txt('COM_SYSTEM_INFO_PHP_INFORMATION'); ?>
                    <a href="#page-phpinfo" id="phpinfo"><?php echo $phpInfoLabel; ?></a>
                </li>
            </ul>
            <div class="clr"></div>
        </div>
    </div>
    <div class="clr"></div>
</div>

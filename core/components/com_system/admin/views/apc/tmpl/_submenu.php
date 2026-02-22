<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$task = Request::getCmd('task');

$baseUrl = 'index.php?option=' . $this->option
    . '&controller=' . $this->controller;
$hostUrl = Route::url($baseUrl);
$systemUrl = Route::url($baseUrl . '&task=system');
$userUrl = Route::url($baseUrl . '&task=user');
$dirUrl = Route::url($baseUrl . '&task=dircache');
$versionUrl = Route::url($baseUrl . '&task=version');

$hostLabel = Lang::txt('COM_SYSTEM_APC_MENU_HOST');
$sysLabel = Lang::txt('COM_SYSTEM_APC_MENU_SYSTEM');
$userLabel = Lang::txt('COM_SYSTEM_APC_MENU_USER');
$dirLabel = Lang::txt('COM_SYSTEM_APC_MENU_DIR');
$verLabel = Lang::txt('COM_SYSTEM_APC_MENU_VERSION');

$hostActive = (!$task || $task == 'host') ? ' class="active"' : '';
$sysActive = ($task == 'system') ? ' class="active"' : '';
$userActive = ($task == 'user') ? ' class="active"' : '';
$dirActive = ($task == 'dircache') ? ' class="active"' : '';
$verActive = ($task == 'version') ? ' class="active"' : '';
?>

<div role="navigation" class="sub sub-navigation">
    <ul id="subsubmenu">
        <li>
            <a href="<?php echo $hostUrl; ?>"<?php echo $hostActive; ?>>
                <?php echo $hostLabel; ?>
            </a>
        </li>
        <li>
            <a href="<?php echo $systemUrl; ?>"<?php echo $sysActive; ?>>
                <?php echo $sysLabel; ?>
            </a>
        </li>
        <li>
            <a href="<?php echo $userUrl; ?>"<?php echo $userActive; ?>>
                <?php echo $userLabel; ?>
            </a>
        </li>
        <li>
            <a href="<?php echo $dirUrl; ?>"<?php echo $dirActive; ?>>
                <?php echo $dirLabel; ?>
            </a>
        </li>
        <li>
            <a href="<?php echo $versionUrl; ?>"<?php echo $verActive; ?>>
                <?php echo $verLabel; ?>
            </a>
        </li>
    </ul>
</div>

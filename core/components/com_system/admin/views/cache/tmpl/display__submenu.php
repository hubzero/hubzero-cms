<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

$task = Request::getCmd('task');

$baseUrl = 'index.php?option=' . $this->option
    . '&controller=' . $this->controller;
$overviewUrl = Route::url($baseUrl);
$opcacheUrl = Route::url($baseUrl . '&task=opcache');
$apcuUrl = Route::url($baseUrl . '&task=apcu');

$overviewLabel = Lang::txt('COM_SYSTEM_CACHE_MENU_OVERVIEW');
$opcacheLabel = Lang::txt('COM_SYSTEM_CACHE_MENU_OPCACHE');
$apcuLabel = Lang::txt('COM_SYSTEM_CACHE_MENU_APCU');

$overviewActive = (!$task || $task == 'display') ? ' class="active"' : '';
$opcacheActive = ($task == 'opcache') ? ' class="active"' : '';
$apcuActive = ($task == 'apcu') ? ' class="active"' : '';
?>

<div role="navigation" class="sub sub-navigation">
    <ul id="subsubmenu">
        <li>
            <a href="<?php echo $overviewUrl; ?>"<?php echo $overviewActive; ?>>
                <?php echo $overviewLabel; ?>
            </a>
        </li>
        <li>
            <a href="<?php echo $opcacheUrl; ?>"<?php echo $opcacheActive; ?>>
                <?php echo $opcacheLabel; ?>
            </a>
        </li>
        <li>
            <a href="<?php echo $apcuUrl; ?>"<?php echo $apcuActive; ?>>
                <?php echo $apcuLabel; ?>
            </a>
        </li>
    </ul>
</div>

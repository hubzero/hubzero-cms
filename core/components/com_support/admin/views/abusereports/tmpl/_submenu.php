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
$reportsUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
$checkUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller . '&task=check'
);
$reportsClass = ($task != 'check') ? ' class="active"' : '';
$checkClass = ($task == 'check') ? ' class="active"' : '';
?>

<div role="navigation" class="sub sub-navigation">
    <ul id="subsubmenu">
        <li>
            <a href="<?php echo $reportsUrl; ?>"<?php echo $reportsClass; ?>>
                <?php echo Lang::txt('COM_SUPPORT_ABUSE_REPORTS'); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo $checkUrl; ?>"<?php echo $checkClass; ?>>
                <?php echo Lang::txt('COM_SUPPORT_ABUSE_CHECK'); ?>
            </a>
        </li>
    </ul>
</div>
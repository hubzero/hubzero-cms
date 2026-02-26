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

$task = strtolower(Request::getCmd('task', ''));
$baseUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
$sessionsUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=sessions'
);
$usageUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=usage'
);
$appsLabel = Lang::txt('COM_TOOLS_WINDOWS_APPS');
$sessionsLabel = Lang::txt('COM_TOOLS_WINDOWS_SESSIONS');
$usageLabel = Lang::txt('COM_TOOLS_WINDOWS_USAGE');
$appsClass = !$task ? ' class="active"' : '';
$sessionsClass = $task == 'sessions' ? ' class="active"' : '';
$usageClass = $task == 'usage' ? ' class="active"' : '';
?>
<nav role="navigation" class="sub sub-navigation">
    <ul>
        <li>
            <a<?php echo $appsClass; ?>
                href="<?php echo $baseUrl; ?>"
            ><?php echo $appsLabel; ?></a>
        </li>
        <li>
            <a<?php echo $sessionsClass; ?>
                href="<?php echo $sessionsUrl; ?>"
            ><?php echo $sessionsLabel; ?></a>
        </li>
        <li>
            <a<?php echo $usageClass; ?>
                href="<?php echo $usageUrl; ?>"
            ><?php echo $usageLabel; ?></a>
        </li>
    </ul>
</nav><!-- / .sub-navigation -->

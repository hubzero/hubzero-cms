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
$classesUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=classes'
);
$activeLabel = Lang::txt('COM_TOOLS_SESSION_ACTIVE');
$classesLabel = Lang::txt('COM_TOOLS_SESSION_CLASSES');
$activeClass = !$task ? ' class="active"' : '';
$classesClass = $task == 'classes' ? ' class="active"' : '';
?>
<nav role="navigation" class="sub sub-navigation">
    <ul>
        <li>
            <a<?php echo $activeClass; ?>
                href="<?php echo $baseUrl; ?>"
            ><?php echo $activeLabel; ?></a>
        </li>
        <li>
            <a<?php echo $classesClass; ?>
                href="<?php echo $classesUrl; ?>"
            ><?php echo $classesLabel; ?></a>
        </li>
    </ul>
</nav><!-- / .sub-navigation -->
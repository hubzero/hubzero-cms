<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$task = strtolower(Request::getCmd('task', ''));
?>
<nav role="navigation" class="sub sub-navigation">
    <ul>
        <li>
            <?php $cls = (!$task) ? ' class="active"' : ''; ?>
            <?php $url = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
            ); ?>
            <a<?php echo $cls; ?> href="<?php echo $url; ?>"><?php
                echo Lang::txt('COM_MEMBERS_QUOTAS');
            ?></a>
        </li>
        <li>
            <?php $cls = ($task == 'displayclasses') ? ' class="active"' : ''; ?>
            <?php $href = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=displayClasses'
            ); ?>
            <a<?php echo $cls; ?> href="<?php echo $href; ?>"><?php
                echo Lang::txt('COM_MEMBERS_QUOTA_CLASSES');
            ?></a>
        </li>
        <li>
            <?php $cls = ($task == 'import') ? ' class="active"' : ''; ?>
            <?php $href = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=import'
            ); ?>
            <a<?php echo $cls; ?> href="<?php echo $href; ?>"><?php
                echo Lang::txt('COM_MEMBERS_QUOTAS_IMPORT');
            ?></a>
        </li>
    </ul>
</nav><!-- / .sub-navigation -->
<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$controller = Request::getCmd('controller', 'registration');
?>
<nav role="navigation" class="sub sub-navigation">
    <ul>
        <li>
            <?php $cls = ($controller == 'registration') ? ' class="active"' : ''; ?>
            <?php $url = Route::url('index.php?option=com_members&controller=registration'); ?>
            <a<?php echo $cls; ?> href="<?php echo $url; ?>"><?php
                echo Lang::txt('COM_MEMBERS_REGISTRATION_CONFIG');
            ?></a>
        </li>
        <li>
            <?php $cls = ($controller == 'incremental') ? ' class="active"' : ''; ?>
            <?php $url = Route::url('index.php?option=com_members&controller=incremental'); ?>
            <a<?php echo $cls; ?> href="<?php echo $url; ?>"><?php
                echo Lang::txt('COM_MEMBERS_INCREMENTAL');
            ?></a>
        </li>
        <li>
            <?php $cls = ($controller == 'premis') ? ' class="active"' : ''; ?>
            <?php $url = Route::url('index.php?option=com_members&controller=premis'); ?>
            <a<?php echo $cls; ?> href="<?php echo $url; ?>"><?php
                echo Lang::txt('COM_MEMBERS_PREMIS');
            ?></a>
        </li>
    </ul>
</nav><!-- / .sub-navigation -->
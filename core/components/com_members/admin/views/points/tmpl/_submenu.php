<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$task = Request::getCmd('task', '');
?>
<nav role="navigation" class="sub sub-navigation">
    <ul>
        <li>
            <?php $cls = (!$task) ? ' class="active"' : ''; ?>
            <?php $url = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
            ); ?>
            <a<?php echo $cls; ?> href="<?php echo $url; ?>">Summary</a>
        </li>
        <li>
            <?php $cls = ($task == 'edit') ? ' class="active"' : ''; ?>
            <?php $href = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=edit'
            ); ?>
            <a<?php echo $cls; ?> href="<?php echo $href; ?>">Look up User Balance</a>
        </li>
        <li>
            <?php $cls = ($task == 'config') ? ' class="active"' : ''; ?>
            <?php $href = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=config'
            ); ?>
            <a<?php echo $cls; ?> href="<?php echo $href; ?>">Configuration</a>
        </li>
        <li>
            <?php $cls = ($task == 'batch') ? ' class="active"' : ''; ?>
            <?php $href = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=batch'
            ); ?>
            <a<?php echo $cls; ?> href="<?php echo $href; ?>">Batch Transaction</a>
        </li>
    </ul>
</nav><!-- / .sub-navigation -->
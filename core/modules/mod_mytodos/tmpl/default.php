<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// no direct access
defined('_HZEXEC_') or die();

?>
<div<?php echo ($this->params->get('moduleclass')) ? ' class="' . $this->params->get('moduleclass') . '"' : ''; ?>>
    <h4>
        <a href="<?php echo Route::url('index.php?option=com_projects'); ?>">
            <?php echo Lang::txt('MOD_MYTODOS_ASSIGNED'); ?>
        </a>
    </h4>
    <?php if (count($this->rows) <= 0) { ?>
        <p><em><?php echo Lang::txt('MOD_MYTODOS_NO_TODOS'); ?></em></p>
    <?php } else { ?>
        <ul class="expandedlist">
            <?php
            foreach ($this->rows as $row) {
                ?>
                <li class="todos">
                    <?php
                    $todoUrl = Route::url('index.php?option=com_projects&alias='
                        . $row->alias . '&active=todo/view/?todoid=' . $row->id);
                    $projUrl = Route::url('index.php?option=com_projects&alias='
                        . $row->alias . '&active=todo');
                    ?>
                    <a href="<?php echo $todoUrl; ?>"><?php
                        echo $this->escape(stripslashes($row->content));
                    ?></a><br />
                    <?php echo Lang::txt('MOD_MYTODOS_PROJECT'); ?>:
                    <a href="<?php echo $projUrl; ?>"><?php
                        echo $this->escape(stripslashes($row->title));
                    ?></a>
                    <span></span>
                </li>
                <?php
            }
            ?>
        </ul>
    <?php } ?>
</div>

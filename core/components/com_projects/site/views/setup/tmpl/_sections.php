<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

?>
<ul id="panelist">
    <?php foreach ($this->sections as $section) : ?>
        <?php if ($section != 'info') : ?>
            <li <?php if ($section == $this->section) {
                echo 'class="activepane"';
                } ?>>
                <?php
                $routeUrl = Route::url(
                    'index
    . php?option='
                    . $this->option
                    . '&task=edit&alias='
                    . $this->model->get('alias')
                    . '&active='
                    . strtolower($section)
                );
                ?>
                <a href="<?php echo $routeUrl; ?>">
                    <?php echo Lang::txt('COM_PROJECTS_EDIT_PROJECT_PANE_' . strtoupper($section)); ?>
                </a>
            </li>
        <?php else : ?>
            <li <?php if ($this->section == 'info' || $this->section == 'info_custom') {
                echo 'class="activepane"';
                } ?>>
                <?php
                $routeUrl2 = Route::url(
                    'index
    . php?option='
                    . $this->option
                    . '&task=edit&alias='
                    . $this->model->get('alias')
                    . '&active='
                    . strtolower($section)
                );
                ?>
                <a href="<?php echo $routeUrl2; ?>">
                    <?php echo Lang::txt('COM_PROJECTS_EDIT_PROJECT_PANE_' . strtoupper($section)); ?>
                </a>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>

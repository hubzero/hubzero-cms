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
    <div id="pimage" class="pimage">
        <?php
        $projectUrl = Route::url(
            'index.php?option=' . $this->option
            . '&alias=' . $this->model->get('alias')
        );
        $linkTitle = $this->escape($this->model->get('title'))
            . ' - ' . Lang::txt('COM_PROJECTS_VIEW_UPDATES');
        ?>
        <a
            href="<?php echo $projectUrl; ?>"
            title="<?php echo $linkTitle; ?>"
        >
            <img src="<?php echo $this->model->picture('master');  ?>
                " alt="<?php echo $this->escape($this->model->get('title')); ?>" />
        </a>
    </div>


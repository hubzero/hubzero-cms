<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();
?>
        <p class="item-asset">
            <span class="asset-file">
                <?php echo $this->escape(stripslashes($this->asset->get('filename'))); ?>
            </span>
            <span class="asset-description">
                <?php
                $deleteUrl = Route::url(
                    'index.php?option=' . $this->option
                    . '&controller=' . $this->controller
                    . '&task=delete&asset=' . $this->asset->get('id')
                    . '&no_html=' . $this->no_html
                );
                $deleteTitle = Lang::txt('COM_SUPPORT_DELETE');
                ?>
                <a
                    class="icon-delete delete"
                    data-id="<?php echo $this->asset->get('id'); ?>"
                    href="<?php echo $deleteUrl; ?>"
                    title="<?php echo $deleteTitle; ?>"
                >
                    <?php echo Lang::txt('COM_SUPPORT_DELETE'); ?>
                </a>
            </span>
        </p>
<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

if ($this->data) { ?>
    <div id="sponsors" class="container">
        <h3><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_HEADER'); ?></h3>
        <div class="plg-content">
            <?php echo $this->data; ?>
        </div>
    </div>
<?php }
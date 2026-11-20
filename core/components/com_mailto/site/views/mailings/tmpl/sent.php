<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

// no direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View $this
 */
?>
<div>
    <div class="align-right">
        <a href="javascript: void window.close()">
            <?php echo Lang::txt('COM_MAILTO_CLOSE_WINDOW'); ?>
            <img src="<?php echo $this->img('close-x.png'); ?>" alt="" />
        </a>
    </div>

    <h2>
        <?php echo Lang::txt('COM_MAILTO_EMAIL_SENT'); ?>
    </h2>
</div>

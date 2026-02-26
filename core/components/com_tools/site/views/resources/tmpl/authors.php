<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

// No direct access.
defined('_HZEXEC_') or die();
?>
<div class="explaination">
    <h4><?php echo Lang::txt('COM_TOOLS_AUTHORS_NO_LOGIN'); ?></h4>
    <p><?php echo Lang::txt('COM_TOOLS_AUTHORS_NO_LOGIN_EXPLANATION'); ?></p>
</div>
<fieldset>
    <legend><?php echo Lang::txt('COM_TOOLS_AUTHORS_AUTHORS'); ?></legend>
    <div class="field-wrap">
        <?php
        $authorsSrc = 'index.php?option=' . $this->option
            . '&amp;controller=authors&amp;rid=' . $this->row->id
            . '&amp;tmpl=component&amp;version=' . $this->version;
        ?>
        <iframe
            name="authors"
            id="authors"
            src="<?php echo $authorsSrc; ?>"
            width="100%"
            height="400"
            frameborder="0"
        ></iframe>
    </div>
</fieldset><div class="clear"></div>
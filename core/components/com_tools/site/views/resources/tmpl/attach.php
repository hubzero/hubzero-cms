<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

// No direct access.
defined('_HZEXEC_') or die();

$allowupload = ($this->version == 'current' or !$this->status['published']) ? 1 : 0;
?>
    <div class="explaination">
        <h4><?php echo Lang::txt('COM_TOOLS_ATTACH_WHAT_ARE_ATTACHMENTS'); ?></h4>
        <p><?php echo Lang::txt('COM_TOOLS_ATTACH_EXPLANATION'); ?></p>
    </div>
    <fieldset>
        <legend><?php echo Lang::txt('COM_TOOLS_ATTACH_ATTACHMENTS'); ?></legend>
        <div class="field-wrap">
            <?php
            $attachesSrc = 'index.php?option=' . $this->option
                . '&amp;controller=attachments&amp;rid=' . $this->row->id
                . '&amp;tmpl=component&amp;type=7'
                . '&amp;allowupload=' . $allowupload;
            ?>
            <iframe
                width="100%"
                height="200"
                frameborder="0"
                name="attaches"
                id="attaches"
                src="<?php echo $attachesSrc; ?>"
            ></iframe>
        </div>
    </fieldset><div class="clear"></div>

    <div class="explaination">
        <h4><?php echo Lang::txt('COM_TOOLS_ATTACH_WHAT_ARE_SCREENSHOTS'); ?></h4>
        <p><?php echo Lang::txt('COM_TOOLS_ATTACH_SCREENSHOTS_EXPLANATION'); ?></p>
    </div>
    <fieldset>
        <legend><?php echo Lang::txt('COM_TOOLS_ATTACH_SCREENSHOTS'); ?></legend>
        <div class="field-wrap">
            <?php
            $screensSrc = 'index.php?option=' . $this->option
                . '&amp;controller=screenshots&amp;rid=' . $this->row->id
                . '&amp;tmpl=component&amp;version=' . $this->version;
            ?>
            <iframe
                width="100%"
                height="400"
                frameborder="0"
                name="screens"
                id="screens"
                src="<?php echo $screensSrc; ?>"
            ></iframe>
        </div>
    </fieldset><div class="clear"></div>
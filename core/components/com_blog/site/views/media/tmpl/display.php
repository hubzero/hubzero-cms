<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;

// No direct access
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(true), '/');

$this->css();
?>
<div id="attachments">
    <?php
    $formAction = $base
        . '/index.php?option=' . $this->option
        . '&amp;tmpl=component&amp;controller=' . $this->controller
        . '&amp;task=upload';
    ?>
    <form action="<?php echo $formAction; ?>"
        id="adminForm"
        method="post"
        enctype="multipart/form-data"
    >
        <fieldset>
            <div id="themanager" class="manager">
                <?php
                $iframeSrc = $base
                    . '/index.php?option=' . $this->option
                    . '&amp;tmpl=component&amp;controller=' . $this->controller
                    . '&amp;task=list'
                    . '&amp;scope=' . urlencode($this->archive->get('scope'))
                    . '&amp;id=' . $this->archive->get('scope_id');
                ?>
                <iframe src="<?php echo $iframeSrc; ?>"
                    name="imgManager"
                    id="imgManager"
                    width="98%"
                    height="180"
                ></iframe>
            </div>
        </fieldset>
        <fieldset>
            <p><input type="file" class="form-control-file" name="upload" id="upload" /></p>
            <p><input type="submit" class="btn" value="<?php echo Lang::txt('COM_BLOG_UPLOAD'); ?>" /></p>

            <input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
            <input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>" />
            <input type="hidden" name="task" value="upload" />
            <input type="hidden" name="scope" value="<?php echo $this->escape($this->archive->get('scope')); ?>" />
            <input type="hidden" name="id" value="<?php echo $this->escape($this->archive->get('scope_id')); ?>" />
            <input type="hidden" name="tmpl" value="component" />

            <?php echo Html::input('token'); ?>
        </fieldset>
    </form>

    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo $this->getError(); ?></p>
    <?php } ?>
</div>

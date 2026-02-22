<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$base = rtrim(Request::base(true), '/');
?>
<div class="col span-half">
    <?php
    $uploadAction = 'index.php?option=com_resources'
        . '&amp;no_html=1&amp;controller=attachments'
        . '&amp;task=save&amp;pid=' . $this->id;
    $uploadList = 'index.php?option=com_resources'
        . '&amp;no_html=1&amp;controller=attachments'
        . '&amp;pid=' . $this->id;
    ?>
    <div id="ajax-uploader"
         data-action="<?php echo $uploadAction; ?>"
         data-list="<?php echo $uploadList; ?>"
         data-instructions="Click or drop file">
    </div>
</div><!-- / .col span-half -->
<div class="col span-half omega">
    <?php
    $linkAction = 'index.php?option=com_resources'
        . '&amp;controller=attachments&amp;no_html=1'
        . '&amp;task=create&amp;pid=' . $this->id
        . '&amp;url=';
    $linkList = 'index.php?option=com_resources'
        . '&amp;controller=attachments&amp;no_html=1'
        . '&amp;pid=' . $this->id;
    ?>
    <div id="link-adder"
         data-action="<?php echo $linkAction; ?>"
         data-list="<?php echo $linkList; ?>">
    </div>
</div><!-- / .col span-half omega -->

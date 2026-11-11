<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Request;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{id: mixed} $this
 */

$base = rtrim(Request::base(true), '/');
?>
<div class="col span-half">
    <?php
    $dataActionUrl = 'index.php?option=com_resources&amp;no_html=1&amp;controller=attachments&amp;task=save&amp;pid='
        . $this->id . '&amp;' . Session::getFormToken() . '=1';
    ?>
    <div id="ajax-uploader" data-action="<?= $dataActionUrl ?>"
        data-list="index.php?option=com_resources&amp;no_html=1&amp;controller=attachments&amp;pid=<?= $this->id ?>"
        data-instructions="Click or drop file">
    </div>
</div><!-- / .col span-half -->
<div class="col span-half omega">
    <?php
    $dataActionUrl = 'index.php?option=com_resources&amp;controller=attachments&amp;no_html=1&amp;task=create&amp;pid='
        . $this->id . '&amp;' . Session::getFormToken() . '=1&amp;url=';
    ?>
    <div id="link-adder" data-action="<?= $dataActionUrl ?>"
        data-list="index.php?option=com_resources&amp;controller=attachments&amp;no_html=1&amp;pid=<?= $this->id ?>">
    </div>
</div><!-- / .col span-half omega -->

<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$default = User::getInstance(0)->picture(0, false);

$picture = $this->profile->picture(0, false);
?>
<div id="ajax-upload-container">
    <?php $formAction = Route::url('index.php?option=' . $this->option); ?>
    <form action="<?php echo $formAction; ?>" method="post" enctype="multipart/form-data">
        <h2><?php echo Lang::txt('Upload a New Profile Picture'); ?></h2>
        <div class="grid">
            <div class="col span6" id="ajax-upload-left">
                <img
                    id="picture-src"
                    src="<?php echo $picture; ?>"
                    alt=""
                    data-default-pic="<?php echo $this->escape($default); ?>"/>
                <?php if ($this->profile->picture() != $default) : ?>
                    <?php $removeTxt = Lang::txt('[Remove Picture]'); ?>
                    <?php
                    $removeHref = Request::base(true)
                        . '/index.php?option=' . $this->option
                        . '&amp;controller=' . $this->controller
                        . '&amp;id=' . $this->profile->get('id')
                        . '&amp;task=delete&amp;no_html=1&amp;'
                        . Session::getFormToken() . '=1';
                    ?>
                    <a href="<?php echo $removeHref; ?>" id="remove-picture"><?php echo $removeTxt; ?></a>
                <?php endif; ?>
            </div><!-- /#ajax-upload-left -->
            <div class="col span6 omega" id="ajax-upload-right">
                <?php
                $uploadAction = Request::base(true)
                    . '/index.php?option=' . $this->option
                    . '&amp;controller=' . $this->controller
                    . '&amp;id=' . $this->profile->get('id')
                    . '&amp;task=doajaxupload&amp;no_html=1&amp;'
                    . Session::getFormToken() . '=1';
                ?>
                <div id="ajax-uploader" data-action="<?php echo $uploadAction; ?>"></div>
            </div><!-- /#ajax-upload-right -->
        </div>

        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="task" value="ajaxuploadsave" />
        <input type="hidden" name="id" value="<?php echo $this->profile->get('id'); ?>" />
        <input type="hidden" name="no_html" value="1" />

        <?php echo Html::input('token'); ?>
    </form>
</div><!-- /#ajax-upload-container -->
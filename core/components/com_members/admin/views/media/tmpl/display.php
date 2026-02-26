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

// No direct access
defined('_HZEXEC_') or die();

$this->css('
hr {
	margin: 0;
}
.align-center {
	text-align: center;
}
.img-wrap {
	display: block;
}
.img-wrap img {
	border: 1px solid #ccc;
}
.img-preview {
	position: relative;
}
.img-preview .delete {
	position: absolute;
	top: 1em;
	right: 1em;
	display: block;
	width: 1em;
	height: 1em;
	font-size: 1.2em;
	line-height: 1;
	overflow: hidden;
	border: none;
	color: #bbb;
	font-style: normal;
}
.img-preview .delete:hover {
	color: red;
}
.icon-trash:before {
	content: "\f014";
	font-family: Fontcons;
	margin-right: 0.5em;
}
');
?>
<div id="media">
    <?php $formAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>
    <form action="<?php echo $formAction; ?>" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
        <fieldset>
            <p>
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
                <input type="hidden" name="tmpl" value="<?php echo Request::getCmd('tmpl'); ?>" />
                <input type="hidden" name="id" value="<?php echo $this->profile->get('id'); ?>" />
                <input type="hidden" name="task" value="upload" />
                <?php echo Html::input('token'); ?>

                <?php $val = Lang::txt('COM_MEMBERS_MEDIA_WILL_REPLACE_EXISTING_IMAGE'); ?>
                <label for="image"><?php echo Lang::txt('COM_MEMBERS_MEDIA_UPLOAD'); ?> <?php echo $val; ?></label>
                <input type="file" name="upload" id="upload" size="17" />&nbsp;&nbsp;&nbsp;
                <input type="submit" value="<?php echo Lang::txt('COM_MEMBERS_MEDIA_UPLOAD'); ?>" />
            </p>

            <?php
            if ($this->getError()) {
                echo '<p class="error">' . $this->getError() . '</p>';
            }
            ?>
            <hr />

            <div class="img-preview">
                <?php
                $removeUrl = Route::url(
                    'index.php?option=' . $this->option . '&controller=' . $this->controller
                    . '&task=remove&id=' . $this->profile->get('id') . '&file=profile.png&'
                    . Session::getFormToken() . '=1&tmpl=' . Request::getCmd('tmpl')
                );
                ?>
                <?php $text = Lang::txt('JACTION_DELETE'); ?>
                <a class="icon-trash delete" href="<?php echo $removeUrl; ?>"><?php echo $text; ?></a>

                <p class="input-wrap align-center">
                    <span class="img-dimensions">50 x 50</span><br />
                    <span class="img-wrap"><img
                        src="<?php echo $this->profile->picture(0, true); ?>"
                        alt="<?php echo Lang::txt('COM_MEMBERS_MEDIA_PICTURE'); ?>"
                        width="50"
                        height="50"
                        id="memberthumb"/></span>
                </p>
                <p class="input-wrap align-center">
                    <span class="img-dimensions">200 x 200</span><br />
                    <span class="img-wrap"><img
                        src="<?php echo $this->profile->picture(0, false); ?>"
                        alt="<?php echo Lang::txt('COM_MEMBERS_MEDIA_PICTURE'); ?>"
                        width="200"
                        height="200"
                        id="conimage"/></span>
                </p>
            </div>
        </fieldset>
    </form>
</div>

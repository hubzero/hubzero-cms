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

Html::behavior('modal');

// push scripts and styles
$this->css()
     ->css('media.css')
     ->js()
     ->js('groups.mediabrowser')
     ->js('jquery.fileuploader', 'system')
     ->js('jquery.contextMenu', 'system')
     ->css('jquery.contextMenu.css', 'system');

//get request vars
$type          = Request::getWord('type', '', 'get');
$ckeditor      = Request::getString('CKEditor', '', 'get');
$ckeditorFunc  = Request::getInt('CKEditorFuncNum', 0, 'get');
$ckeditorQuery = '&type=' . $type . '&CKEditor=' . $ckeditor . '&CKEditorFuncNum=' . $ckeditorFunc;
?>

<div class="upload-browser cf">
    <?php
    foreach ($this->notifications as $notification) {
        echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
    }
    ?>

    <div class="upload-browser-col left">
        <div class="toolbar cf">
            <div class="title"><?php echo Lang::txt('COM_GROUPS_MEDIA_GROUP_FILES'); ?></div>
            <?php if ($this->authorized) : ?>
                <div class="buttons">
                    <?php $val = Route::url(
                        'index.php?option=com_groups&cn=' .
                            $this->group->get('cn') .
                            '&controller=media&task=addfolder&tmpl=component'
                    ); ?>
                    <a href="<?php echo $val; ?>" class="icon-add action-addfolder"></a>
                </div>
            <?php endif; ?>
        </div>
        <div class="foldertree" data-activefolder="<?php echo $this->activeFolder; ?>">
            <?php echo $this->folderTree; ?>
        </div>
        <div class="foldertree-list">
            <?php echo $this->folderList; ?>
        </div>
        <?php $url = Route::url('index.php?option=' . $this->option); ?>
        <form action="<?php echo $url; ?>" method="post" enctype="multipart/form-data" class="upload-browser-uploader">
            <fieldset>
                <?php $val1 = Lang::txt('Click or drop file'); ?>
                <?php $val = Route::url(
                    'index.php?option=com_groups&cn=' .
                        $this->group->get('cn') .
                        '&controller=media&task=ajaxupload&no_html=1&' .
                        Session::getFormToken() .
                        '=1'
                ); ?>
                <div id="ajax-uploader" data-instructions="<?php echo $val1; ?>" data-action="<?php echo $val; ?>">
                    <noscript>
                        <p><input type="file" name="upload" id="upload" /></p>
                        <p><input type="submit" value="<?php echo Lang::txt('UPLOAD'); ?>" /></p>
                    </noscript>
                </div>
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="controller" value="media" />
                <input type="hidden" name="task" value="upload" />
                <input
                    type="hidden"
                    name="listdir"
                    id="listdir"
                    value="<?php echo $this->group->get('gidNumber'); ?>" />
                <input type="hidden" name="tmpl" value="component" />
                <?php echo Html::input('token'); ?>
            </fieldset>
        </form>
    </div>
    <div class="upload-browser-col right">
        <?php $val = Route::url(
            'index.php?option=com_groups&cn=' .
                $this->group->get('cn') .
                '&controller=media&task=listfiles&tmpl=component&type=' .
                $ckeditorQuery
        ); ?>
        <iframe class="upload-browser-filelist-iframe" src="<?php echo $val; ?>"></iframe>
    </div>
</div>
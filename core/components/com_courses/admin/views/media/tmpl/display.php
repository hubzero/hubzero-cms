<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('framework', true);

if ($this->getError()) {
    echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>';
}

$this->css('uploader')
    ->js('jquery.fileuploader.js', 'system')
    ->js('media.js');
?>

<?php $routeUrl = Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>
<form action="<?php echo $routeUrl; ?>" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
    <fieldset>
        <legend>
            <?php
            $uploadPath = DS . trim($this->config->get('uploadpath', '/site/courses'), DS)
                . DS . $this->course_id . DS . $this->listdir;
            ?>
            <span>
                <?php echo Lang::txt('COM_COURSES_FILES'); ?> -
                <?php echo $uploadPath; ?>
                <?php echo $this->dirPath; ?>
            </span>
        </legend>
        <div id="ajax-uploader-before">&nbsp;</div>
        <?php
            $routeUrl = Route::url(
                'index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=upload&course='
                . $this->course_id . '&listdir=' . $this->listdir . '&no_html=1&' . Session::getFormToken() . '=1'
            );
            ?>
        <div
            id="ajax-uploader"
            data-action="<?php echo $routeUrl; ?>"
            data-instructions="<?php echo Lang::txt('COM_COURSES_UPLOAD_CLICK_OR_DROP'); ?>">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <input type="file" name="upload" id="upload" />
                        </td>
                        <td>
                            <input
                                type="checkbox"
                                name="batch"
                                id="batch"
                                <?php $txt = Lang::txt('Unpack (.zip, .tar, etc)'); ?>
                                value="1" /> <label for="batch"><?php echo $txt; ?></label>
                        </td>
                        <td>
                            <input type="submit" value="<?php echo Lang::txt('COM_COURSES_UPLOAD'); ?>" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="themanager" class="manager">
            <?php
                $routeUrl = Route::url(
                    'index.php?option=' . $this->option  . '&controller=' . $this->controller
                    . '&task=list&tmpl=component&listdir=' . $this->listdir . '&subdir=' . $this->subdir
                    . '&course=' . $this->course_id
                );
                ?>
            <iframe src="<?php echo $routeUrl; ?>" name="imgManager" id="imgManager" width="98%" height="150"
                <?php
                    $routeUrl = Route::url(
                        'index.php?option=' . $this->option  . '&controller=' . $this->controller
                        . '&task=list&tmpl=component&course=' . $this->course_id
                    );
                    ?>
                data-dir="<?php echo $routeUrl; ?>"></iframe>
        </div>

        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="listdir" id="listdir" value="<?php echo $this->listdir; ?>" />
        <input type="hidden" name="course" value="<?php echo $this->course_id; ?>" />
        <input type="hidden" name="task" value="upload" />
    </fieldset>

    <?php echo Html::input('token'); ?>
</form>

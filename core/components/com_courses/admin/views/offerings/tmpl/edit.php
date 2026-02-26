<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;
use Hubzero\Facades\Event;

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

$canDo = \Components\Courses\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_OFFERING') . ': ' . $text, 'courses');
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('offering');

$base = str_replace('/administrator', '', Request::base(true));

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js('jquery.fileuploader.js', 'system')
    ->js();
$this->css();
?>
<?php if ($this->getError()) { ?>
    <p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<?php $routeUrl = Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>
<form
    action="<?php echo $routeUrl; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

                <input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
                <input type="hidden" name="fields[course_id]" value="<?php echo $this->row->get('course_id'); ?>" />
                <input type="hidden" name="course" value="<?php echo $this->row->get('course_id'); ?>" />
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
                <input type="hidden" name="task" value="save" />

                <div class="input-wrap">
                    <label for="field-title">
                        <?php $val = Lang::txt('COM_COURSES_FIELD_TITLE'); ?>
                        <?php echo $val; ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                    </label><br />
                    <input
                        type="text"
                        name="fields[title]"
                        id="field-title"
                        class="required"
                        value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
                </div>
                <div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?>">
                    <label for="field-alias"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS'); ?>:</label><br />
                    <input
                        type="text"
                        name="fields[alias]"
                        id="field-alias"
                        value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
                    <span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?></span>
                </div>
            </fieldset>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_PUBLISHING'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-state">
                        <?php $val = Lang::txt('COM_COURSES_FIELD_STATE'); ?>
                        <?php echo $val; ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                    </label><br />
                    <select name="fields[state]" id="field-state">
                        <option value="0"<?php if ($this->row->get('state') == 0) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('COM_COURSES_UNPUBLISHED'); ?></option>
                        <option value="1"<?php if ($this->row->get('state') == 1) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('COM_COURSES_PUBLISHED'); ?></option>
                        <option value="2"<?php if ($this->row->get('state') == 2) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('COM_COURSES_TRASHED'); ?></option>
                    </select>
                </div>

                <p><?php echo Lang::txt('COM_COURSES_OFFERING_START_END_HINT'); ?></p>

                <div class="grid">
                    <div class="col span6">
                        <div class="input-wrap" data-hint="YYYY-MM-DD HH:mm:ss">
                            <label for="publish_up"><?php echo Lang::txt('COM_COURSES_FIELD_STARTS'); ?>:</label><br />
                            <?php
                                $pubUp = ($this->row->get('publish_up')
                                    && $this->row->get('publish_up') != '0000-00-00 00:00:00')
                                    ? $this->row->get('publish_up') : '';
                                echo Html::input(
                                    'calendar',
                                    'fields[publish_up]',
                                    $pubUp,
                                    array('id' => 'publish_up')
                                );
                                ?>
                            <span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_STARTS_HINT'); ?></span>
                        </div>
                    </div>
                    <div class="col span6">
                        <div class="input-wrap" data-hint="YYYY-MM-DD HH:mm:ss">
                            <label for="publish_down"><?php echo Lang::txt('COM_COURSES_FIELD_ENDS'); ?>:</label><br />
                            <?php
                                $pubDown = ($this->row->get('publish_down')
                                    && $this->row->get('publish_down') != '0000-00-00 00:00:00')
                                    ? $this->row->get('publish_down') : '';
                                echo Html::input(
                                    'calendar',
                                    'fields[publish_down]',
                                    $pubDown,
                                    array('id' => 'publish_down')
                                );
                                ?>
                            <span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_ENDS_HINT'); ?></span>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <table class="meta">
                <tbody>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_COURSE_ID'); ?></th>
                        <td><?php echo $this->escape($this->row->get('course_id')); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_OFFERING_ID'); ?></th>
                        <td><?php echo $this->escape($this->row->get('id')); ?></td>
                    </tr>
                    <?php if ($this->row->get('created')) { ?>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_CREATED'); ?></th>
                            <?php $timeVal = $this->escape($this->row->get('created')); ?>
                            <?php $timeVal2 = $this->escape(Date::of($this->row->get('created'))->toLocal()); ?>
                            <td><time datetime="<?php echo $timeVal; ?>"><?php echo $timeVal2; ?></time></td>
                        </tr>
                    <?php } ?>
                    <?php if ($this->row->get('created_by')) { ?>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_CREATOR'); ?></th>
                            <td><?php
                            $creator = User::getInstance($this->row->get('created_by'));
                            echo $this->escape(stripslashes($creator->get('name'))); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_COURSES_LOGO'); ?></span></legend>

                <?php
                if ($this->row->exists()) {
                    $logo = $this->row->params('logo');
                    ?>
                    <div class="uploader-wrap">
                        <?php
                            $routeUrl = Route::url(
                                'index.php?option=' . $this->option  . '&controller=logo&task=upload&type=offering&id='
                                . $this->row->get('id') . '&no_html=1&' . Session::getFormToken() . '=1'
                            );
                        ?>
                        <div
                            id="ajax-uploader"
                            data-action="<?php echo $routeUrl; ?>"
                            data-instructions="<?php echo Lang::txt('COM_COURSES_UPLOAD_CLICK_OR_DROP'); ?>">
                            <noscript>
                                <?php
                                    $routeUrl = Route::url(
                                        'index.php?option=' . $this->option  . '&controller=logo&tmpl=component&file='
                                        . $logo . '&type=offering&id=' . $this->row->get('id')
                                    );
                                ?>
                                <iframe
                                    width="100%"
                                    height="350"
                                    name="filer"
                                    id="filer"
                                    frameborder="0"
                                    src="<?php echo $routeUrl; ?>"></iframe>
                            </noscript>
                        </div>
                    </div>
                        <?php
                        $width  = 0;
                        $height = 0;
                        $fsize  = 0;

                        $pic  = 'blank.png';
                        $path = '/core/components/com_courses/admin/assets/img';

                        if ($logo) {
                            $pathl = substr(PATH_APP, strlen(PATH_ROOT)) . $this->row->logo('path');

                            if (file_exists(PATH_ROOT . $pathl . DS . $logo)) {
                                $fsize = filesize(PATH_ROOT . $pathl . DS . $logo);
                                list($width, $height, $type, $attr) = getimagesize(PATH_ROOT . $pathl . DS . $logo);
                                $pic  = $logo;
                                $path = $pathl;
                            } else {
                                $logo = null;
                            }
                        }
                        ?>
                        <div id="img-container">
                            <img
                                id="img-display"
                                src="<?php echo '..' . $path . DS . $pic; ?>"
                                alt="<?php echo Lang::txt('COM_COURSES_LOGO'); ?>" />
                            <input
                                type="hidden"
                                name="currentfile"
                                id="currentfile"
                                value="<?php echo $this->escape($logo); ?>" />
                        </div>
                        <table class="formed">
                            <tbody>
                                <tr>
                                    <th><?php echo Lang::txt('COM_COURSES_FILE'); ?>:</th>
                                    <td>
                                        <span id="img-name">
                                            <?php
                                                $picName = ($pic && $pic != 'blank.png')
                                                    ? $pic
                                                    : Lang::txt('COM_COURSES_NONE');
                                                echo $picName;
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                            $routeUrl = Route::url(
                                                'index.php?option=' . $this->option
                                                . '&controller=logo&tmpl=component&task=remove&currentfile=' . $logo
                                                . '&type=offering&id=' . $this->row->get('id') . '&'
                                                . Session::getFormToken() . '=1'
                                            );
                                        ?>
                                        <?php
                                            $defaultImg = "'../core/components"
                                                . "/com_courses/admin/assets"
                                                . "/img/blank.png'";
                                        ?>
                                        <a
                                            id="img-delete"
                                            <?php echo $logo ? '' : 'class="hide"'; ?>
                                            href="<?php echo $routeUrl; ?>"
                                            title="<?php echo Lang::txt('COM_COURSES_DELETE'); ?>"
                                            data-defaultimg="<?php echo $defaultImg; ?>"
                                        >
                                            [ x ]
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php echo Lang::txt('COM_COURSES_PICTURE_SIZE'); ?>:</th>
                                    <?php $val = \Hubzero\Utility\Number::formatBytes($fsize); ?>
                                    <td><span id="img-size"><?php echo $val; ?></span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th><?php echo Lang::txt('COM_COURSES_PICTURE_WIDTH'); ?>:</th>
                                    <td><span id="img-width"><?php echo $width; ?></span> px</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th><?php echo Lang::txt('COM_COURSES_PICTURE_HEIGHT'); ?>:</th>
                                    <td><span id="img-height"><?php echo $height; ?></span> px</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php
                } else {
                    echo '<p class="warning">' . Lang::txt('COM_COURSES_PICTURE_ADDED_LATER') . '</p>';
                }
                ?>
            </fieldset>

            <?php $params = new \Hubzero\Config\Registry($this->row->get('params')); ?>

            <fieldset class="adminform offeringparams">
                <legend><?php echo Lang::txt('COM_COURSES_FIELDSET_PARAMS'); ?></legend>
                <div class="input-wrap">
                    <?php $txt = Lang::txt('COM_COURSES_PROGRESS_CALCULATION'); ?>
                    <label for="params-progress-calculation"><?php echo $txt; ?>:</label><br />
                    <?php
                        $pc = $params->get('progress_calculation', '');
                    ?>
                    <select name="params[progress_calculation]" id="params-progress-calculation">
                        <?php $sel = ($pc == '') ? 'selected="selected"' : ''; ?>
                        <option value=""<?php echo $sel; ?>>
                            <?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_INHERIT'); ?>
                        </option>
                        <?php $sel = ($pc == 'all') ? 'selected="selected"' : ''; ?>
                        <option value="all"<?php echo $sel; ?>>
                            <?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_ALL'); ?>
                        </option>
                        <?php $sel = ($pc == 'graded') ? 'selected="selected"' : ''; ?>
                        <option value="graded"<?php echo $sel; ?>>
                            <?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_GRADED'); ?>
                        </option>
                        <?php $sel = ($pc == 'videos') ? 'selected="selected"' : ''; ?>
                        <option value="videos"<?php echo $sel; ?>>
                            <?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_VIDEOS'); ?>
                        </option>
                        <?php $sel = ($pc == 'manual') ? 'selected="selected"' : ''; ?>
                        <option value="manual"<?php echo $sel; ?>>
                            <?php echo Lang::txt('COM_COURSES_PROGRESS_CALCULATION_MANUAL'); ?>
                        </option>
                    </select>
                </div>
            </fieldset>

            <?php
            if ($plugins = Event::trigger('courses.onOfferingEdit')) {
                $data = $this->row->get('params');

                foreach ($plugins as $plugin) {
                    $param = new \Hubzero\Html\Parameter(
                        (is_object($data) ? $data->toString() : $data),
                        PATH_CORE . DS . 'plugins' . DS . 'courses' . DS . $plugin['name'] . DS . $plugin['name']
                            . '.xml'
                    );
                    $out = $param->render('params', 'onOfferingEdit');
                    if (!$out) {
                        continue;
                    }
                    ?>
                        <fieldset class="adminform eventparams" id="params-<?php echo $plugin['name']; ?>">
                            <?php $txt = Lang::txt('COM_COURSES_FIELDSET_PARAMETERS', $plugin['title']); ?>
                            <legend><?php echo $txt; ?></legend>
                        <?php echo $out; ?>
                        </fieldset>
                        <?php
                }
            }
            ?>
        </div>
    </div>

    <?php echo Html::input('token'); ?>
</form>
